<x-guest-layout>
    <div class="rounded-3xl bg-white border border-gray-100 shadow-2xl backdrop-blur-md px-8 py-10 space-y-8">
        <div class="text-center space-y-2">
            <x-wl-brand-logo class="mx-auto" />
        </div>

        @if (session('status'))
            <div class="mb-2 text-sm text-emerald-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-2 text-sm text-rose-600">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-800 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all px-3 py-2 text-base"
                    placeholder="email@example.com"
                >
            </div>

            <div class="space-y-1" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        :type="show ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-800 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all px-3 py-2 text-base pr-10"
                        placeholder="••••••••"
                    >
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer focus:outline-none">
                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.577-2.977M7.618 7.618A5.96 5.96 0 0112 6c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.577 2.977M8 11a4 4 0 014-4m4 4a4 4 0 01-4 4m0 0l-4 4m4-4l4 4" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <label for="remember_me" class="ml-2 block text-sm font-medium text-gray-600">
                        {{ __('Remember me') }}
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    {{ __('Sign in') }}
                </button>
            </div>

            <div class="text-center mt-4">
                @if (Route::has('register'))
                    <p class="text-sm text-gray-600">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-bold transition-colors ml-1">
                            {{ __('Create Account') }}
                        </a>
                    </p>
                @endif
            </div>
        </form>
    </div>
</x-guest-layout>
