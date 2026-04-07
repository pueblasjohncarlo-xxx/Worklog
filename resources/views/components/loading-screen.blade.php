<div id="global-loader" class="fixed inset-0 z-[100] bg-indigo-950/80 backdrop-blur-md flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300">
    <div class="relative flex items-center justify-center">
        <!-- 3D Rotating Ring (Fluid Organic Shapes) -->
        <svg class="animate-spin absolute h-64 w-64" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" style="animation-duration: 8s;">
            <defs>
                <!-- Fluid Gradients -->
                <linearGradient id="gold-fluid" x1="0%" y1="100%" x2="0%" y2="0%">
                    <stop offset="0%" stop-color="#FFD700" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="#FDB931" stop-opacity="0.4" />
                </linearGradient>
                <linearGradient id="black-fluid" x1="0%" y1="100%" x2="0%" y2="0%">
                    <stop offset="0%" stop-color="#000000" stop-opacity="0.9" />
                    <stop offset="100%" stop-color="#2d2d2d" stop-opacity="0.5" />
                </linearGradient>
                
                <!-- Liquid Glow Filter -->
                <filter id="liquid-glow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="1.5" result="blur" />
                    <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 15 -5" result="goo" />
                    <feComposite in="SourceGraphic" in2="goo" operator="over" />
                    <feDropShadow dx="0" dy="0" stdDeviation="2" flood-color="rgba(255, 215, 0, 0.3)" />
                </filter>
            </defs>
            
            <g transform="translate(100, 100)" filter="url(#liquid-glow)">
                <!-- Ring of 12 Fluid Shapes (Teardrop/Citrus Segments) -->
                <!-- Gold Shapes (Even positions) -->
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(0)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(60)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(120)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(180)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(240)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#gold-fluid)" transform="rotate(300)" />

                <!-- Black Shapes (Odd positions) -->
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(30)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(90)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(150)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(210)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(270)" />
                <path d="M0,-55 C9,-60 14,-80 0,-95 C-14,-80 -9,-60 0,-55" fill="url(#black-fluid)" stroke="url(#gold-fluid)" stroke-width="1" stroke-opacity="0.5" transform="rotate(330)" />
            </g>
        </svg>
        
        <!-- Stationary Logo in White Container with Glow -->
        <div class="z-10 bg-white rounded-full p-8 shadow-[0_0_50px_rgba(255,255,255,0.8)] relative animate-pulse-slow">
             <x-wl-logo class="h-24 w-24 drop-shadow-md" />
        </div>
    </div>
</div>

<style>
    .animate-pulse-slow {
        animation: pulse-glow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 30px rgba(255,255,255,0.6); }
        50% { box-shadow: 0 0 60px rgba(255,255,255,0.9); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('global-loader');
        
        // Function to show loader
        window.showLoader = function() {
            loader.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100', 'pointer-events-auto');
            
            // Auto-hide after 2.5 seconds (2-3s range)
            setTimeout(() => {
                hideLoader();
            }, 2500);
        };

        // Function to hide loader
        window.hideLoader = function() {
            loader.classList.remove('opacity-100', 'pointer-events-auto');
            loader.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                loader.classList.add('hidden');
            }, 500); // 500ms fade out transition
        };

        // Initial Load - Show loader immediately then hide
        showLoader();

        // Show loader on link clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.href.startsWith('#') && !link.target && link.href !== window.location.href) {
                // Check if it's an internal link
                if (link.hostname === window.location.hostname) {
                    showLoader();
                }
            }
        });

        // Show loader on form submissions
        document.addEventListener('submit', function(e) {
            if (!e.target.hasAttribute('data-no-loader')) {
                showLoader();
            }
        });

        // Hide loader on back/forward cache restore (pageshow)
        window.addEventListener('pageshow', function(event) {
            hideLoader();
        });
    });
</script>
