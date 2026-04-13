<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'in:student,supervisor,coordinator,ojt_adviser'],
            'section' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:255'],
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
            'section' => $request->section,
            'department' => $request->department,
            'has_requested_account' => true,
        ];

        // Registration no longer requires admin approval. Keep checks for schema compatibility.
        if (Schema::hasColumn('users', 'is_approved')) {
            $userData['is_approved'] = true;
        }

        if (Schema::hasColumn('users', 'status')) {
            $userData['status'] = 'approved';
        }

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Your account has been created successfully.');
    }
}
