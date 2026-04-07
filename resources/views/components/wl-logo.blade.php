<svg {{ $attributes }} viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="15" result="coloredBlur"/>
            <feMerge>
                <feMergeNode in="coloredBlur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
        
        <linearGradient id="gradW" x1="120" y1="120" x2="320" y2="270" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#4F46E5"/>
            <stop offset="100%" stop-color="#312E81"/>
        </linearGradient>
        
        <linearGradient id="gradL" x1="320" y1="120" x2="420" y2="270" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stop-color="#2DD4BF"/>
            <stop offset="100%" stop-color="#0D9488"/>
        </linearGradient>
    </defs>

    <!-- W Shape (Gradient Dark Purple) -->
    <path d="M120 120 L170 270 L220 170 L270 270 L320 120" 
          stroke="url(#gradW)" 
          stroke-width="50" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>
    
    <!-- L Shape (Gradient Teal) -->
    <path d="M320 120 V270 H420" 
          stroke="url(#gradL)" 
          stroke-width="50" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>
          
    <!-- Pulse Line (White Outline for Cutout) -->
    <path d="M220 220 L250 160 L280 220" 
          stroke="white" 
          stroke-width="25" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>
          
    <!-- Pulse Line (Purple) -->
    <path d="M220 220 L250 160 L280 220" 
          stroke="#312E81" 
          stroke-width="15" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>

    <!-- Green Checkmark -->
    <path d="M280 220 L320 140 L360 100" 
          stroke="#84CC16" 
          stroke-width="30" 
          stroke-linecap="round" 
          stroke-linejoin="round"/>
</svg>
