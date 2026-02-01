
import gsap from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('hive-scene');
    if (!container) return;
    const containerRect = container.getBoundingClientRect();

    // --- 1. Buzzing Swarm around Hive ---
    const swarmContainer = document.getElementById('hive-swarm-group');
    if (swarmContainer) {
        const swarmSize = 30;
        for (let i = 0; i < swarmSize; i++) {
            const dot = document.createElementNS("http://www.w3.org/2000/svg", "circle");
            dot.setAttribute("r", gsap.utils.random(1, 2));
            dot.setAttribute("fill", "#F59E0B"); // Honey color
            dot.setAttribute("opacity", 0.6);

            swarmContainer.appendChild(dot);

            // Random buzzing motion around center
            gsap.to(dot, {
                x: "random(-20, 20)",
                y: "random(-15, 15)",
                duration: "random(0.2, 0.5)",
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
        }
    }

    // --- 2. The Strayed Bee (Wandering) ---
    // Bee SVG string
    const beeSVGString = `
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" class="text-honey-600">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 8.6c-4.2.6-5.8 3.8-2.5 5.2 2.4 1 5 .2 5.9-2.2 M15.5 8.6c4.2.6 5.8 3.8 2.5 5.2-2.4 1-5 .2-5.9-2.2"/>
            <path fill="currentColor" opacity="0.8" d="M11.8 7.5c-1.8 0-3.2 1.8-3.2 4s1.4 4 3.2 4 3.2-1.8 3.2-4-1.4-4-3.2-4"/>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5V5 M9 6l1 1 M15 6l-1 1"/>
        </svg>
    `;

    const strayedBee = document.createElement('div');
    strayedBee.innerHTML = beeSVGString;
    strayedBee.className = 'absolute w-12 h-12 z-20 pointer-events-none';
    container.appendChild(strayedBee);

    // Initial position: start near the hive
    const hiveX = containerRect.width / 2;
    const hiveY = 120 * (300 / 200); // Scale hive SVG y-coord to container height

    gsap.set(strayedBee, { x: hiveX, y: hiveY });
    gstray(strayedBee);

    function gstray(el) {
        // 40% chance to fly towards/around the hive, 60% to wander far
        const goHome = Math.random() < 0.4;

        let x, y;
        if (goHome) {
            // Target the hive area with some offset (buzzing around it)
            x = hiveX + gsap.utils.random(-60, 60);
            y = hiveY + gsap.utils.random(-60, 60);
        } else {
            // Wander far, potentially off-screen (-150 to width + 150)
            x = gsap.utils.random(-150, container.clientWidth + 150);
            y = gsap.utils.random(-80, container.clientHeight + 80);
        }

        const currentX = gsap.getProperty(el, "x");
        const currentY = gsap.getProperty(el, "y");

        // Face movement direction
        const angle = Math.atan2(y - currentY, x - currentX) * (180 / Math.PI) + 90;

        gsap.to(el, {
            x: x,
            y: y,
            rotation: angle,
            duration: gsap.utils.random(1.2, 2.5), // Much faster
            ease: "sine.inOut",
            onComplete: () => gstray(el)
        });

        // Faster bobbing
        gsap.to(el, {
            y: "+=15",
            yoyo: true,
            repeat: -1,
            duration: 0.5,
            ease: "sine.inOut"
        });
    }

    // --- 3. Hanging Hive Gentle Swing ---
    const hiveGroup = document.getElementById('hive-group');
    if (hiveGroup) {
        gsap.to(hiveGroup, {
            rotation: 2,
            transformOrigin: "top center",
            repeat: -1,
            yoyo: true,
            duration: 3,
            ease: "sine.inOut"
        });
    }
});
