<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account, role details, and profile photo. Changes save automatically as you edit.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        id="profileSettingsForm"
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
        enctype="multipart/form-data"
        data-autosave="true"
    >
        @csrf
        @method('patch')

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-4">
                    <img
                        id="settingsProfilePhotoPreview"
                        src="{{ $user->profile_photo_url }}"
                        data-avatar-user-id="{{ $user->id }}"
                        alt="{{ $user->name }}"
                        class="h-20 w-20 rounded-full object-cover border-2 border-indigo-300 dark:border-indigo-500 shadow-md"
                    >
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100" data-user-name-id="{{ $user->id }}">{{ $user->name }}</div>
                        <div class="text-xs uppercase tracking-widest text-indigo-600 dark:text-indigo-300">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                </div>

                <div class="sm:ml-auto">
                    <label for="photo" class="inline-flex cursor-pointer items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                        Change Photo
                    </label>
                    <input id="photo" name="photo" type="file" class="hidden" accept="image/*" />
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, or WEBP up to 4 MB.</p>
                </div>
            </div>
            <x-input-error class="mt-3" :messages="$errors->get('photo')" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Display Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="firstname" :value="__('First Name')" />
                <x-text-input id="firstname" name="firstname" type="text" class="mt-1 block w-full" :value="old('firstname', $user->firstname)" />
                <x-input-error class="mt-2" :messages="$errors->get('firstname')" />
            </div>

            <div>
                <x-input-label for="lastname" :value="__('Last Name')" />
                <x-text-input id="lastname" name="lastname" type="text" class="mt-1 block w-full" :value="old('lastname', $user->lastname)" />
                <x-input-error class="mt-2" :messages="$errors->get('lastname')" />
            </div>

            <div>
                <x-input-label for="middlename" :value="__('Middle Name')" />
                <x-text-input id="middlename" name="middlename" type="text" class="mt-1 block w-full" :value="old('middlename', $user->middlename)" />
                <x-input-error class="mt-2" :messages="$errors->get('middlename')" />
            </div>

            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <x-text-input id="gender" name="gender" type="text" class="mt-1 block w-full" :value="old('gender', $user->gender)" />
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <div>
                <x-input-label for="age" :value="__('Age')" />
                <x-text-input id="age" name="age" type="number" min="1" max="120" class="mt-1 block w-full" :value="old('age', $user->age)" />
                <x-input-error class="mt-2" :messages="$errors->get('age')" />
            </div>

            <div>
                <x-input-label for="department" :value="__('Department')" />
                <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $user->department)" />
                <x-input-error class="mt-2" :messages="$errors->get('department')" />
            </div>

            <div>
                <x-input-label for="section" :value="__('Section')" />
                <x-text-input id="section" name="section" type="text" class="mt-1 block w-full" :value="old('section', $user->section)" />
                <x-input-error class="mt-2" :messages="$errors->get('section')" />
            </div>
        </div>

        @if ($user->role === \App\Models\User::ROLE_STUDENT)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200">Student Details</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="student_number" :value="__('Student Number')" />
                        <x-text-input id="student_number" name="student_number" type="text" class="mt-1 block w-full" :value="old('student_number', $user->studentProfile?->student_number)" />
                    </div>
                    <div>
                        <x-input-label for="program" :value="__('Program')" />
                        <x-text-input id="program" name="program" type="text" class="mt-1 block w-full" :value="old('program', $user->studentProfile?->program)" />
                    </div>
                    <div>
                        <x-input-label for="year_level" :value="__('Year Level')" />
                        <x-text-input id="year_level" name="year_level" type="text" class="mt-1 block w-full" :value="old('year_level', $user->studentProfile?->year_level)" />
                    </div>
                    <div>
                        <x-input-label for="student_phone" :value="__('Phone')" />
                        <x-text-input id="student_phone" name="student_phone" type="text" class="mt-1 block w-full" :value="old('student_phone', $user->studentProfile?->phone)" />
                    </div>
                    <div>
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', optional($user->studentProfile?->date_of_birth)->format('Y-m-d'))" />
                    </div>
                </div>
            </div>
        @endif

        @if ($user->role === \App\Models\User::ROLE_SUPERVISOR)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200">Supervisor Details</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="position_title" :value="__('Position Title')" />
                        <x-text-input id="position_title" name="position_title" type="text" class="mt-1 block w-full" :value="old('position_title', $user->supervisorProfile?->position_title)" />
                    </div>
                    <div>
                        <x-input-label for="supervisor_department" :value="__('Department')" />
                        <x-text-input id="supervisor_department" name="supervisor_department" type="text" class="mt-1 block w-full" :value="old('supervisor_department', $user->supervisorProfile?->department)" />
                    </div>
                    <div>
                        <x-input-label for="supervisor_phone" :value="__('Phone')" />
                        <x-text-input id="supervisor_phone" name="supervisor_phone" type="text" class="mt-1 block w-full" :value="old('supervisor_phone', $user->supervisorProfile?->phone)" />
                    </div>
                </div>
            </div>
        @endif

        @if ($user->role === \App\Models\User::ROLE_COORDINATOR)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200">Coordinator Details</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="coordinator_department" :value="__('Department')" />
                        <x-text-input id="coordinator_department" name="coordinator_department" type="text" class="mt-1 block w-full" :value="old('coordinator_department', $user->coordinatorProfile?->department)" />
                    </div>
                    <div>
                        <x-input-label for="coordinator_phone" :value="__('Phone')" />
                        <x-text-input id="coordinator_phone" name="coordinator_phone" type="text" class="mt-1 block w-full" :value="old('coordinator_phone', $user->coordinatorProfile?->phone)" />
                    </div>
                </div>
            </div>
        @endif

        @if ($user->role === \App\Models\User::ROLE_OJT_ADVISER)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-700 dark:text-gray-200">OJT Adviser Details</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="ojt_adviser_department" :value="__('Department')" />
                        <x-text-input id="ojt_adviser_department" name="ojt_adviser_department" type="text" class="mt-1 block w-full" :value="old('ojt_adviser_department', $user->ojtAdviserProfile?->department)" />
                    </div>
                    <div>
                        <x-input-label for="ojt_adviser_phone" :value="__('Phone')" />
                        <x-text-input id="ojt_adviser_phone" name="ojt_adviser_phone" type="text" class="mt-1 block w-full" :value="old('ojt_adviser_phone', $user->ojtAdviserProfile?->phone)" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="ojt_adviser_address" :value="__('Address')" />
                        <textarea id="ojt_adviser_address" name="ojt_adviser_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('ojt_adviser_address', $user->ojtAdviserProfile?->address) }}</textarea>
                    </div>
                </div>
            </div>
        @endif

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-4">
            <x-primary-button>{{ __('Save Now') }}</x-primary-button>
            <span id="profileAutosaveStatus" class="text-sm text-gray-600 dark:text-gray-400">
                @if (session('status') === 'profile-updated')
                    {{ __('Saved.') }}
                @else
                    {{ __('Autosave ready.') }}
                @endif
            </span>
        </div>
    </form>
