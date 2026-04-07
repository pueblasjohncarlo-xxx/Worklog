<x-admin-layout>
    <x-slot name="header">
        User Details
    </x-slot>

    <div class="space-y-6">
        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-3xl border-4 border-white dark:border-gray-700 shadow-lg">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">{{ $user->role }}</span>
                            @if($user->role === 'student')
                                @if(!$user->has_requested_account)
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        Imported
                                    </span>
                                @elseif(!$user->is_approved)
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    System User
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    @if($user->role === 'student' && $user->has_requested_account && !$user->is_approved)
                        <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Approve
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Back
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Profile & Contact -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">Profile Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Email Address</label>
                                <div class="text-base text-gray-900 dark:text-white font-medium break-all">{{ $user->email }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Date Joined</label>
                                <div class="text-base text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('F d, Y') }}</div>
                            </div>
                            
                            @if($user->role === 'student')
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Section</label>
                                    <div class="text-base text-gray-900 dark:text-white font-medium">{{ $user->section ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Department</label>
                                    <div class="text-base text-gray-900 dark:text-white font-medium">{{ $user->department ?? 'N/A' }}</div>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Last Login</label>
                                <div class="text-base text-gray-900 dark:text-white font-medium">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment Information (Student Only) -->
                @if($user->role === 'student')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">Current Assignment</h3>
                        @php
                            $assignment = $user->studentAssignments()->where('status', 'active')->first();
                        @endphp
                        
                        @if($assignment)
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-xs text-indigo-500 dark:text-indigo-400 uppercase font-bold tracking-wider">Company</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $assignment->company->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-indigo-500 dark:text-indigo-400 uppercase font-bold tracking-wider">Supervisor</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $assignment->supervisor->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-indigo-500 dark:text-indigo-400 uppercase font-bold tracking-wider">Start Date</div>
                                        <div class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $assignment->start_date ? $assignment->start_date->format('M d, Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 text-center">
                                <p class="text-gray-500 dark:text-gray-400 italic">No active assignment found.</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Security & Actions -->
            <div class="space-y-6">
                <!-- Security Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Security</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Password</label>
                                
                                @if($user->encrypted_password)
                                    <div x-data="{ show: false }" class="relative">
                                        <div class="flex">
                                            <input 
                                                :type="show ? 'text' : 'password'" 
                                                value="{{ \Illuminate\Support\Facades\Crypt::decryptString($user->encrypted_password) }}" 
                                                readonly 
                                                class="block w-full rounded-l-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm sm:text-sm focus:ring-0 focus:border-gray-300"
                                            >
                                            <button 
                                                type="button" 
                                                @click="show = !show" 
                                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-300 cursor-pointer"
                                            >
                                                <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.275 5.275C6.938 4.464 9.32 4 12 4c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.127 2.22m-1.92 1.92a9.96 9.96 0 01-3.66 1.76M9.172 9.172a4 4 0 015.656 5.656" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-green-600 font-medium flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Decrypted
                                        </p>
                                    </div>
                                @elseif($user->role === 'student' && !$user->has_requested_account)
                                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md border border-gray-200 dark:border-gray-700">
                                        <div class="font-mono text-sm text-gray-800 dark:text-gray-200">{{ strtolower($user->lastname) . '123' }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Default Password</div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md border border-gray-200 dark:border-gray-700">
                                        <div class="font-mono text-gray-400 text-sm">•••••••••••••</div>
                                        <div class="text-xs text-gray-400 mt-1">Encrypted (Cannot View)</div>
                                    </div>
                                @endif
                            </div>

                            <div x-data="{ showReset: false }">
                                <button @click="showReset = true" type="button" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 focus:outline-none focus:border-indigo-300 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Reset Password
                                </button>

                                <!-- Reset Password Modal -->
                                <div x-show="showReset" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="showReset = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700 relative z-50">
                                            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                                                @csrf
                                                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                                        Reset Password
                                                    </h3>
                                                    <div class="space-y-4">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            Enter a new password for <strong>{{ $user->name }}</strong>.
                                                        </p>
                                                        <div>
                                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                                            <input type="password" name="password" id="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        </div>
                                                        <div>
                                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                        Reset Password
                                                    </button>
                                                    <button type="button" @click="showReset = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-red-100 dark:border-red-900/30">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-red-600 dark:text-red-400">Danger Zone</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Permanently delete this user and all associated data. This action cannot be undone.
                        </p>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Delete User Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
