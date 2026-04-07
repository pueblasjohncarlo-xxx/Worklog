<x-coordinator-layout>
    <x-slot name="header">
        OJT Adviser Overview
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white/95 dark:bg-gray-900/70 backdrop-blur overflow-hidden shadow-sm sm:rounded-lg border border-white/10">
            <div class="p-6 text-gray-900 dark:text-gray-100" x-data='{
                selectedAdviserId: null,
                advisers: @json($advisersData),
                init() {
                },
                selectedAdviser() {
                    if (!this.selectedAdviserId) return null;
                    return this.advisers.find(a => String(a.id) === String(this.selectedAdviserId)) || null;
                },
                studentsForSelected() {
                    const adviser = this.selectedAdviser();
                    if (!adviser) return [];
                    return Object.values(adviser.studentsBySection || {}).flat();
                }
            }' x-init="init()">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">OJT Adviser Roster</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Manage Users
                        </a>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-6">
                    <div class="w-full md:w-80">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-2">
                            OJT Adviser
                        </label>
                        <select x-model="selectedAdviserId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/60 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select an adviser</option>
                            <template x-for="adviser in advisers" :key="adviser.id">
                                <option :value="adviser.id" x-text="adviser.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-full md:flex-1 flex items-end md:justify-end">
                        <a x-show="selectedAdviserId" x-transition :href="`/messages/${selectedAdviserId}`" class="inline-flex items-center px-4 py-2 bg-gray-900/90 dark:bg-white/10 border border-gray-900/10 dark:border-white/10 rounded-md font-semibold text-xs text-white dark:text-gray-100 uppercase tracking-widest hover:bg-gray-900 dark:hover:bg-white/20 transition ease-in-out duration-150">
                            Message Adviser
                        </a>
                    </div>
                </div>

                <div class="border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden bg-white dark:bg-gray-900/40">
                    <template x-if="advisers.length === 0">
                        <div class="p-6 text-center text-sm text-gray-700 dark:text-gray-200">
                            No OJT Advisers found.
                        </div>
                    </template>

                    <template x-if="advisers.length > 0">
                        <div>
                            <div x-cloak x-show="selectedAdviserId" x-transition>
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-900/30">
                                    <div class="flex items-center gap-4">
                                        <div class="h-11 w-11 rounded-full overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-900/10 flex items-center justify-center">
                                            <template x-if="selectedAdviser() && selectedAdviser().photo_url">
                                                <img :src="selectedAdviser().photo_url" alt="" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="selectedAdviser() && !selectedAdviser().photo_url">
                                                <div class="text-white font-black" x-text="selectedAdviser().name ? selectedAdviser().name.charAt(0).toUpperCase() : ''"></div>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-base font-bold text-gray-900 dark:text-white truncate" x-text="selectedAdviser() ? selectedAdviser().name : ''"></div>
                                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-200" x-show="selectedAdviser()" x-transition>
                                                <span x-text="selectedAdviser().email"></span>
                                                <span class="mx-2">•</span>
                                                <span x-text="selectedAdviser().department"></span>
                                                <span class="mx-2">•</span>
                                                <span class="font-semibold"><span x-text="studentsForSelected().length"></span> Student(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                                        <thead class="bg-white dark:bg-transparent">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Student Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Section</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                            <template x-if="studentsForSelected().length === 0">
                                                <tr>
                                                    <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-700 dark:text-gray-200">
                                                        No students assigned to this adviser.
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-for="student in studentsForSelected()" :key="student.id">
                                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                                    <td class="px-4 py-4">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="student.name"></div>
                                                        <div class="text-xs text-gray-700 dark:text-gray-200" x-text="student.email"></div>
                                                    </td>
                                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-gray-100" x-text="student.section"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
