<x-coordinator-layout>
    <x-slot name="header">
        {{ __('Add New Supervisor') }}
    </x-slot>

    <div class="py-12" x-data="{ createCompany: {{ old('create_company') ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if ($errors->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('coordinator.supervisors.store') }}" class="space-y-6">
                @csrf

                <!-- Supervisor Information -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <header class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Supervisor Information') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Enter the personal and professional details of the new supervisor.') }}
                        </p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('department')" />
                        </div>

                        <div>
                            <x-input-label for="position_title" :value="__('Position/Role')" />
                            <x-text-input id="position_title" name="position_title" type="text" class="mt-1 block w-full" :value="old('position_title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('position_title')" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Temporary Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>
                </div>

                <!-- Company Association Toggle -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="create_company" value="0">
                        <input 
                            type="checkbox" 
                            id="create_company" 
                            name="create_company" 
                            value="1" 
                            x-model="createCompany"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            {{ old('create_company') ? 'checked' : '' }}
                        >
                        <x-input-label for="create_company" :value="__('Simultaneously create a new company for this supervisor')" class="font-semibold cursor-pointer" />
                    </div>
                </div>

                <!-- Company Information (Conditional) -->
                <div x-show="createCompany" x-transition class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <header class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Company Information') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Provide the details of the company this supervisor belongs to.') }}
                        </p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="company_name" :value="__('Company Name')" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                        </div>

                        <div>
                            <x-input-label for="company_industry" :value="__('Industry')" />
                            <x-text-input id="company_industry" name="company_industry" type="text" class="mt-1 block w-full" :value="old('company_industry')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_industry')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="company_address" :value="__('Physical Address')" />
                            <x-text-input id="company_address" name="company_address" type="text" class="mt-1 block w-full" :value="old('company_address')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_address')" />
                        </div>

                        <div>
                            <x-input-label for="company_city" :value="__('City')" />
                            <x-text-input id="company_city" name="company_city" type="text" class="mt-1 block w-full" :value="old('company_city')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_city')" />
                        </div>

                        <div>
                            <x-input-label for="company_state" :value="__('State/Province')" />
                            <x-text-input id="company_state" name="company_state" type="text" class="mt-1 block w-full" :value="old('company_state')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_state')" />
                        </div>

                        <div>
                            <x-input-label for="company_postal_code" :value="__('Postal Code')" />
                            <x-text-input id="company_postal_code" name="company_postal_code" type="text" class="mt-1 block w-full" :value="old('company_postal_code')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_postal_code')" />
                        </div>

                        <div>
                            <x-input-label for="company_country" :value="__('Country')" />
                            <x-text-input id="company_country" name="company_country" type="text" class="mt-1 block w-full" :value="old('company_country')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_country')" />
                        </div>

                        <div>
                            <x-input-label for="company_contact_person" :value="__('Contact Person')" />
                            <x-text-input id="company_contact_person" name="company_contact_person" type="text" class="mt-1 block w-full" :value="old('company_contact_person')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_contact_person')" />
                        </div>

                        <div>
                            <x-input-label for="company_contact_email" :value="__('Contact Email')" />
                            <x-text-input id="company_contact_email" name="company_contact_email" type="email" class="mt-1 block w-full" :value="old('company_contact_email')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_contact_email')" />
                        </div>

                        <div>
                            <x-input-label for="company_contact_phone" :value="__('Contact Phone')" />
                            <x-text-input id="company_contact_phone" name="company_contact_phone" type="text" class="mt-1 block w-full" :value="old('company_contact_phone')" ::required="createCompany" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_contact_phone')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('coordinator.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                    <x-primary-button>
                        {{ __('Create Supervisor') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-coordinator-layout>
