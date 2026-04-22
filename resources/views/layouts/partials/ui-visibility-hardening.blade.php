<style>
    /* System-wide readability and click affordance fallback (works without compiled assets) */
    body.worklog-ui-hardening a,
    body.worklog-ui-hardening button,
    body.worklog-ui-hardening [role="button"],
    body.worklog-ui-hardening [onclick],
    body.worklog-ui-hardening [x-on\:click],
    body.worklog-ui-hardening [\@click] {
        cursor: pointer;
    }

    body.worklog-ui-hardening a,
    body.worklog-ui-hardening button,
    body.worklog-ui-hardening input,
    body.worklog-ui-hardening select,
    body.worklog-ui-hardening textarea {
        transition: color 150ms ease, background-color 150ms ease, border-color 150ms ease, box-shadow 150ms ease;
    }

    /* Header slot text often comes with dark utility classes; force readable contrast in dark headers */
    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-900,
    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-800,
    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-700,
    body.worklog-ui-hardening header[class*="bg-black"] .text-slate-900,
    body.worklog-ui-hardening header[class*="bg-black"] .text-slate-800,
    body.worklog-ui-hardening header[class*="bg-black"] .text-slate-700,
    body.worklog-ui-hardening header[class*="bg-black"] .text-black {
        color: #f8fafc !important;
    }

    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-600,
    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-500,
    body.worklog-ui-hardening header[class*="bg-black"] .text-gray-400 {
        color: #cbd5e1 !important;
    }

    /* Some pages use translucent dark panels; rescue accidental dark text there too. */
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-900,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-800,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-700,
    body.worklog-ui-hardening [class*="bg-black/"] .text-slate-900,
    body.worklog-ui-hardening [class*="bg-black/"] .text-slate-800,
    body.worklog-ui-hardening [class*="bg-black/"] .text-slate-700,
    body.worklog-ui-hardening [class*="bg-black/"] .text-black {
        color: #f8fafc !important;
    }

    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-600,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-500,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-400 {
        color: #cbd5e1 !important;
    }

    .top-header-title-scope,
    .top-header-title-scope * {
        color: #ffffff !important;
    }

    .top-header-title-link {
        display: block;
        border-radius: 0.5rem;
        padding: 0.125rem 0.375rem;
        line-height: 1.2;
        text-decoration: none;
        font-weight: 800;
        letter-spacing: -0.01em;
    }

    .top-header-title-link:hover,
    .top-header-title-link:focus-visible {
        background-color: rgba(255, 255, 255, 0.08);
        color: #c7d2fe !important;
        outline: none;
    }

    /* Keep text dark inside light cards/tables in LIGHT MODE for clarity.
       This intentionally applies even when the card also has `dark:bg-*`.
       Dark mode is handled by the `@media (prefers-color-scheme: dark)` rescue below.
    */
    @media (prefers-color-scheme: light) {
        body.worklog-ui-hardening .bg-white .text-gray-900,
        body.worklog-ui-hardening .bg-white .text-gray-800,
        body.worklog-ui-hardening .bg-white .text-gray-700,
        body.worklog-ui-hardening .bg-gray-50 .text-gray-900,
        body.worklog-ui-hardening .bg-gray-50 .text-gray-800,
        body.worklog-ui-hardening .bg-gray-50 .text-gray-700,
        body.worklog-ui-hardening .bg-gray-100 .text-gray-900,
        body.worklog-ui-hardening .bg-gray-100 .text-gray-800,
        body.worklog-ui-hardening .bg-gray-100 .text-gray-700 {
            color: #0f172a !important;
        }

        body.worklog-ui-hardening .bg-white .text-gray-600,
        body.worklog-ui-hardening .bg-white .text-gray-500,
        body.worklog-ui-hardening .bg-gray-50 .text-gray-600,
        body.worklog-ui-hardening .bg-gray-50 .text-gray-500,
        body.worklog-ui-hardening .bg-gray-100 .text-gray-600,
        body.worklog-ui-hardening .bg-gray-100 .text-gray-500 {
            color: #334155 !important;
        }
    }

    /* Dark-mode rescue: if a surface switches to dark via `dark:bg-*`, ensure text never stays dark by mistake. */
    @media (prefers-color-scheme: dark) {
        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-900,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-800,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-700,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-slate-900,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-slate-800,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-slate-700,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-black {
            color: #f8fafc !important;
        }

        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-600,
        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-500 {
            color: #cbd5e1 !important;
        }

        body.worklog-ui-hardening [class*="dark:bg-"] .text-gray-400 {
            color: #e2e8f0 !important;
        }
    }

    /* Stronger affordance for links/buttons in dark shells */
    body.worklog-ui-hardening .glass-panel a:hover,
    body.worklog-ui-hardening [class*="bg-black/"] a:hover,
    body.worklog-ui-hardening [class*="bg-indigo-"] a:hover {
        color: #c7d2fe !important;
    }

    /* Prevent overly-faded helper text on dark shells */
    body.worklog-ui-hardening .glass-panel .text-gray-500,
    body.worklog-ui-hardening .glass-panel .text-gray-400,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-500,
    body.worklog-ui-hardening [class*="bg-black/"] .text-gray-400 {
        color: #e2e8f0 !important;
    }

    body.worklog-ui-hardening .bg-black .text-gray-500,
    body.worklog-ui-hardening .bg-black .text-gray-400,
    body.worklog-ui-hardening [class*="bg-indigo-"] .text-gray-500,
    body.worklog-ui-hardening [class*="bg-indigo-"] .text-gray-400,
    body.worklog-ui-hardening [class*="bg-purple-"] .text-gray-500,
    body.worklog-ui-hardening [class*="bg-purple-"] .text-gray-400 {
        color: #e2e8f0 !important;
    }

    body.worklog-ui-hardening .bg-black svg.text-gray-500,
    body.worklog-ui-hardening .bg-black svg.text-gray-400 {
        color: #e2e8f0 !important;
    }

    /* Sidebars and top navigation need brighter default/menu contrast across all roles. */
    body.worklog-ui-hardening .app-sidebar,
    body.worklog-ui-hardening nav[class*="bg-black"] {
        color: #f8fafc;
    }

    body.worklog-ui-hardening .app-sidebar .text-gray-500,
    body.worklog-ui-hardening .app-sidebar .text-gray-400,
    body.worklog-ui-hardening .app-sidebar .text-gray-300,
    body.worklog-ui-hardening nav[class*="bg-black"] .text-gray-500,
    body.worklog-ui-hardening nav[class*="bg-black"] .text-gray-400,
    body.worklog-ui-hardening nav[class*="bg-black"] .text-gray-300 {
        color: #e5e7eb !important;
        opacity: 1 !important;
    }

    body.worklog-ui-hardening .app-sidebar a,
    body.worklog-ui-hardening nav[class*="bg-black"] a {
        color: inherit;
    }

    body.worklog-ui-hardening .app-sidebar a:hover,
    body.worklog-ui-hardening .app-sidebar button:hover,
    body.worklog-ui-hardening nav[class*="bg-black"] a:hover,
    body.worklog-ui-hardening nav[class*="bg-black"] button:hover {
        color: #ffffff !important;
    }

    body.worklog-ui-hardening .text-gray-400.uppercase,
    body.worklog-ui-hardening .text-gray-500.uppercase,
    body.worklog-ui-hardening .text-slate-500.uppercase {
        letter-spacing: 0.08em;
        font-weight: 700 !important;
    }

    body.worklog-ui-hardening :focus-visible {
        outline: 2px solid #818cf8;
        outline-offset: 2px;
    }

    body.worklog-ui-hardening .bg-white button,
    body.worklog-ui-hardening .bg-white a,
    body.worklog-ui-hardening .bg-gray-50 button,
    body.worklog-ui-hardening .bg-gray-50 a {
        color: inherit;
    }

    body.worklog-ui-hardening .bg-white table,
    body.worklog-ui-hardening .bg-gray-50 table,
    body.worklog-ui-hardening .bg-white thead,
    body.worklog-ui-hardening .bg-gray-50 thead,
    body.worklog-ui-hardening .bg-white tbody,
    body.worklog-ui-hardening .bg-gray-50 tbody {
        color: #0f172a !important;
    }

    body.worklog-ui-hardening .bg-white th,
    body.worklog-ui-hardening .bg-gray-50 th {
        color: #334155 !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em;
    }

    body.worklog-ui-hardening .bg-white td,
    body.worklog-ui-hardening .bg-gray-50 td,
    body.worklog-ui-hardening .bg-white label,
    body.worklog-ui-hardening .bg-gray-50 label,
    body.worklog-ui-hardening .bg-white p,
    body.worklog-ui-hardening .bg-gray-50 p,
    body.worklog-ui-hardening .bg-white span,
    body.worklog-ui-hardening .bg-gray-50 span {
        color: inherit;
    }

    body.worklog-ui-hardening .bg-white .text-white,
    body.worklog-ui-hardening .bg-gray-50 .text-white {
        color: #ffffff !important;
    }

    body.worklog-ui-hardening .bg-white [class*="bg-indigo-"],
    body.worklog-ui-hardening .bg-white [class*="bg-blue-"],
    body.worklog-ui-hardening .bg-white [class*="bg-green-"],
    body.worklog-ui-hardening .bg-white [class*="bg-red-"],
    body.worklog-ui-hardening .bg-white [class*="bg-amber-"],
    body.worklog-ui-hardening .bg-gray-50 [class*="bg-indigo-"],
    body.worklog-ui-hardening .bg-gray-50 [class*="bg-blue-"],
    body.worklog-ui-hardening .bg-gray-50 [class*="bg-green-"],
    body.worklog-ui-hardening .bg-gray-50 [class*="bg-red-"],
    body.worklog-ui-hardening .bg-gray-50 [class*="bg-amber-"] {
        color: #ffffff;
    }

    body.worklog-ui-hardening .bg-white input::placeholder,
    body.worklog-ui-hardening .bg-white textarea::placeholder,
    body.worklog-ui-hardening .bg-gray-50 input::placeholder,
    body.worklog-ui-hardening .bg-gray-50 textarea::placeholder {
        color: #64748b !important;
        opacity: 1 !important;
    }

    body.worklog-ui-hardening input,
    body.worklog-ui-hardening select,
    body.worklog-ui-hardening textarea {
        color: inherit;
    }

    body.worklog-ui-hardening .bg-white input,
    body.worklog-ui-hardening .bg-white select,
    body.worklog-ui-hardening .bg-white textarea,
    body.worklog-ui-hardening .bg-gray-50 input,
    body.worklog-ui-hardening .bg-gray-50 select,
    body.worklog-ui-hardening .bg-gray-50 textarea {
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
    }

    body.worklog-ui-hardening [class*="bg-black/"] input,
    body.worklog-ui-hardening [class*="bg-black/"] select,
    body.worklog-ui-hardening [class*="bg-black/"] textarea,
    body.worklog-ui-hardening [class*="dark:bg-gray-"] input,
    body.worklog-ui-hardening [class*="dark:bg-gray-"] select,
    body.worklog-ui-hardening [class*="dark:bg-gray-"] textarea {
        color: #f8fafc !important;
        border-color: rgba(99, 102, 241, 0.25) !important;
    }

    body.worklog-ui-hardening option {
        color: #0f172a;
        background: #ffffff;
    }

    /* Modal/read-only panels */
    body.worklog-ui-hardening [role="dialog"] .bg-white,
    body.worklog-ui-hardening [role="dialog"] [class*="bg-white"],
    body.worklog-ui-hardening .fixed .bg-white {
        color: #0f172a !important;
    }

    body.worklog-ui-hardening [role="dialog"] .text-gray-500,
    body.worklog-ui-hardening [role="dialog"] .text-gray-400,
    body.worklog-ui-hardening .fixed .text-gray-500,
    body.worklog-ui-hardening .fixed .text-gray-400 {
        color: #475569 !important;
        opacity: 1 !important;
    }

    body.worklog-ui-hardening .bg-white.rounded-lg,
    body.worklog-ui-hardening .bg-white.rounded-xl,
    body.worklog-ui-hardening .bg-white.rounded-2xl,
    body.worklog-ui-hardening .bg-gray-50.rounded-lg,
    body.worklog-ui-hardening .bg-gray-50.rounded-xl,
    body.worklog-ui-hardening .bg-gray-50.rounded-2xl {
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    /* Student cards and tabs should remain readable even before CSS assets are rebuilt. */
    body.worklog-ui-hardening .student-light-card {
        background: #ffffff !important;
        border-color: rgba(203, 213, 225, 0.95) !important;
        color: #0f172a !important;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12) !important;
    }

    body.worklog-ui-hardening .student-light-card .student-card-title,
    body.worklog-ui-hardening .student-light-card .text-slate-500,
    body.worklog-ui-hardening .student-light-card .text-gray-500,
    body.worklog-ui-hardening .student-light-card .text-gray-400,
    body.worklog-ui-hardening .student-task-card .text-slate-500,
    body.worklog-ui-hardening .student-task-card .text-gray-500,
    body.worklog-ui-hardening .student-task-card .text-gray-400 {
        color: #475569 !important;
    }

    body.worklog-ui-hardening .student-task-card {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12) !important;
    }

    body.worklog-ui-hardening .student-section-shell .text-gray-500,
    body.worklog-ui-hardening .student-section-shell .text-gray-400 {
        color: #e2e8f0 !important;
    }

    body.worklog-ui-hardening .student-tab-inactive {
        color: #cbd5e1 !important;
    }

    body.worklog-ui-hardening .student-tab-active {
        color: #ffffff !important;
    }

    /* Student messages layout */
    body.worklog-ui-hardening [x-data="chatApp()"] .text-gray-500,
    body.worklog-ui-hardening [x-data="chatApp()"] .text-gray-400 {
        color: #cbd5e1 !important;
        opacity: 1 !important;
    }

    body.worklog-ui-hardening [x-data="chatApp()"] .bg-gray-800 {
        color: #f8fafc !important;
    }

    body.worklog-ui-hardening [x-data="chatApp()"] input::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }

    /* Table readability across reports and mapping surfaces */
    body.worklog-ui-hardening table.divide-y tbody tr {
        border-color: #e2e8f0 !important;
    }

    body.worklog-ui-hardening table thead th {
        font-weight: 800 !important;
    }

    body.worklog-ui-hardening table tbody td {
        vertical-align: middle;
    }

    body.worklog-ui-hardening .inline-flex.rounded-full.text-xs,
    body.worklog-ui-hardening .inline-flex.rounded-full.text-\[10px\],
    body.worklog-ui-hardening .inline-flex.rounded-full.text-\[11px\] {
        font-weight: 700 !important;
    }

    /* Select2 surfaces used by coordinator/admin flows */
    body.worklog-ui-hardening .select2-container--default .select2-selection--single,
    body.worklog-ui-hardening .select2-container--default .select2-selection--multiple {
        border-color: rgba(99, 102, 241, 0.24) !important;
        color: #f8fafc !important;
    }

    body.worklog-ui-hardening .select2-container--default .select2-selection--single .select2-selection__rendered,
    body.worklog-ui-hardening .select2-container--default .select2-selection--multiple .select2-selection__rendered,
    body.worklog-ui-hardening .select2-container--default .select2-selection--single .select2-selection__placeholder,
    body.worklog-ui-hardening .select2-dropdown,
    body.worklog-ui-hardening .select2-search__field {
        color: #e5e7eb !important;
    }

    /* Disabled controls should not become invisible */
    body.worklog-ui-hardening button:disabled,
    body.worklog-ui-hardening input:disabled,
    body.worklog-ui-hardening select:disabled,
    body.worklog-ui-hardening textarea:disabled {
        opacity: 0.8 !important;
        color: #94a3b8 !important;
    }
</style>

<script>
    // Chart.js global defaults: keep labels readable on dark shells.
    (function () {
        if (!window.Chart || !window.Chart.defaults) return;
        try {
            const fallbackText = '#e5e7eb';
            const text = getComputedStyle(document.body).color || fallbackText;

            window.Chart.defaults.color = text;
            window.Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.25)';
            window.Chart.defaults.plugins = window.Chart.defaults.plugins || {};
            window.Chart.defaults.plugins.legend = window.Chart.defaults.plugins.legend || {};
            window.Chart.defaults.plugins.legend.labels = window.Chart.defaults.plugins.legend.labels || {};
            window.Chart.defaults.plugins.legend.labels.color = text;

            if (window.Chart.defaults.scales) {
                for (const scaleKey of Object.keys(window.Chart.defaults.scales)) {
                    const scale = window.Chart.defaults.scales[scaleKey];
                    if (scale && scale.ticks) scale.ticks.color = text;
                    if (scale && scale.grid) scale.grid.color = 'rgba(148, 163, 184, 0.18)';
                }
            }
        } catch (e) {
            // No-op: never block page rendering.
        }
    })();
</script>
