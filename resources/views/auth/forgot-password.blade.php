<x-guest-layout>
    <div class="guest-auth-card rounded-3xl px-8 py-10 space-y-6">
        <div class="text-center space-y-2">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-400 to-indigo-500 shadow-lg">
                <span class="text-lg font-extrabold text-white">W</span>
            </div>
            <div class="guest-auth-title text-xl font-semibold tracking-tight">
                Reset password
            </div>
        </div>

        <div class="guest-auth-body mb-2 text-sm text-center">
            {{ __('Forgot your password? No problem. Enter your email and we will send you a reset link.') }}
        </div>

        <x-auth-session-status class="mb-2 text-emerald-300" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium text-purple-100">
                    {{ __('Email') }}
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="mt-1 block w-full rounded-xl border-0 bg-purple-950/70 text-purple-50 placeholder-purple-300/70 shadow-inner focus:ring-2 focus:ring-purple-400 focus:outline-none px-3 py-2 text-sm"
                    placeholder="email@example.com"
                >
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
            </div>

            <div class="flex items-center justify-end pt-2">
                <button
                    type="submit"
                    class="inline-flex justify-center items-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-400 to-indigo-500 text-xs font-semibold text-white tracking-wide shadow-lg hover:from-purple-300 hover:to-indigo-400 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-offset-2 focus:ring-offset-purple-900"
                >
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
