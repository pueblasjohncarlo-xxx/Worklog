<div id="a11y-widget" class="fixed bottom-5 left-5 z-50 flex flex-col-reverse items-start gap-3">
    <!-- Main Trigger Button -->
    <button 
        id="a11y-trigger" 
        class="bg-indigo-600 text-white p-3 rounded-full shadow-xl hover:bg-indigo-700 hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 group"
        aria-label="Accessibility Tools"
        title="Accessibility Tools"
    >
        <!-- Icon: Accessibility / Universal Access -->
        <svg id="icon-closed" class="h-6 w-6 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /> 
        </svg>
        <!-- Icon: Close (Hidden by default) -->
        <svg id="icon-open" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Expanded Menu -->
    <div 
        id="a11y-menu" 
        class="flex flex-col gap-2 mb-2 transition-all duration-300 origin-bottom-left transform scale-0 opacity-0 absolute bottom-14 left-0"
    >
        <!-- Zoom Toggle Option -->
        <button 
            id="a11y-toggle-zoom"
            class="flex items-center gap-3 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors whitespace-nowrap group/item"
        >
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg text-indigo-600 dark:text-indigo-400 group-hover/item:bg-indigo-200 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                </svg>
            </div>
            <span class="text-sm font-bold">Toggle Text Size</span>
        </button>
    </div>
</div>

<style>
    /* Global Text Sizing */
    html {
        font-size: 16px;
        transition: font-size 0.3s ease;
    }
    html.text-xl-mode {
        font-size: 20px;
    }

    /* Animation Classes */
    .a11y-menu-open {
        transform: scale(1) !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .a11y-menu-closed {
        transform: scale(0.9) !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
</style>

<script>
    (function() {
        const trigger = document.getElementById('a11y-trigger');
        const menu = document.getElementById('a11y-menu');
        const zoomBtn = document.getElementById('a11y-toggle-zoom');
        const iconClosed = document.getElementById('icon-closed');
        const iconOpen = document.getElementById('icon-open');
        const html = document.documentElement;
        
        let isOpen = false;

        // 1. Load Preference
        if (localStorage.getItem('a11y-text-xl') === 'true') {
            html.classList.add('text-xl-mode');
        }

        // 2. Toggle Menu Logic
        trigger.addEventListener('click', () => {
            isOpen = !isOpen;
            toggleMenuState();
        });

        function toggleMenuState() {
            if (isOpen) {
                menu.classList.remove('scale-0', 'opacity-0');
                menu.classList.add('a11y-menu-open');
                iconClosed.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                trigger.classList.add('bg-gray-700');
                trigger.classList.remove('bg-indigo-600');
            } else {
                menu.classList.remove('a11y-menu-open');
                menu.classList.add('scale-0', 'opacity-0');
                iconClosed.classList.remove('hidden');
                iconOpen.classList.add('hidden');
                trigger.classList.remove('bg-gray-700');
                trigger.classList.add('bg-indigo-600');
            }
        }

        // 3. Zoom Toggle Logic
        zoomBtn.addEventListener('click', () => {
            html.classList.toggle('text-xl-mode');
            localStorage.setItem('a11y-text-xl', html.classList.contains('text-xl-mode'));
            
            // Optional: Close menu after selection? 
            // isOpen = false; 
            // toggleMenuState();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !menu.contains(e.target) && isOpen) {
                isOpen = false;
                toggleMenuState();
            }
        });
    })();
</script>
