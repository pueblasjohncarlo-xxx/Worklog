<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const STUDENT_SECTIONS = [
        'BSIT-4A',
        'BSIT-4B',
        'BSIT-4C',
        'BSIT-4D',
        'BSIT-4AE',
    ];

    private const STUDENT_MAJORS = [
        'Computer Technology',
        'Electronics Technology',
    ];

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'role' => $request->input('role', User::ROLE_STUDENT),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in([User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER])],
            'section' => ['required_if:role,'.User::ROLE_STUDENT, 'nullable', 'string', Rule::in(self::STUDENT_SECTIONS)],
            'department' => ['required_if:role,'.User::ROLE_STUDENT, 'nullable', 'string', Rule::in(self::STUDENT_MAJORS)],
        ]);

        $role = $validated['role'] ?? User::ROLE_STUDENT;
        $email = $request->email;
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        // Build user data with all required fields
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'encrypted_password' => Crypt::encryptString($request->password),
            'role' => $role,
            'section' => $role === User::ROLE_STUDENT ? ($validated['section'] ?? null) : null,
            'department' => $role === User::ROLE_STUDENT ? ($validated['department'] ?? null) : null,
            'has_requested_account' => true,
        ];

        // Publicly registered accounts require coordinator approval.
        if (Schema::hasColumn('users', 'is_approved')) {
            $userData['is_approved'] = false;
        }

        if (Schema::hasColumn('users', 'status')) {
            $userData['status'] = 'pending';
        }

        if (Schema::hasColumn('users', 'approved_at')) {
            $userData['approved_at'] = null;
        }

        if (Schema::hasColumn('users', 'rejected_at')) {
            $userData['rejected_at'] = null;
        }

        if (Schema::hasColumn('users', 'rejection_reason')) {
            $userData['rejection_reason'] = null;
        }

        $user = User::create($userData);

        return redirect()->route('login')->with('status', 'Your account has been created and is pending coordinator approval.');
    }
}
