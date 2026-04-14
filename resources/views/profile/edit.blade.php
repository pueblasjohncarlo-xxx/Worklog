@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Personal Information Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-900 dark:text-gray-100">
                <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Email -->
                    <div class="border-l-4 border-indigo-500 pl-4">
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Email</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                    </div>

                    <!-- Full Name -->
                    @if($user->firstname || $user->lastname)
                        <div class="border-l-4 border-indigo-400 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Full Name</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->firstname }} {{ $user->lastname }}</p>
                        </div>
                    @endif

                    <!-- Middle Name -->
                    @if($user->middlename)
                        <div class="border-l-4 border-indigo-300 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Middle Name</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->middlename }}</p>
                        </div>
                    @endif

                    <!-- Department -->
                    @if($user->department)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Department</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->department }}</p>
                        </div>
                    @endif

                    <!-- Section -->
                    @if($user->section)
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Section</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->section }}</p>
                        </div>
                    @endif

                    <!-- Role -->
                    <div class="border-l-4 border-purple-500 pl-4">
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Role</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                    </div>

                    <!-- Age -->
                    @if($user->age)
                        <div class="border-l-4 border-yellow-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Age</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->age }} years</p>
                        </div>
                    @endif

                    <!-- Gender -->
                    @if($user->gender)
                        <div class="border-l-4 border-pink-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Gender</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $user->gender }}</p>
                        </div>
                    @endif

                    <!-- Joined Date -->
                    @if($user->created_at)
                        <div class="border-l-4 border-orange-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Joined</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M d, Y · h:i A') }}</p>
                        </div>
                    @endif

                    <!-- Last Login -->
                    @if($user->last_login_at)
                        <div class="border-l-4 border-red-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Last Login</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->last_login_at->format('M d, Y · h:i A') }}</p>
                        </div>
                    @endif

                    <!-- Email Verified -->
                    @if($user->email_verified_at)
                        <div class="border-l-4 border-cyan-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Email Verified</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->email_verified_at->format('M d, Y · h:i A') }}</p>
                        </div>
                    @endif

                    <!-- Account Requested -->
                    <div class="border-l-4 border-violet-600 pl-4">
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Account Requested</p>
                        @if($user->has_requested_account)
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-sm font-bold">✓ Yes</span>
                        @else
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 rounded-full text-sm font-bold">✗ No</span>
                        @endif
                    </div>

                    <!-- Account Status -->
                    <div class="border-l-4 border-green-600 pl-4">
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Account Status</p>
                        @if($user->is_approved)
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full text-sm font-bold">✓ Approved</span>
                        @else
                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 rounded-full text-sm font-bold">⏳ Pending</span>
                        @endif
                    </div>

                    <!-- Profile Photo Path -->
                    @if($user->profile_photo_path)
                        <div class="border-l-4 border-teal-500 pl-4 md:col-span-2">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Profile Photo</p>
                            <div class="mt-2 flex items-center gap-4">
                                <img src="{{ $user->profile_photo_url }}" data-avatar-user-id="{{ $user->id }}" alt="Profile Photo" class="w-16 h-16 rounded-full object-cover border-2 border-teal-300">
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ basename($user->profile_photo_path) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student Assignment Section -->
        @if($user->role === 'student' && $currentAssignment)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Current Assignment
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Company</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->company?->name ?? 'N/A' }}</p>
                        </div>

                        @if($currentAssignment->company?->industry)
                            <div class="border-l-4 border-purple-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Industry</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->company->industry }}</p>
                            </div>
                        @endif

                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Required Hours</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $requiredHours }} hrs</p>
                        </div>

                        <div class="border-l-4 border-yellow-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Completed Hours</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($approvedHours, 1) }} hrs</p>
                        </div>

                        @php
                            $progress = $requiredHours > 0 ? min(100, ($approvedHours / $requiredHours) * 100) : 0;
                        @endphp

                        <div class="md:col-span-2 border-l-4 border-indigo-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-3">Progress: {{ number_format($progress, 1) }}%</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        @if($currentAssignment->start_date)
                            <div class="border-l-4 border-cyan-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Start Date</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->start_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($currentAssignment->end_date)
                            <div class="border-l-4 border-red-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">End Date</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->end_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        @if($currentAssignment->status)
                            <div class="border-l-4 border-orange-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Status</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $currentAssignment->status }}</p>
                            </div>
                        @endif

                        @isset($currentAssignment->supervisor)
                            <div class="border-l-4 border-pink-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Supervisor</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->supervisor->name }}</p>
                            </div>
                        @endisset

                        @isset($currentAssignment->ojtAdviser)
                            <div class="border-l-4 border-teal-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">OJT Adviser</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $currentAssignment->ojtAdviser->name }}</p>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        @endif

        <!-- Supervisor Profile Section -->
        @if($user->role === 'supervisor' && $user->supervisorProfile)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zm-2-8a1 1 0 11-2 0 1 1 0 012 0zM14 15a4 4 0 00-8 0v2h8v-2zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 12a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                        Supervisor Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        @if($user->supervisorProfile->company_id)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Company</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->supervisorProfile->company?->name ?? 'N/A' }}</p>
                            </div>
                        @endif

                        <div class="border-l-4 border-indigo-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Students Supervised</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $supervisorAssignments }}</p>
                        </div>

                        @if($user->supervisorProfile->phone)
                            <div class="border-l-4 border-green-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->supervisorProfile->phone }}</p>
                            </div>
                        @endif

                        @if($user->supervisorProfile->position)
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Position</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->supervisorProfile->position }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Coordinator Profile Section -->
        @if($user->role === 'coordinator' && $user->coordinatorProfile)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v6h16V5a2 2 0 00-2-2H5zm16 8H3v5a2 2 0 002 2h14a2 2 0 002-2v-5z"></path></svg>
                        Coordinator Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Students Coordinated</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $coordinatorAssignments }}</p>
                        </div>

                        @if($user->coordinatorProfile->office_location)
                            <div class="border-l-4 border-green-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Office Location</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->coordinatorProfile->office_location }}</p>
                            </div>
                        @endif

                        @if($user->coordinatorProfile->phone)
                            <div class="border-l-4 border-purple-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->coordinatorProfile->phone }}</p>
                            </div>
                        @endif

                        @if($user->coordinatorProfile->department)
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Department</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->coordinatorProfile->department }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- OJT Adviser Profile Section -->
        @if($user->role === 'ojt_adviser' && $user->ojtAdviserProfile)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-10-3v3m5-3v3m-8-1.5h13m-13 6h13m-13 3h13"></path></svg>
                        OJT Adviser Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        @if($user->ojtAdviserProfile->phone)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->ojtAdviserProfile->phone }}</p>
                            </div>
                        @endif

                        @if($user->ojtAdviserProfile->office_location)
                            <div class="border-l-4 border-green-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Office Location</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->ojtAdviserProfile->office_location }}</p>
                            </div>
                        @endif

                        @if($user->ojtAdviserProfile->specialization)
                            <div class="border-l-4 border-purple-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Specialization</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->ojtAdviserProfile->specialization }}</p>
                            </div>
                        @endif

                        @if($user->ojtAdviserProfile->department)
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Department</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->ojtAdviserProfile->department }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Update Profile Information -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if (session('status') === 'profile-updated')
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.WorkLogAvatarSync && typeof window.WorkLogAvatarSync.broadcast === 'function') {
            window.WorkLogAvatarSync.broadcast();
            window.WorkLogAvatarSync.refresh();
        }
    });
</script>
@endif
@endpush
