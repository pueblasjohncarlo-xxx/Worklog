<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $path = $request->file('photo')->store('profile-photos', 'public');
            $request->user()->profile_photo_path = $path;
        }

        $request->user()->save();

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
}
