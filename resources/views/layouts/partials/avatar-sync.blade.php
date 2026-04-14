@auth
<script>
(function () {
    const endpoint = "{{ route('profile.avatar-versions') }}";
    const storageKey = 'worklog:avatar-updated';
    const refreshIntervalMs = 2000;

    let refreshTimer = null;
    let inFlight = false;

    function getAvatarImages() {
        return Array.from(document.querySelectorAll('img[data-avatar-user-id]'));
    }

    function getTrackedUserIds() {
        const ids = new Set();
        getAvatarImages().forEach((img) => {
            const value = parseInt(img.dataset.avatarUserId || '', 10);
            if (!Number.isNaN(value) && value > 0) {
                ids.add(value);
            }
        });

        return Array.from(ids);
    }

    function applyAvatarMap(avatarMap) {
        if (!avatarMap || typeof avatarMap !== 'object') {
            return;
        }

        getAvatarImages().forEach((img) => {
            const userId = img.dataset.avatarUserId;
            if (!userId || !avatarMap[userId] || !avatarMap[userId].url) {
                return;
            }

            const nextUrl = avatarMap[userId].url;
            if (img.src !== nextUrl) {
                img.src = nextUrl;
            }
        });
    }

    async function refreshAvatars() {
        if (inFlight) {
            return;
        }

        const userIds = getTrackedUserIds();
        if (userIds.length === 0) {
            return;
        }

        inFlight = true;
        try {
            const params = new URLSearchParams();
            params.set('ids', userIds.join(','));

            const response = await fetch(endpoint + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            applyAvatarMap(data.avatars);
        } catch (error) {
            console.error('Avatar sync error:', error);
        } finally {
            inFlight = false;
        }
    }

    function broadcastAvatarUpdate() {
        try {
            localStorage.setItem(storageKey, String(Date.now()));
        } catch (error) {
            // Ignore storage issues in private mode.
        }

        window.dispatchEvent(new CustomEvent('worklog:avatar-updated'));
    }

    window.WorkLogAvatarSync = {
        refresh: refreshAvatars,
        broadcast: broadcastAvatarUpdate,
    };

    window.addEventListener('storage', (event) => {
        if (event.key === storageKey) {
            refreshAvatars();
        }
    });

    window.addEventListener('worklog:avatar-updated', refreshAvatars);

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            refreshAvatars();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        refreshAvatars();

        if (refreshTimer) {
            clearInterval(refreshTimer);
        }

        refreshTimer = setInterval(refreshAvatars, refreshIntervalMs);
    });
})();
</script>
@endauth
