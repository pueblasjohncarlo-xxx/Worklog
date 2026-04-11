<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View; // Add this line

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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'encrypted_password' => Crypt::encryptString($request->password),
            'role' => $role,
            'section' => $request->section,
            'department' => $request->department,
            'is_approved' => false,
            'status' => 'pending',
            'has_requested_account' => true,
        ]);

        return redirect()->route('login')->with('status', 'Your account has been created and is pending admin approval.');
    }
}
