@auth
<script>
(function () {
    const endpoint = "{{ route('profile.avatar-versions') }}";
    const storageKey = 'worklog:profile-updated';
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

    function getNameNodes() {
        return Array.from(document.querySelectorAll('[data-user-name-id]'));
    }

    function getEmailNodes() {
        return Array.from(document.querySelectorAll('[data-user-email-id]'));
    }

    function applyProfileMap(avatarMap) {
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

            if (avatarMap[userId].name) {
                img.alt = avatarMap[userId].name;
                img.title = avatarMap[userId].name;
            }
        });

        getNameNodes().forEach((node) => {
            const userId = node.dataset.userNameId;
            if (!userId || !avatarMap[userId] || !avatarMap[userId].name) {
                return;
            }

            node.textContent = avatarMap[userId].name;
        });

        getEmailNodes().forEach((node) => {
            const userId = node.dataset.userEmailId;
            if (!userId || !avatarMap[userId] || !avatarMap[userId].email) {
                return;
            }

            node.textContent = avatarMap[userId].email;
        });
    }

    async function refreshProfiles() {
        if (inFlight) {
            return;
        }

        const ids = new Set(getTrackedUserIds());
        getNameNodes().forEach((node) => {
            const value = parseInt(node.dataset.userNameId || '', 10);
            if (!Number.isNaN(value) && value > 0) {
                ids.add(value);
            }
        });
        getEmailNodes().forEach((node) => {
            const value = parseInt(node.dataset.userEmailId || '', 10);
            if (!Number.isNaN(value) && value > 0) {
                ids.add(value);
            }
        });

        const userIds = Array.from(ids);
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
            applyProfileMap(data.avatars);
        } catch (error) {
            console.error('Profile sync error:', error);
        } finally {
            inFlight = false;
        }
    }

    function broadcastProfileUpdate(profile) {
        try {
            localStorage.setItem(storageKey, JSON.stringify({
                updated_at: Date.now(),
                profile: profile || null,
            }));
        } catch (error) {
            // Ignore storage issues in private mode.
        }

        window.dispatchEvent(new CustomEvent('worklog:profile-updated', {
            detail: {
                profile: profile || null,
            },
        }));
    }

    window.WorkLogProfileSync = {
        refresh: refreshProfiles,
        broadcast: broadcastProfileUpdate,
    };

    window.WorkLogAvatarSync = window.WorkLogProfileSync;

    window.addEventListener('storage', (event) => {
        if (event.key === storageKey) {
            try {
                const payload = event.newValue ? JSON.parse(event.newValue) : null;
                if (payload && payload.profile && payload.profile.id) {
                    applyProfileMap({
                        [String(payload.profile.id)]: {
                            url: payload.profile.avatar_url,
                            name: payload.profile.name,
                            email: payload.profile.email,
                            updated_at: payload.profile.updated_at,
                        },
                    });
                }
            } catch (error) {
                // Ignore malformed localStorage payloads.
            }

            refreshProfiles();
        }
    });

    window.addEventListener('worklog:profile-updated', (event) => {
        const profile = event.detail && event.detail.profile ? event.detail.profile : null;
        if (profile && profile.id) {
            applyProfileMap({
                [String(profile.id)]: {
                    url: profile.avatar_url,
                    name: profile.name,
                    email: profile.email,
                    updated_at: profile.updated_at,
                },
            });
        }

        refreshProfiles();
    });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            refreshProfiles();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        refreshProfiles();

        if (refreshTimer) {
            clearInterval(refreshTimer);
        }

        refreshTimer = setInterval(refreshProfiles, refreshIntervalMs);
    });
})();
</script>
@endauth
