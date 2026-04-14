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
    }

    .top-header-title-link:hover,
    .top-header-title-link:focus-visible {
        background-color: rgba(255, 255, 255, 0.08);
        color: #c7d2fe !important;
        outline: none;
    }

    /* Keep text dark inside white cards/tables for clarity */
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

    /* Stronger affordance for links/buttons in dark shells */
    body.worklog-ui-hardening .glass-panel a:hover,
    body.worklog-ui-hardening [class*="bg-black/"] a:hover,
    body.worklog-ui-hardening [class*="bg-indigo-"] a:hover {
        color: #c7d2fe !important;
    }

    body.worklog-ui-hardening :focus-visible {
        outline: 2px solid #818cf8;
        outline-offset: 2px;
    }
</style>
