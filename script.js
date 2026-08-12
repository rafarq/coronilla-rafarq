document.addEventListener('DOMContentLoaded', () => {
    const stack = document.getElementById('stack');
    let cards = Array.from(document.querySelectorAll('.card'));
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let currentY = 0;
    let startTime = 0;
    let isDragging = false;
    let isAnimating = false;
    const threshold = 100; // Minimum distance to trigger swipe

    const totalCards = document.querySelectorAll('.card').length;
    let currentCardIndex = 0;
    const progressBar = document.getElementById('progress-bar');
    const progressMarkers = document.getElementById('progress-markers');

    let hasCounted = false;

    function initMarkers() {
        const allCards = document.querySelectorAll('.card');
        allCards.forEach((card, index) => {
            const title = card.querySelector('h2').textContent;
            // Check for Grano Mayor or specific ID 60 (Invocación 1)
            // Note: ID is not directly on the card element as an attribute in the PHP loop, 
            // but we can infer it or check the title/index if we know the order.
            // Better to check the data-index or just the title/content if unique enough.
            // The user said "card with id 60". In the JSON, id 60 is "Invocación 1".
            // Let's check the data-index from the PHP loop.

            const cardId = parseInt(card.getAttribute('data-index')) + 1; // data-index is 0-based from array
            // The JSON IDs match the 1-based index mostly, but let's rely on the JSON content if possible.
            // Actually, the PHP loop uses the array index as data-index.
            // Let's assume the JSON IDs correspond to the order.

            let labelText = '';
            if (title.includes('Grano Mayor')) {
                const match = title.match(/(\d+)$/);
                labelText = match ? `G${match[1]}` : 'GM';
            } else if (cardId === 60) {
                labelText = 'F';
            }

            if (labelText) {
                const position = (index / (totalCards - 1)) * 100;
                const marker = document.createElement('div');
                // Base classes
                marker.className = 'marker absolute top-1/2 -translate-y-1/2 w-1 h-3 bg-gray-400 rounded-full transition-colors duration-300';
                marker.style.left = `${position}%`;
                marker.dataset.index = index; // Store index for active check

                const label = document.createElement('div');
                label.className = 'marker-label absolute -top-4 left-1/2 -translate-x-1/2 text-[0.5rem] text-gray-500 font-bold whitespace-nowrap transition-colors duration-300';
                label.textContent = labelText;

                marker.appendChild(label);
                progressMarkers.appendChild(marker);
            }
        });
    }

    function updateProgress() {
        if (!progressBar || totalCards < 2) return;
        // Calculate progress
        const progress = ((currentCardIndex) / (totalCards - 1)) * 100;
        progressBar.style.width = `${Math.min(progress, 100)}%`;

        // Update markers active state
        const markers = document.querySelectorAll('.marker');
        markers.forEach(marker => {
            const markerIndex = parseInt(marker.dataset.index);
            const label = marker.querySelector('.marker-label');

            if (currentCardIndex >= markerIndex) {
                marker.classList.remove('bg-gray-400');
                marker.classList.add('bg-blue-600');

                label.classList.remove('text-gray-500');
                label.classList.add('text-blue-600');
            } else {
                marker.classList.add('bg-gray-400');
                marker.classList.remove('bg-blue-600');

                label.classList.add('text-gray-500');
                label.classList.remove('text-blue-600');
            }
        });

        // Trigger counter at 95% (approx index 60 out of 64)
        if (currentCardIndex >= 60 && !hasCounted) {
            hasCounted = true;
            fetch('counter.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'increment' })
            }).then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            }).then(data => {
                if (!data.success) throw new Error(data.error || 'Unknown counter error');
            }).catch(err => console.error('Error incrementing counter:', err));
        }
    }

    function updateStack() {
        cards = Array.from(document.querySelectorAll('.card'));
        cards.forEach((card, index) => {
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.display = 'flex'; // Ensure visible

            if (index === 0) {
                card.style.zIndex = 3;
                card.style.transform = 'scale(1) translateY(0)';
                card.style.opacity = 1;

                // Check if this is the last card (index 63 in 0-based array of 64 cards)
                // The card element will have data-index="63"
                if (card.getAttribute('data-index') === '63') {
                    fetch('counter.php?action=get')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const bodyP = card.querySelector('p');
                                if (bodyP) {
                                    // Keep original text or replace? User said "muestres el texto XXX personas..."
                                    // Let's append or replace. The original text is "En el nombre del Padre..."
                                    // Maybe replace the body or append below it.
                                    // User request: "en la ultima tarjeta muestres el texto XXX personas rezaron la Coronilla con esta app. Y un botón..."

                                    // Let's clear the current body and add the new content
                                    card.innerHTML = '';

                                    const h2 = document.createElement('h2');
                                    h2.className = 'text-2xl font-semibold mb-6 text-center text-gray-900';
                                    h2.textContent = '¡Gracias!';
                                    card.appendChild(h2);

                                    const p = document.createElement('p');
                                    p.className = 'text-lg text-center leading-relaxed text-gray-600 whitespace-pre-line mb-8';
                                    p.textContent = `${data.count} personas rezaron la Coronilla con esta app.`;
                                    card.appendChild(p);

                                    const btn = document.createElement('button');
                                    btn.className = 'bg-blue-600 text-white px-6 py-3 rounded-full font-semibold shadow-lg hover:bg-blue-700 transition-colors';
                                    btn.textContent = 'Volver a rezar';
                                    btn.onclick = () => window.location.reload();
                                    card.appendChild(btn);
                                }
                            }
                        })
                        .catch(err => console.error('Error fetching count:', err));
                }

            } else if (index === 1) {
                card.style.zIndex = 2;
                card.style.transform = 'scale(0.95) translateY(10px)';
                card.style.opacity = 0.4; // Updated transparency
            } else if (index === 2) {
                card.style.zIndex = 1;
                card.style.transform = 'scale(0.9) translateY(20px)';
                card.style.opacity = 0.2; // Updated transparency
            } else {
                card.style.zIndex = 0;
                card.style.transform = 'scale(0.85) translateY(30px)';
                card.style.opacity = 0;
                card.style.display = 'none'; // Hide others for performance
            }
        });
        updateProgress();
    }

    function handleStart(e) {
        if (isAnimating) return;
        // Ignore clicks on buttons or interactive elements if any (none for now)
        // Allow button clicks on the last card
        if (e.target.closest && e.target.closest('button, a, input, textarea, select')) return;

        startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        startY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        currentX = startX;
        currentY = startY;
        startTime = new Date().getTime();
        isDragging = true;

        // Target the top card
        cards = Array.from(document.querySelectorAll('.card'));
        if (cards.length > 0) {
            const card = cards[0];
            card.style.transition = 'none';
        }
    }

    const history = []; // Stack to store removed cards

    function handleMove(e) {
        if (!isDragging) return;

        // Prevent default scrolling
        if (e.cancelable) e.preventDefault();

        // Target the top card
        cards = Array.from(document.querySelectorAll('.card'));
        if (cards.length === 0 && history.length === 0) return; // No cards and no history

        const card = cards[0];

        currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        currentY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        const diffX = currentX - startX;

        // Swipe Right (Next Card)
        if (diffX > 0 && card) {
            const rotate = diffX * 0.05;
            card.style.transform = `translateX(${diffX}px) rotate(${rotate}deg)`;
            card.style.opacity = 1 - (diffX / 500);
        }
        // Swipe Left (Previous Card) - Only if there is history
        else if (diffX < 0 && history.length > 0) {
            // We don't move the current card, we might want to show a hint of the previous card coming back?
            // Or we just detect the swipe left to trigger the restore.
            // Let's keep it simple: if swipe left > threshold, restore.
            // Visual feedback for left swipe is tricky without the card being there.
            // Maybe we can just track the swipe and trigger on end.
        }
    }

    function handleEnd(e) {
        if (!isDragging || isAnimating) return;
        isDragging = false;

        cards = Array.from(document.querySelectorAll('.card'));
        const card = cards[0];
        const diffX = currentX - startX;

        const diffY = currentY - startY;
        const timeDiff = new Date().getTime() - startTime;

        // Tap detection
        if (Math.abs(diffX) < 10 && Math.abs(diffY) < 10 && timeDiff < 300) {
            if (card.scrollHeight > card.clientHeight) {
                // Check if we are at the bottom
                if (card.scrollTop + card.clientHeight >= card.scrollHeight - 10) {
                    card.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    // Scroll down by approx 4 lines (assuming ~30px per line -> 120px)
                    card.scrollBy({ top: 120, behavior: 'smooth' });
                }
            }
            return;
        }

        // Swipe Right (Next)
        if (diffX > threshold && card) {
            isAnimating = true;
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.transform = `translateX(100vw) rotate(20deg)`;
            card.style.opacity = 0;

            setTimeout(() => {
                history.push(card); // Save to history
                card.remove(); // Remove from DOM
                currentCardIndex++;
                updateStack();
                isAnimating = false;
            }, 300);
        }
        // Swipe Left (Back)
        else if (diffX < -threshold && history.length > 0) {
            const previousCard = history.pop();
            const stack = document.getElementById('stack');

            // Reset styles for re-entry
            previousCard.style.transition = 'none';
            previousCard.style.transform = 'translateX(100vw) rotate(20deg)';
            previousCard.style.opacity = '0';
            previousCard.style.display = 'flex';

            stack.prepend(previousCard); // Add back to top of stack

            // Trigger reflow
            void previousCard.offsetWidth;

            // Animate in
            previousCard.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            previousCard.style.transform = 'scale(1) translateY(0)';
            previousCard.style.opacity = '1';

            currentCardIndex--;
            updateStack();
        }
        // Revert (Not enough swipe)
        else if (card) {
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.transform = 'scale(1) translateY(0)';
            card.style.opacity = 1;
        }

        // Reset values
        startX = 0;
        currentX = 0;
    }

    // Global listeners
    document.addEventListener('touchstart', handleStart);
    document.addEventListener('touchmove', handleMove, { passive: false }); // Passive false for preventDefault
    document.addEventListener('touchend', handleEnd);

    document.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);

    // Initialize
    initMarkers();
    updateStack();
});
