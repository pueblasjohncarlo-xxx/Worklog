@php
    $worklogThemeUser = auth()->user();
    $worklogThemePreference = 'system';

    if ($worklogThemeUser) {
        $worklogThemePreference = (string) data_get(
            session("worklog.settings.user.{$worklogThemeUser->id}", []),
            'preferences.theme',
            'system'
        );
    }

    if (! in_array($worklogThemePreference, ['system', 'light', 'dark'], true)) {
        $worklogThemePreference = 'system';
    }
@endphp

<script>
    (function () {
        const root = document.documentElement;
        const serverTheme = @json($worklogThemePreference);
        const preferredTheme = serverTheme;
        const mediaQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function resolveTheme(theme) {
            if (theme === 'dark') {
                return 'dark';
            }

            if (theme === 'light') {
                return 'light';
            }

            return mediaQuery && mediaQuery.matches ? 'dark' : 'light';
        }

        function applyTheme(theme) {
            const resolved = resolveTheme(theme);

            root.classList.toggle('dark', resolved === 'dark');
            root.dataset.themePreference = theme;
            root.dataset.themeResolved = resolved;
        }

        window.WorkLogTheme = {
            apply(theme) {
                const safeTheme = ['system', 'light', 'dark'].includes(theme) ? theme : 'system';
                applyTheme(safeTheme);
            },
            currentPreference() {
                return root.dataset.themePreference || preferredTheme;
            },
            resolvedTheme() {
                return root.dataset.themeResolved || resolveTheme(preferredTheme);
            }
        };

        applyTheme(preferredTheme);

        if (mediaQuery && typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', function () {
                if ((root.dataset.themePreference || preferredTheme) === 'system') {
                    applyTheme('system');
                }
            });
        }
    })();
</script>

<style>
    html {
        color-scheme: light;
    }

    html.dark,
    html[data-theme-resolved="dark"] {
        color-scheme: dark;
    }

    body.worklog-theme-shell {
        transition: background-color 180ms ease, color 180ms ease, background-image 180ms ease;
    }

    html[data-theme-resolved="dark"] body.worklog-theme-shell {
        background-color: #09090f;
        background-image: linear-gradient(135deg, #3b0764 0%, #1e1b4b 45%, #020617 100%);
        color: #e5e7eb;
    }

    html[data-theme-resolved="light"] body.worklog-theme-shell {
        background-color: #eef2ff;
        background-image: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 55%, #dbeafe 100%);
        color: #0f172a;
    }

    html[data-theme-resolved="light"] body.worklog-theme-shell input::placeholder,
    html[data-theme-resolved="light"] body.worklog-theme-shell textarea::placeholder {
        color: #64748b;
    }

    html[data-theme-resolved="light"] body.worklog-theme-shell ::-webkit-scrollbar-track {
        background: #dbe4ff;
    }

    html[data-theme-resolved="light"] body.worklog-theme-shell ::-webkit-scrollbar-thumb {
        background: #6366f1;
        border-color: #dbe4ff;
    }
</style>
