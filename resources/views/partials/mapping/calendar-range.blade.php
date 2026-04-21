@php
    /** @var array $mapping */
@endphp

<div class="space-y-6">
    @foreach(($mapping['months'] ?? []) as $m)
        @php
            $status = $m['validation']['status'] ?? null;
            $statusClasses = $status === 'match'
                ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                : ($status === 'inconsistent'
                    ? 'bg-rose-100 text-rose-700 border-rose-200'
                    : ($status === 'incomplete'
                        ? 'bg-amber-100 text-amber-700 border-amber-200'
                        : 'bg-gray-100 text-gray-700 border-gray-200'));
        @endphp

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                <div class="font-bold text-gray-800 dark:text-gray-100 tracking-wide">{{ $m['label'] ?? '' }}</div>
                <div class="flex items-center gap-3">
                    @if($status)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $statusClasses }}">
                            {{ strtoupper($status) }}
                        </span>
                    @endif
                    <div class="text-xs text-gray-600 dark:text-gray-300">
                        Monthly Total: <span class="font-bold text-red-600">{{ rtrim(rtrim(number_format((float)($m['month_total'] ?? 0), 2), '0'), '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-white dark:bg-gray-800">
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                                <th class="border border-gray-200 dark:border-gray-700 px-2 py-2 text-[11px] font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider text-center">{{ $d }}</th>
                            @endforeach
                            <th class="border border-gray-200 dark:border-gray-700 px-2 py-2 text-[11px] font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider text-center whitespace-nowrap">
                                Total OJT Hr<br>per week
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($m['weeks'] ?? []) as $week)
                            <tr>
                                @foreach(($week['days'] ?? []) as $day)
                                    <td class="border border-gray-200 dark:border-gray-700 w-[84px] h-[60px] align-top p-2">
                                        @if(!empty($day['day']))
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 leading-none">{{ $day['day'] }}</div>
                                            <div class="mt-1 text-[12px] font-black text-red-600 leading-none">
                                                {{ empty($day['hours']) ? '' : rtrim(rtrim(number_format((float)$day['hours'], 2), '0'), '.') }}
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="border border-gray-200 dark:border-gray-700 text-center px-2 py-2">
                                    <span class="text-[12px] font-black text-red-600">
                                        {{ ($week['total'] ?? 0) > 0 ? rtrim(rtrim(number_format((float)$week['total'], 2), '0'), '.') : '' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50 dark:bg-gray-900/30">
                            <td colspan="7" class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-right text-sm font-bold text-gray-700 dark:text-gray-200">
                                Monthly Total:
                            </td>
                            <td class="border border-gray-200 dark:border-gray-700 px-3 py-2 text-center">
                                <span class="text-sm font-black text-red-600">{{ rtrim(rtrim(number_format((float)($m['month_total'] ?? 0), 2), '0'), '.') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700 font-bold text-gray-800 dark:text-gray-100">
            Summary
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-white dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">OJT Hr per month</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Sub Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach(($mapping['summary'] ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $row['label'] ?? '' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-red-600">
                                {{ rtrim(rtrim(number_format((float)($row['hours'] ?? 0), 2), '0'), '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-gray-900/30">
                        <td class="px-4 py-3 text-sm font-black text-gray-800 dark:text-gray-100 text-right">TOTAL OJT HOURS</td>
                        <td class="px-4 py-3 text-sm font-black text-red-600 text-right">
                            {{ rtrim(rtrim(number_format((float)($mapping['overall_total'] ?? 0), 2), '0'), '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
