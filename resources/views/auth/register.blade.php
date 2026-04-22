<x-guest-layout>
    @php
        $studentSectionOptions = \App\Models\User::STUDENT_SECTIONS;
        $studentMajorOptions = \App\Models\User::STUDENT_MAJORS;
        $companyOptions = $companies ?? collect();
        $invitationData = $invitation ?? null;
        $invitedRole = $invitationData?->role;
        $invitedEmail = $invitationData?->email;
        $invitedCompanyId = $invitationData?->company_id;
    @endphp
    <div class="guest-auth-card rounded-3xl px-8 py-10 space-y-8">
        <div class="text-center space-y-2">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-400 to-indigo-500 shadow-lg">
                <span class="text-lg font-extrabold text-white">W</span>
            </div>
            <div class="guest-auth-title text-xl font-semibold tracking-tight">
                Create WorkLog account
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        @if (!empty($inviteError))
            <div class="mb-4 rounded-xl border border-amber-400/40 bg-amber-500/15 px-4 py-3 text-sm text-amber-100">
                {{ $inviteError }}
            </div>
        @endif

        @if ($invitationData)
            <div class="mb-4 rounded-xl border border-emerald-400/40 bg-emerald-500/15 px-4 py-3 text-sm text-emerald-100">
                You are registering through an invitation for <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $invitedRole)) }}</span>
                using <span class="font-semibold">{{ $invitedEmail }}</span>.
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ role: '{{ old('role', $invitedRole ?? 'student') }}' }">
            @csrf

            @if ($invitationData && !empty($inviteToken))
                <input type="hidden" name="invite_token" value="{{ $inviteToken }}">
            @endif

            <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-purple-100">
                    Name
                </label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                    placeholder="Your full name"
                >
            </div>

            <div class="space-y-1">
                <label for="role" class="block text-sm font-medium text-purple-100">
                    Role
                </label>
                <select
                    id="role"
                    name="role"
                    x-model="role"
                    @disabled($invitationData)
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                >
                    <option value="student">
                        Student
                    </option>
                    <option value="supervisor">
                        Supervisor
                    </option>
                    <option value="ojt_adviser">
                        OJT Adviser
                    </option>
                </select>
                @if ($invitationData)
                    <input type="hidden" name="role" value="{{ $invitedRole }}">
                @endif
            </div>

            <!-- Student Specific Fields -->
            <div class="grid grid-cols-2 gap-4" x-show="role === 'student'" x-transition>
                <div class="space-y-1">
                    <label for="section" class="block text-sm font-medium text-purple-100">
                        Course/Section
                    </label>
                    <select
                        id="section"
                        name="section"
                        :required="role === 'student'"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                    >
                        <option value="" class="text-gray-400">Select Course/Section</option>
                        @foreach ($studentSectionOptions as $sectionOption)
                            <option value="{{ $sectionOption }}" @selected(old('section') === $sectionOption)>
                                {{ $sectionOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="department" class="block text-sm font-medium text-purple-100">
                        Major
                    </label>
                    <select
                        id="department"
                        name="department"
                        :required="role === 'student'"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                    >
                        <option value="" class="text-gray-400">Select Major</option>
                        @foreach ($studentMajorOptions as $majorOption)
                            <option value="{{ $majorOption }}" @selected(old('department') === $majorOption)>
                                {{ $majorOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Supervisor Specific Field -->
            <div class="space-y-1" x-show="role === 'supervisor'" x-transition>
                <label for="company_id" class="block text-sm font-medium text-purple-100">
                    Company
                </label>
                <select
                    id="company_id"
                    name="company_id"
                    :required="role === 'supervisor'"
                    @disabled($invitationData && $invitedRole === \App\Models\User::ROLE_SUPERVISOR && !empty($invitedCompanyId))
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                >
                    <option value="">Select company</option>
                    @foreach ($companyOptions as $companyOption)
                        <option value="{{ $companyOption->id }}" @selected((string) old('company_id', $invitedCompanyId) === (string) $companyOption->id)>
                            {{ $companyOption->name }}
                        </option>
                    @endforeach
                </select>
                @if ($invitationData && $invitedRole === \App\Models\User::ROLE_SUPERVISOR && !empty($invitedCompanyId))
                    <input type="hidden" name="company_id" value="{{ $invitedCompanyId }}">
                @endif
                @if ($companyOptions->isEmpty())
                    <p class="text-xs text-amber-300">
                        No companies are available right now. Please contact the coordinator to create your supervisor account.
                    </p>
                @endif
            </div>

            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-purple-100">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $invitedEmail) }}"
                    required
                    @readonly($invitationData)
                    autocomplete="username"
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                    placeholder="email@example.com"
                >
            </div>

            <div class="space-y-1" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-purple-100">
                    Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        :type="show ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm pr-10"
                        placeholder="Choose a password"
                    >
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-purple-300 hover:text-purple-100 cursor-pointer focus:outline-none">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.577-2.977M7.618 7.618A5.96 5.96 0 0112 6c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.577 2.977M8 11a4 4 0 014-4m4 4a4 4 0 01-4 4m0 0l-4 4m4-4l4 4" /></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1" x-data="{ show: false }">
                <label for="password_confirmation" class="block text-sm font-medium text-purple-100">
                    Confirm Password
                </label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        :type="show ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm pr-10"
                        placeholder="Re-enter password"
                    >
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-purple-300 hover:text-purple-100 cursor-pointer focus:outline-none">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.577-2.977M7.618 7.618A5.96 5.96 0 0112 6c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.577 2.977M8 11a4 4 0 014-4m4 4a4 4 0 01-4 4m0 0l-4 4m4-4l4 4" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('login') }}"
                    class="text-xs text-purple-200 hover:text-white underline underline-offset-2"
                >
                    Already registered?
                </a>

                <button
                    type="submit"
                    class="inline-flex justify-center items-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-400 to-indigo-500 text-xs font-semibold text-white tracking-wide shadow-lg hover:from-purple-300 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-offset-2 focus:ring-offset-purple-900"
                >
                    Register
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
