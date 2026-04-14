<nav x-data="{ open: false }" class="bg-black/50 backdrop-blur-md border-b border-indigo-500/30 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
            <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg border-2 border-indigo-400">
                            W
                        </div>
                        <span class="text-white font-bold text-sm hidden sm:inline">WorkLog</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-indigo-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if(Auth::user()->role === 'student')
                        <x-nav-link :href="route('student.tasks.index')" :active="request()->routeIs('student.tasks.*')" class="text-white hover:text-indigo-200">
                            {{ __('My Tasks') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @include('layouts.partials.language-switcher-compact')
                <x-dropdown align="right" width="48" contentClasses="py-1 bg-indigo-900 border border-indigo-500/30 text-white">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-indigo-200 focus:outline-none transition ease-in-out duration-150 gap-2">
                            <div class="flex-shrink-0">
                                <img src="{{ Auth::user()->profile_photo_url }}" data-avatar-user-id="{{ Auth::id() }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover border-2 border-indigo-400">
                            </div>
                            
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-white hover:bg-indigo-800">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <x-dropdown-link :href="route('logout.get')" class="text-white hover:bg-indigo-800">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-black/80 backdrop-blur-md">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-indigo-200 hover:bg-indigo-900/30 border-l-4 border-transparent hover:border-indigo-400">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'student')
                <x-responsive-nav-link :href="route('student.tasks.index')" :active="request()->routeIs('student.tasks.*')" class="text-white hover:text-indigo-200 hover:bg-indigo-900/30 border-l-4 border-transparent hover:border-indigo-400">
                    {{ __('My Tasks') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-indigo-500/30">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-indigo-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:text-indigo-200 hover:bg-indigo-900/30 border-l-4 border-transparent hover:border-indigo-400">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <x-responsive-nav-link :href="route('logout.get')" class="text-white hover:text-indigo-200 hover:bg-indigo-900/30 border-l-4 border-transparent hover:border-indigo-400">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </div>
        </div>
    </div>
</nav>
