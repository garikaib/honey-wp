
import gsap from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('bee-swarm');
    if (!container) return;

    // Randomize number of bees (15 to 25)
    const beeCount = gsap.utils.random(15, 25, 1);
    const bees = [];
    const containerRect = container.getBoundingClientRect();

    // Bee SVG template - Using same template
    const beeSVG = `
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-full h-full text-honey-500 opacity-80" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 8.6c-4.2.6-5.8 3.8-2.5 5.2 2.4 1 5 .2 5.9-2.2 M15.5 8.6c4.2.6 5.8 3.8 2.5 5.2-2.4 1-5 .2-5.9-2.2"/>
            <path fill="currentColor" opacity="0.8" d="M11.8 7.5c-1.8 0-3.2 1.8-3.2 4s1.4 4 3.2 4 3.2-1.8 3.2-4-1.4-4-3.2-4"/>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5V5 M9 6l1 1 M15 6l-1 1"/>
        </svg>
    `;

    // Initialize Bees
    for (let i = 0; i < beeCount; i++) {
        const bee = document.createElement('div');
        bee.innerHTML = beeSVG;
        bee.className = 'absolute w-8 h-8 pointer-events-none z-0';

        // Random start position
        const startX = gsap.utils.random(0, containerRect.width);
        const startY = gsap.utils.random(0, containerRect.height);

        gsap.set(bee, { x: startX, y: startY, scale: 0 });
        container.appendChild(bee);

        // Pop in animation
        gsap.to(bee, {
            scale: gsap.utils.random(0.8, 1.2),
            duration: 0.5,
            delay: gsap.utils.random(0, 1),
            ease: "back.out(1.7)"
        });

        bees.push({
            el: bee,
            x: startX,
            y: startY
        });

        animateBee(bee);
    }

    // Mouse Interaction
    let mouseX = containerRect.width / 2;
    let mouseY = containerRect.height / 2;

    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;

        // Interaction Logic
        bees.forEach(bee => {
            const dx = mouseX - gsap.getProperty(bee.el, "x");
            const dy = mouseY - gsap.getProperty(bee.el, "y");
            const distance = Math.sqrt(dx * dx + dy * dy);

            // Increased range (400) and stronger, faster pull
            if (distance < 400) {
                gsap.to(bee.el, {
                    x: "+=" + (dx * 0.15),
                    y: "+=" + (dy * 0.15),
                    rotation: Math.atan2(dy, dx) * (180 / Math.PI) + 90, // Face cursor
                    duration: 0.6, // Fast response
                    overwrite: "auto"
                });
            }
        });
    });

    function animateBee(bee) {
        const x = gsap.utils.random(0, container.clientWidth);
        const y = gsap.utils.random(0, container.clientHeight);

        const currentX = gsap.getProperty(bee, "x");
        const currentY = gsap.getProperty(bee, "y");
        const angle = Math.atan2(y - currentY, x - currentX) * (180 / Math.PI) + 90;

        // FAST Wander with rotation
        gsap.to(bee, {
            x: x,
            y: y,
            rotation: angle,
            duration: gsap.utils.random(3, 7), // Much faster random flight
            ease: "sine.inOut",
            onComplete: () => animateBee(bee)
        });

        // Faster Hover effect
        gsap.to(bee, {
            y: "+=15",
            yoyo: true,
            repeat: -1,
            duration: gsap.utils.random(0.5, 1.2), // Quick flutter
            ease: "sine.inOut"
        });
    }
});
