<x-guest-layout>
    <div class="rounded-3xl bg-purple-900/60 border border-purple-500/30 shadow-2xl backdrop-blur-md px-8 py-10 space-y-8">
        <div class="text-center space-y-2">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-400 to-indigo-500 shadow-lg">
                <span class="text-lg font-extrabold text-white">W</span>
            </div>
            <div class="text-xl font-semibold text-white tracking-tight">
                Create WorkLog account
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}" class="space-y-5" x-data="{ role: '{{ old('role', 'student') }}' }">
            @csrf

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
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                >
                    <option value="student">
                        Student
                    </option>
                    <option value="supervisor">
                        Supervisor
                    </option>
                    <option value="coordinator">
                        Coordinator
                    </option>
                    <option value="ojt_adviser">
                        OJT Adviser
                    </option>
                </select>
            </div>

            <!-- Student Specific Fields -->
            <div class="grid grid-cols-2 gap-4" x-show="role === 'student'" x-transition>
                <div class="space-y-1">
                    <label for="section" class="block text-sm font-medium text-purple-100">
                        Course/Section
                    </label>
                    <input
                        id="section"
                        name="section"
                        type="text"
                        value="{{ old('section') }}"
                        :required="role === 'student'"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                        placeholder="e.g. BSIT-4A"
                    >
                </div>
                <div class="space-y-1">
                    <label for="department" class="block text-sm font-medium text-purple-100">
                        Major
                    </label>
                    <input
                        id="department"
                        name="department"
                        type="text"
                        value="{{ old('department') }}"
                        :required="role === 'student'"
                        class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                        placeholder="e.g. Computer Tech"
                    >
                </div>
            </div>

            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-purple-100">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
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
