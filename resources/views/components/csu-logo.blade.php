@props(['class' => 'h-12 w-12'])

<svg class="{{ $class }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- Outer Gold Ring -->
    <circle cx="50" cy="50" r="48" fill="#F5B800" stroke="#3B060F" stroke-width="2" />
    <!-- Inner Deep Maroon Ring -->
    <circle cx="50" cy="50" r="42" fill="#6B0F1A" />
    <circle cx="50" cy="50" r="40" fill="none" stroke="#F5B800" stroke-width="1.5" stroke-dasharray="3 2" />
    
    <!-- Central Shield / Academic Book -->
    <path d="M50 20 L75 35 V62 L50 78 L25 62 V35 Z" fill="#FFFFFF" stroke="#F5B800" stroke-width="2" />
    
    <!-- Torch of Knowledge / Open Book -->
    <path d="M50 28 L68 40 V58 L50 68 L32 58 V40 Z" fill="#3B060F" />
    
    <!-- Flame (Gold) -->
    <path d="M50 25 C52 30, 56 32, 54 36 C52 40, 48 40, 46 36 C44 32, 48 30, 50 25 Z" fill="#F5B800" />
    
    <!-- Book Pages -->
    <path d="M36 48 Q 43 45 50 49 Q 57 45 64 48 V 56 Q 57 53 50 56 Q 43 53 36 56 Z" fill="#FFFFFF" stroke="#6B0F1A" stroke-width="1" />
    
    <!-- Star of Excellence -->
    <polygon points="50,12 52,17 57,17 53,20 55,25 50,22 45,25 47,20 43,17 48,17" fill="#F5B800" />
    
    <!-- Outer Text Ring Simulation -->
    <path d="M 18,50 A 32,32 0 1,1 82,50 A 32,32 0 1,1 18,50" fill="none" id="textPath" />
    <text font-family="Arial, sans-serif" font-size="5.5" font-weight="bold" fill="#FFFFFF" text-anchor="middle">
        <textPath href="#textPath" startOffset="50%">CAGAYAN STATE UNIVERSITY • LAL-LO CAMPUS</textPath>
    </text>
</svg>
