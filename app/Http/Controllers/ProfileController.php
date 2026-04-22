<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['studentProfile', 'supervisorProfile', 'coordinatorProfile', 'ojtAdviserProfile']);
        
        $profileData = [
            'user' => $user,
            'currentAssignment' => null,
            'approvedHours' => 0,
            'requiredHours' => 0,
            'supervisorAssignments' => [],
            'coordinatorAssignments' => [],
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

        DB::transaction(function () use ($request, $user, $validated) {
            $user->fill([
                'name' => $validated['name'],
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
                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
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

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

        $users = User::whereIn('id', $userIds)->get(['id', 'name', 'profile_photo_path', 'updated_at']);

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
}
