<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['studentProfile', 'supervisorProfile', 'coordinatorProfile', 'ojtAdviserProfile']);
        $settings = $this->resolveSettings($request);
        
        $profileData = [
            'user' => $user,
            'currentAssignment' => null,
            'approvedHours' => 0,
            'requiredHours' => 0,
            'supervisorAssignments' => [],
            'coordinatorAssignments' => [],
            'settings' => $settings,
            'recentNotifications' => $user->notifications()->latest()->take(6)->get(),
            'unreadNotificationsCount' => $user->unreadNotifications()->count(),
            'activityLogs' => AuditLog::query()
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
        ];

        // Load role-specific data for students
        if ($user->role === 'student' && $user->studentAssignments()->exists()) {
            $profileData['currentAssignment'] = $user->studentAssignments()
                ->with(['company', 'supervisor', 'coordinator', 'ojtAdviser'])
                ->first();
            
            if ($profileData['currentAssignment']) {
                $profileData['approvedHours'] = $profileData['currentAssignment']->totalApprovedHours();
                $profileData['requiredHours'] = $profileData['currentAssignment']->required_hours ?? 0;
            }
        }

        // Load role-specific data for supervisors
        if ($user->role === 'supervisor') {
            $profileData['supervisorAssignments'] = $user->supervisorAssignments()->count() ?? 0;
        }

        // Load role-specific data for coordinators
        if ($user->role === 'coordinator') {
            $profileData['coordinatorAssignments'] = $user->coordinatorAssignments()->count() ?? 0;
        }

        return view('profile.edit', $profileData);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user()->loadMissing([
            'studentProfile',
            'supervisorProfile',
            'coordinatorProfile',
            'ojtAdviserProfile',
        ]);

        $validated = $request->validated();
        $structuredName = trim(implode(' ', array_filter([
            trim((string) ($validated['firstname'] ?? '')),
            trim((string) ($validated['middlename'] ?? '')),
            trim((string) ($validated['lastname'] ?? '')),
        ])));

        DB::transaction(function () use ($request, $user, $validated, $structuredName) {
            $user->fill([
                'name' => $structuredName !== '' ? $structuredName : $validated['name'],
                'firstname' => $validated['firstname'] ?? null,
                'middlename' => $validated['middlename'] ?? null,
                'lastname' => $validated['lastname'] ?? null,
                'age' => $validated['age'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'email' => $validated['email'],
                'section' => $validated['section'] ?? null,
                'department' => $validated['department'] ?? null,
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($request->hasFile('photo')) {
                $oldPhotoPath = $user->getRawOriginal('profile_photo_path');
                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $path;

                if ($oldPhotoPath && $oldPhotoPath !== $path) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            }

            $user->save();

            if ($user->role === User::ROLE_STUDENT) {
                $user->studentProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_number' => $validated['student_number'] ?? null,
                        'program' => $validated['program'] ?? null,
                        'year_level' => $validated['year_level'] ?? null,
                        'phone' => $validated['student_phone'] ?? null,
                        'date_of_birth' => $validated['date_of_birth'] ?? null,
                    ]
                );
            }

            if ($user->role === User::ROLE_SUPERVISOR) {
                $user->supervisorProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $validated['supervisor_phone'] ?? null,
                        'position_title' => $validated['position_title'] ?? null,
                        'department' => $validated['supervisor_department'] ?? null,
                    ]
                );
            }

            if ($user->role === User::ROLE_COORDINATOR) {
                $user->coordinatorProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $validated['coordinator_phone'] ?? null,
                        'department' => $validated['coordinator_department'] ?? null,
                    ]
                );
            }

            if ($user->role === User::ROLE_OJT_ADVISER) {
                $user->ojtAdviserProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $validated['ojt_adviser_phone'] ?? null,
                        'department' => $validated['ojt_adviser_department'] ?? null,
                        'address' => $validated['ojt_adviser_address'] ?? null,
                    ]
                );
            }
        });

        $user->refresh()->loadMissing([
            'studentProfile',
            'supervisorProfile',
            'coordinatorProfile',
            'ojtAdviserProfile',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'profile-updated',
                'profile' => $this->buildProfileSyncPayload($user),
            ]);
        }

        return Redirect::route('settings.index')->with('status', 'profile-updated');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();
        $section = (string) $request->input('section', 'preferences');

        abort_unless(in_array($section, ['privacy', 'notifications', 'preferences', 'blocking'], true), 404);

        $validated = $request->validate([
            'section' => ['required', 'string'],
            'privacy.profile_visibility' => ['nullable', 'string', 'in:role_only,organization,public'],
            'privacy.show_email' => ['nullable', 'boolean'],
            'privacy.show_activity_status' => ['nullable', 'boolean'],
            'privacy.allow_profile_indexing' => ['nullable', 'boolean'],
            'notifications.email_updates' => ['nullable', 'boolean'],
            'notifications.browser_alerts' => ['nullable', 'boolean'],
            'notifications.deadline_alerts' => ['nullable', 'boolean'],
            'notifications.approval_alerts' => ['nullable', 'boolean'],
            'notifications.digest_frequency' => ['nullable', 'string', 'in:instant,daily,weekly'],
            'preferences.language' => ['nullable', 'string', 'max:10'],
            'preferences.theme' => ['nullable', 'string', 'in:system,light,dark'],
            'preferences.compact_mode' => ['nullable', 'boolean'],
            'preferences.start_page' => ['nullable', 'string', 'max:100'],
            'blocking.muted_keywords' => ['nullable', 'string', 'max:1000'],
            'blocking.hidden_people' => ['nullable', 'string', 'max:1000'],
        ]);

        $settings = $this->resolveSettings($request);
        $payload = $settings[$section] ?? [];

        foreach (($validated[$section] ?? []) as $key => $value) {
            $payload[$key] = $value;
        }

        if ($section === 'privacy') {
            foreach (['show_email', 'show_activity_status', 'allow_profile_indexing'] as $key) {
                $payload[$key] = $request->boolean("privacy.{$key}");
            }
        }

        if ($section === 'notifications') {
            foreach (['email_updates', 'browser_alerts', 'deadline_alerts', 'approval_alerts'] as $key) {
                $payload[$key] = $request->boolean("notifications.{$key}");
            }
        }

        if ($section === 'preferences') {
            $payload['compact_mode'] = $request->boolean('preferences.compact_mode');
            if (! empty($payload['language'])) {
                session(['locale' => $payload['language']]);
            }
        }

        $settings[$section] = $payload;
        $request->session()->put($this->settingsSessionKey($user->id), $settings);

        return Redirect::route('settings.index')
            ->with('settings-status', ucfirst($section).' settings updated.');
    }

    public function avatarVersions(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []));

        if ($ids->isEmpty()) {
            $idsParam = trim((string) $request->input('ids', ''));
            if ($idsParam !== '') {
                $ids = collect(explode(',', $idsParam));
            }
        }

        $userIds = $ids
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(200)
            ->values();

        if ($userIds->isEmpty()) {
            return response()->json([
                'avatars' => [],
                'generated_at' => now()->toIso8601String(),
            ]);
        }

        $users = User::whereIn('id', $userIds)->get(['id', 'name', 'email', 'profile_photo_path', 'updated_at']);

        $avatars = $users->mapWithKeys(function (User $user) {
            return [
                (string) $user->id => [
                    'url' => $user->profile_photo_url,
                    'name' => $user->name,
                    'email' => $user->email,
                    'updated_at' => optional($user->updated_at)->toIso8601String(),
                ],
            ];
        });

        return response()->json([
            'avatars' => $avatars,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function photo(User $user): BinaryFileResponse|Response
    {
        $disk = Storage::disk('public');
        $path = trim((string) $user->profile_photo_path);

        if ($path !== '' && $disk->exists($path)) {
            $absolutePath = $disk->path($path);
            $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

            return response()->file($absolutePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $name = trim((string) $user->name) !== '' ? (string) $user->name : 'WorkLog User';
        $initials = strtoupper((string) ($user->initials ?: 'WU'));
        $svg = $this->buildDefaultAvatarSvg($name, $initials);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function buildProfileSyncPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->profile_photo_url,
            'updated_at' => optional($user->updated_at)->toIso8601String(),
        ];
    }

    private function resolveSettings(Request $request): array
    {
        $user = $request->user();

        return array_replace_recursive(
            $this->defaultSettings($user),
            (array) $request->session()->get($this->settingsSessionKey($user->id), [])
        );
    }

    private function defaultSettings(User $user): array
    {
        return [
            'privacy' => [
                'profile_visibility' => 'role_only',
                'show_email' => true,
                'show_activity_status' => true,
                'allow_profile_indexing' => false,
            ],
            'notifications' => [
                'email_updates' => true,
                'browser_alerts' => true,
                'deadline_alerts' => true,
                'approval_alerts' => $user->role !== User::ROLE_STUDENT,
                'digest_frequency' => 'instant',
            ],
            'preferences' => [
                'language' => session('locale', app()->getLocale()),
                'theme' => 'system',
                'compact_mode' => false,
                'start_page' => $this->defaultStartPageForRole($user),
            ],
            'blocking' => [
                'muted_keywords' => '',
                'hidden_people' => '',
            ],
        ];
    }

    private function defaultStartPageForRole(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN, User::ROLE_STAFF => route('admin.dashboard'),
            User::ROLE_COORDINATOR => route('coordinator.dashboard'),
            User::ROLE_SUPERVISOR => route('supervisor.dashboard'),
            User::ROLE_STUDENT => route('student.dashboard'),
            User::ROLE_OJT_ADVISER => route('ojt_adviser.dashboard'),
            default => route('dashboard'),
        };
    }

    private function settingsSessionKey(int $userId): string
    {
        return "worklog.settings.user.{$userId}";
    }

    private function buildDefaultAvatarSvg(string $name, string $initials): string
    {
        $safeName = e($name);
        $safeInitials = e($initials);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256" role="img" aria-label="{$safeName}">
  <defs>
    <linearGradient id="worklogAvatarGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0ea5e9" />
      <stop offset="100%" stop-color="#4f46e5" />
    </linearGradient>
  </defs>
  <rect width="256" height="256" rx="48" fill="url(#worklogAvatarGradient)" />
  <circle cx="128" cy="104" r="46" fill="rgba(255,255,255,0.18)" />
  <path d="M58 216c14-36 42-54 70-54s56 18 70 54" fill="rgba(255,255,255,0.18)" />
  <text x="128" y="148" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="72" font-weight="700" fill="#ffffff">{$safeInitials}</text>
</svg>
SVG;
    }
}
