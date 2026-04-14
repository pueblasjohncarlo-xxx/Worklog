<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RegistrationInvitation;
use App\Models\User;
use App\Notifications\RegistrationInvitationLinkNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user();

        $invitations = RegistrationInvitation::query()
            ->with(['invitedBy', 'company', 'revokedBy'])
            ->when($viewer->role !== User::ROLE_ADMIN, function ($query) use ($viewer) {
                $query->where('invited_by_user_id', $viewer->id);
            })
            ->latest()
            ->paginate(20);

        return view('invitations.index', [
            'layoutComponent' => $viewer->role === User::ROLE_ADMIN ? 'admin-layout' : 'coordinator-layout',
            'companies' => Company::orderBy('name')->get(),
            'invitations' => $invitations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'string', Rule::in([User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER])],
            'company_id' => ['required_if:role,'.User::ROLE_SUPERVISOR, 'nullable', 'integer', 'exists:companies,id'],
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
        ], [
            'company_id.required_if' => 'Please select a company for supervisor invitations.',
        ]);

        $email = strtolower(trim($validated['email']));

        if (User::where('email', $email)->exists()) {
            return back()->withInput()->withErrors([
                'email' => 'This email already has a registered account.',
            ]);
        }

        $activeInviteExists = RegistrationInvitation::query()
            ->where('email', $email)
            ->where('role', $validated['role'])
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($activeInviteExists) {
            return back()->withInput()->withErrors([
                'email' => 'An active invitation already exists for this email and role.',
            ]);
        }

        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);
        $expiresAt = now()->addHours((int) ($validated['expires_in_hours'] ?? 72));

        $invitation = RegistrationInvitation::create([
            'email' => $email,
            'role' => $validated['role'],
            'company_id' => $validated['role'] === User::ROLE_SUPERVISOR ? (int) $validated['company_id'] : null,
            'invited_by_user_id' => $request->user()->id,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'metadata' => [
                'source' => 'sidebar_invite',
            ],
        ]);

        $registerUrl = route('invitations.accept', ['token' => $token]);

        $warning = null;
        try {
            Notification::route('mail', $invitation->email)
                ->notify(new RegistrationInvitationLinkNotification(
                    inviterName: (string) $request->user()->name,
                    role: $invitation->role,
                    registerUrl: $registerUrl,
                    companyName: optional($invitation->company)->name,
                    expiresAt: $expiresAt,
                ));
        } catch (\Throwable $e) {
            report($e);
            $warning = 'Invitation created, but the email could not be sent. Share the link manually.';
        }

        return back()->with('status', 'Invitation created. Email dispatch completed for '.$invitation->email.'.')
            ->with('invite_link', $registerUrl)
            ->with('warning', $warning);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = RegistrationInvitation::query()
            ->where('token_hash', hash('sha256', trim($token)))
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            return redirect()->route('register')->withErrors([
                'email' => 'This invitation link is invalid, expired, or already used.',
            ]);
        }

        // If inviter is currently logged in and clicks the link for testing,
        // move to guest context so registration can continue immediately.
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('register', ['invite' => $token]);
    }

    public function revoke(Request $request, RegistrationInvitation $invitation): RedirectResponse
    {
        $viewer = $request->user();

        if ($viewer->role !== User::ROLE_ADMIN && $invitation->invited_by_user_id !== $viewer->id) {
            abort(403, 'You cannot revoke this invitation.');
        }

        if (! is_null($invitation->accepted_at)) {
            return back()->with('warning', 'This invitation is already accepted and can no longer be revoked.');
        }

        if (! is_null($invitation->revoked_at)) {
            return back()->with('warning', 'This invitation is already revoked.');
        }

        $invitation->update([
            'revoked_at' => now(),
            'revoked_by_user_id' => $viewer->id,
        ]);

        return back()->with('status', 'Invitation revoked successfully.');
    }
}
