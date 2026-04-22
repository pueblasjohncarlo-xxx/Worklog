<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        Invitation Links
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('invite_link'))
            <div class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-3 shadow-sm">
                <p class="text-sm font-bold text-indigo-950">Manual Invitation Link</p>
                <div class="mt-2 flex flex-col md:flex-row gap-2">
                    <input
                        type="text"
                        readonly
                        value="{{ session('invite_link') }}"
                        class="w-full rounded-md border border-indigo-200 bg-white px-3 py-2 text-sm text-indigo-950"
                    >
                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText('{{ session('invite_link') }}')"
                        class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2"
                    >
                        Copy Link
                    </button>
                </div>
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Send New Invitation</h3>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">Create a secure registration link and send it through email.</p>

            <form action="{{ route('invitations.store') }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @csrf

                <div class="lg:col-span-2">
                    <label for="email" class="mb-1 block text-sm font-semibold text-gray-800 dark:text-gray-100">Recipient Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email') }}"
                        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-400 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                        placeholder="name@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="mb-1 block text-sm font-semibold text-gray-800 dark:text-gray-100">Role</label>
                    <select id="role" name="role" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="student" @selected(old('role') === 'student')>Student</option>
                        <option value="supervisor" @selected(old('role') === 'supervisor')>Supervisor</option>
                        <option value="ojt_adviser" @selected(old('role') === 'ojt_adviser')>OJT Adviser</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expires_in_hours" class="mb-1 block text-sm font-semibold text-gray-800 dark:text-gray-100">Expires In</label>
                    <select id="expires_in_hours" name="expires_in_hours" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <option value="24" @selected((int) old('expires_in_hours', 72) === 24)>24 hours</option>
                        <option value="48" @selected((int) old('expires_in_hours', 72) === 48)>48 hours</option>
                        <option value="72" @selected((int) old('expires_in_hours', 72) === 72)>72 hours</option>
                        <option value="168" @selected((int) old('expires_in_hours', 72) === 168)>7 days</option>
                    </select>
                </div>

                <div class="md:col-span-2 lg:col-span-2">
                    <label for="company_id" class="mb-1 block text-sm font-semibold text-gray-800 dark:text-gray-100">Company (for Supervisor role)</label>
                    <select id="company_id" name="company_id" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <option value="">Select company (optional for non-supervisor)</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="mt-1 text-xs font-medium text-rose-700 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 lg:col-span-2 flex items-end">
                    <button type="submit" class="w-full rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                        Send Invitation Link
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Recent Invitations</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 text-left text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">Email</th>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">Role</th>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">Company</th>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">Expires</th>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-2 pr-4 font-semibold uppercase tracking-wider">By</th>
                            <th class="py-2 font-semibold uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($invitations as $invitation)
                            @php
                                $status = $invitation->status;
                                $statusClasses = match ($status) {
                                    'accepted' => 'bg-emerald-100 text-emerald-700',
                                    'revoked' => 'bg-rose-100 text-rose-700',
                                    'expired' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-indigo-100 text-indigo-700',
                                };
                            @endphp
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $invitation->email }}</td>
                                <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $invitation->role)) }}</td>
                                <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $invitation->company?->name ?? '-' }}</td>
                                <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $invitation->expires_at?->format('M d, Y h:i A') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $invitation->invitedBy?->name ?? 'Unknown' }}</td>
                                <td class="py-3">
                                    @if ($status === 'pending')
                                        <form action="{{ route('invitations.revoke', $invitation) }}" method="POST" onsubmit="return confirm('Revoke this invitation?');">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold uppercase tracking-wide text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-200">Revoke</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-600 dark:text-gray-300">No invitations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $invitations->links() }}
            </div>
        </div>
    </div>
</x-dynamic-component>
