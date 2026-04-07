<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Tasks') }}
            </h2>
            
            <div class="flex items-center gap-4">
                <!-- Sorting Control -->
                <div class="flex items-center text-sm">
                    <span class="mr-2 text-gray-500 dark:text-gray-400">Sort by Date:</span>
                    <div class="relative inline-flex bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" 
                           class="px-3 py-1 rounded-l-md {{ request('sort') === 'asc' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                           title="Ascending">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" 
                           class="px-3 py-1 rounded-r-md border-l border-gray-300 dark:border-gray-600 {{ request('sort', 'desc') === 'desc' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                           title="Descending">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Semester Toggle -->
                <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                    <button @click="$dispatch('switch-tab', '1st')" 
                            :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': $store.tabs.current === '1st', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': $store.tabs.current !== '1st' }" 
                            class="px-4 py-1.5 rounded-md text-sm font-medium transition-all duration-200">
                        1st Semester
                    </button>
                    <button @click="$dispatch('switch-tab', '2nd')" 
                            :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': $store.tabs.current === '2nd', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': $store.tabs.current !== '2nd' }" 
                            class="px-4 py-1.5 rounded-md text-sm font-medium transition-all duration-200">
                        2nd Semester
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- 1st Semester Content -->
                    <div x-show="$store.tabs.current === '1st'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0">
                        @include('student.tasks.partials.task-list', ['tasks' => $sem1_tasks, 'semester' => '1st'])
                    </div>

                    <!-- 2nd Semester Content -->
                    <div x-show="$store.tabs.current === '2nd'" 
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0">
                        @include('student.tasks.partials.task-list', ['tasks' => $sem2_tasks, 'semester' => '2nd'])
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('tabs', {
                current: '1st'
            });
            
            window.addEventListener('switch-tab', (event) => {
                Alpine.store('tabs').current = event.detail;
            });
        });
    </script>
    @endpush
</x-app-layout>