</section>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('profileSettingsForm');
            if (!form) {
                return;
            }

            const statusEl = document.getElementById('profileAutosaveStatus');
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('settingsProfilePhotoPreview');
            let saveTimer = null;
            let saving = false;
            let queued = false;
            let previewObjectUrl = null;

            const setStatus = (text, tone) => {
                if (!statusEl) {
                    return;
                }

                statusEl.textContent = text;
                statusEl.className = 'text-sm';

                if (tone === 'error') {
                    statusEl.classList.add('text-rose-600', 'dark:text-rose-400');
                } else if (tone === 'success') {
                    statusEl.classList.add('text-emerald-600', 'dark:text-emerald-400');
                } else {
                    statusEl.classList.add('text-gray-600', 'dark:text-gray-400');
                }
            };

            const applySyncedProfile = (profile) => {
                if (!profile) {
                    return;
                }

                if (window.WorkLogProfileSync && typeof window.WorkLogProfileSync.broadcast === 'function') {
                    window.WorkLogProfileSync.broadcast(profile);
                } else if (window.WorkLogAvatarSync && typeof window.WorkLogAvatarSync.broadcast === 'function') {
                    window.WorkLogAvatarSync.broadcast();
                }
            };

            const saveProfile = async () => {
                if (saving) {
                    queued = true;
                    return;
                }

                saving = true;
                setStatus('Saving changes...', 'neutral');

                try {
                    const formData = new FormData(form);
                    formData.set('_method', 'PATCH');

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to save settings.');
                    }

                    if (data.profile && data.profile.avatar_url && photoPreview) {
                        photoPreview.src = data.profile.avatar_url;
                    }

                    applySyncedProfile(data.profile || null);
                    setStatus('Saved just now.', 'success');
                } catch (error) {
                    setStatus(error.message || 'Unable to save settings.', 'error');
                } finally {
                    saving = false;

                    if (queued) {
                        queued = false;
                        saveProfile();
                    }
                }
            };

            const queueSave = () => {
                clearTimeout(saveTimer);
                setStatus('Changes pending...', 'neutral');
                saveTimer = setTimeout(saveProfile, 700);
            };

            form.querySelectorAll('input, textarea, select').forEach((field) => {
                if (field.type === 'file') {
                    return;
                }

                field.addEventListener('input', queueSave);
                field.addEventListener('change', queueSave);
            });

            if (photoInput) {
                photoInput.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                    if (file && photoPreview && file.type.startsWith('image/')) {
                        if (previewObjectUrl) {
                            URL.revokeObjectURL(previewObjectUrl);
                        }

                        previewObjectUrl = URL.createObjectURL(file);
                        photoPreview.src = previewObjectUrl;
                    }

                    queueSave();
                });
            }
        });
    </script>
    @endpush
@endonce
