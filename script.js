document.addEventListener('DOMContentLoaded', () => {
    const stack = document.getElementById('stack');
    const progressBar = document.getElementById('progress-bar');
    const progressTrack = document.querySelector('.progress-track');
    const progressMarkers = document.getElementById('progress-markers');
    const decenaIndicator = document.getElementById('decena-indicator');
    const liveRegion = document.getElementById('live-region');
    const menuOverlay = document.getElementById('menu-overlay');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnMenu = document.getElementById('btn-menu');
    const btnRestart = document.getElementById('btn-restart');
    const STORAGE_KEY = 'coronilla-position-v3';

    let cards = Array.from(document.querySelectorAll('.card'));
    const totalCards = cards.length;
    const finalCard = cards[totalCards - 1];
    const finalCardMarkup = finalCard ? finalCard.innerHTML : '';
    let currentCardIndex = 0;

    // --- Estado de gestos ---
    let startX = 0, startY = 0, currentX = 0, currentY = 0;
    let startTime = 0;
    let isDragging = false;
    let isAnimating = false;
    let swipeAxis = null; // 'x' | 'y' | null
    const threshold = 80;

    const history = [];
    let hasCounted = false;

    // La Coronilla tiene 64 tarjetas; la última (índice 63) es el final.
    const FINAL_INDEX = totalCards - 1;
    const INCREMENT_AT = 60; // se registra una vez al llegar a las invocaciones finales

    // --- Inicialización de marcadores de decena ---
    function initMarkers() {
        if (!progressMarkers) return;
        // Marcadores en cada Grano Mayor (índices 4, 15, 26, 37, 48) y en el final.
        const markerIndexes = [4, 15, 26, 37, 48, FINAL_INDEX];
        markerIndexes.forEach(idx => {
            if (idx >= totalCards) return;
            const position = totalCards > 1 ? (idx / (totalCards - 1)) * 100 : 0;
            const marker = document.createElement('div');
            marker.className = 'marker';
            marker.style.left = `${position}%`;
            marker.dataset.index = String(idx);
            const label = document.createElement('span');
            label.className = 'marker-label';
            label.textContent = idx === FINAL_INDEX ? 'Fin' : `D${(idx - 4) / 11 + 1}`;
            marker.appendChild(label);
            progressMarkers.appendChild(marker);
        });
    }

    function announce(text) {
        if (liveRegion) liveRegion.textContent = text;
    }

    function updateProgress() {
        if (!progressBar || totalCards < 2) return;
        const progress = (currentCardIndex / (totalCards - 1)) * 100;
        progressBar.style.width = `${Math.min(progress, 100)}%`;
        if (progressTrack) {
            progressTrack.setAttribute('aria-valuenow', String(Math.round(progress)));
        }

        document.querySelectorAll('.marker').forEach(marker => {
            const markerIndex = parseInt(marker.dataset.index, 10);
            const active = currentCardIndex >= markerIndex;
            marker.classList.toggle('active', active);
            const label = marker.querySelector('.marker-label');
            if (label) label.classList.toggle('active', active);
        });

        // Indicador de decena
        if (decenaIndicator) {
            const card = cards[0];
            const decena = card ? card.dataset.decena : null;
            if (decena) {
                decenaIndicator.innerHTML = `Decena <strong>${decena}</strong> de 5`;
            } else if (currentCardIndex === FINAL_INDEX) {
                decenaIndicator.textContent = '';
            } else if (currentCardIndex <= 3) {
                decenaIndicator.textContent = 'Preparación';
            } else if (currentCardIndex >= 59 && currentCardIndex <= 61) {
                decenaIndicator.textContent = 'Invocaciones';
            } else if (currentCardIndex === 62) {
                decenaIndicator.textContent = 'Oración conclusión';
            } else {
                decenaIndicator.textContent = '';
            }
        }

        // Registrar el rezo una sola vez al llegar a las invocaciones finales.
        if (currentCardIndex >= INCREMENT_AT && !hasCounted) {
            hasCounted = true;
            fetch('counter.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'increment' })
            }).then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            }).then(data => {
                if (!data.success) throw new Error(data.error || 'Unknown counter error');
            }).catch(err => console.error('Error incrementing counter:', err));
        }
    }

    // --- Persistencia del progreso ---
    function saveProgress() {
        try { localStorage.setItem(STORAGE_KEY, String(currentCardIndex)); } catch (e) { /* noop */ }
    }

    function loadSavedIndex() {
        try {
            const saved = parseInt(localStorage.getItem(STORAGE_KEY), 10);
            if (!Number.isNaN(saved) && saved > 0 && saved < totalCards) return saved;
        } catch (e) { /* noop */ }
        return 0;
    }

    // --- Navegación ---
    // Invariante: history.length === currentCardIndex === número de tarjetas
    // descartadas de la pila. cards[0] es la tarjeta visible actual.
    function moveTo(index) {
        if (isAnimating) return;
        const target = Math.max(0, Math.min(totalCards - 1, index));
        if (target === currentCardIndex) return;

        if (target > currentCardIndex) {
            const delta = target - currentCardIndex;
            for (let i = 0; i < delta; i++) {
                // Consultar el DOM en cada iteración: la snapshot `cards` no
                // se actualiza al hacer remove().
                const card = stack.querySelector('.card');
                if (!card) break;
                history.push(card);
                card.remove();
            }
        } else {
            let steps = currentCardIndex - target;
            while (steps > 0 && history.length > 0) {
                const previousCard = history.pop();
                previousCard.style.display = 'flex';
                previousCard.style.transition = 'none';
                stack.prepend(previousCard);
                steps--;
            }
        }
        currentCardIndex = history.length;
        refreshState();
    }

    function nextCard() {
        if (currentCardIndex < FINAL_INDEX) moveTo(currentCardIndex + 1);
    }

    function prevCard() {
        if (currentCardIndex > 0) moveTo(currentCardIndex - 1);
    }

    function restart() {
        // Restaurar todas las tarjetas en orden original.
        while (history.length > 0) {
            const card = history.pop();
            card.style.display = 'flex';
            card.style.transition = 'none';
            stack.prepend(card);
        }
        // La tarjeta final se modifica para la pantalla de agradecimiento.
        // Recuperar su contenido original al reiniciar.
        const restoredFinal = stack.querySelector('[data-index="63"]');
        if (restoredFinal && finalCardMarkup) restoredFinal.innerHTML = finalCardMarkup;
        currentCardIndex = 0;
        hasCounted = false;
        localStorage.removeItem(STORAGE_KEY);
        refreshState();
    }

    // --- Render del stack ---
    function refreshState() {
        cards = Array.from(document.querySelectorAll('.card'));
        cards.forEach((card, index) => {
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.display = 'flex';
            if (index === 0) {
                card.style.zIndex = 3;
                card.style.transform = 'scale(1) translateY(0)';
                card.style.opacity = 1;
                card.focus({ preventScroll: true });
            } else if (index === 1) {
                card.style.zIndex = 2;
                card.style.transform = 'scale(0.96) translateY(8px)';
                card.style.opacity = 0.35;
            } else if (index === 2) {
                card.style.zIndex = 1;
                card.style.transform = 'scale(0.92) translateY(16px)';
                card.style.opacity = 0.12;
            } else {
                card.style.zIndex = 0;
                card.style.transform = 'scale(0.88) translateY(24px)';
                card.style.opacity = 0;
                card.style.display = 'none';
            }
        });

        btnPrev.disabled = currentCardIndex === 0;
        btnNext.disabled = currentCardIndex === FINAL_INDEX;

        if (currentCardIndex === FINAL_INDEX) {
            renderThanksCard();
        } else {
            const title = cards[0] ? cards[0].querySelector('.card-title').textContent : '';
            announce(`Tarjeta ${currentCardIndex + 1} de ${totalCards}: ${title}`);
        }
        updateProgress();
        saveProgress();
    }

    // --- Pantalla final ---
    function renderThanksCard() {
        const card = cards[0];
        if (!card) return;
        card.innerHTML = '';
        card.style.display = 'flex';
        card.style.flexDirection = 'column';
        card.style.justifyContent = 'center';

        const h2 = document.createElement('h2');
        h2.className = 'thanks-title';
        h2.textContent = '¡Gracias!';
        card.appendChild(h2);

        const p = document.createElement('p');
        p.className = 'thanks-text';
        p.textContent = 'Has completado la Coronilla. Que la misericordia de Dios te acompañe.';
        card.appendChild(p);

        const actions = document.createElement('div');
        actions.className = 'final-actions';

        const countP = document.createElement('p');
        countP.className = 'thanks-text';
        countP.id = 'count-text';
        countP.style.marginBottom = '0.5rem';
        actions.appendChild(countP);

        const restartBtn = document.createElement('button');
        restartBtn.className = 'btn btn-primary';
        restartBtn.textContent = 'Volver a rezar';
        restartBtn.addEventListener('click', restart);
        actions.appendChild(restartBtn);

        const installBtn = document.createElement('button');
        installBtn.className = 'btn btn-ghost';
        installBtn.id = 'install-btn';
        installBtn.textContent = 'Instalar aplicación';
        installBtn.style.display = window.deferredPrompt ? 'inline-flex' : 'none';
        installBtn.addEventListener('click', async () => {
            if (window.deferredPrompt) {
                window.deferredPrompt.prompt();
                await window.deferredPrompt.userChoice;
                window.deferredPrompt = null;
                installBtn.style.display = 'none';
            }
        });
        actions.appendChild(installBtn);

        const shareBtn = document.createElement('button');
        shareBtn.className = 'btn btn-ghost';
        shareBtn.textContent = 'Compartir';
        shareBtn.addEventListener('click', () => {
            const data = { title: 'Coronilla de la Divina Misericordia', text: 'Reza la Coronilla con esta aplicación', url: window.location.href };
            if (navigator.share) {
                navigator.share(data).catch(() => {});
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    shareBtn.textContent = 'Enlace copiado';
                    setTimeout(() => { shareBtn.textContent = 'Compartir'; }, 2000);
                });
            }
        });
        actions.appendChild(shareBtn);

        const credit = document.createElement('p');
        credit.className = 'thanks-text';
        credit.style.fontSize = '0.75rem';
        credit.style.color = 'var(--muted)';
        credit.style.marginTop = '0.5rem';
        const link = document.createElement('a');
        link.href = 'https://rafarq.com';
        link.target = '_blank';
        link.rel = 'noopener';
        link.style.color = 'var(--accent)';
        link.textContent = 'rafarq.com';
        credit.appendChild(document.createTextNode('Hecho con ♥ · '));
        credit.appendChild(link);
        actions.appendChild(credit);

        card.appendChild(actions);
        announce('Has completado la Coronilla. Gracias.');
        loadCount(countP);
    }

    function loadCount(countP) {
        fetch('counter.php?action=get')
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    countP.textContent = `${data.count} personas han rezado la Coronilla con esta aplicación.`;
                }
            })
            .catch(() => {});
    }

    // --- Instalación PWA ---
    // Mostrar el botón de instalación cuando el navegador lo permita.
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window.deferredPrompt = e;
        const btn = document.getElementById('install-btn');
        if (btn) btn.style.display = 'inline-flex';
    });

    // --- Gestos (distinguir scroll vertical de swipe horizontal) ---
    function handleStart(e) {
        if (isAnimating) return;
        if (e.target.closest && e.target.closest('button, a, input, textarea, select')) return;

        startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        startY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        currentX = startX;
        currentY = startY;
        startTime = Date.now();
        isDragging = true;
        swipeAxis = null;
        cards = Array.from(document.querySelectorAll('.card'));
        if (cards[0]) cards[0].style.transition = 'none';
    }

    function handleMove(e) {
        if (!isDragging) return;
        cards = Array.from(document.querySelectorAll('.card'));
        if (cards.length === 0) return;
        const card = cards[0];

        currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        currentY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        const diffX = currentX - startX;
        const diffY = currentY - startY;

        // Determinar el eje dominante una sola vez.
        if (!swipeAxis) {
            if (Math.abs(diffX) > 12 || Math.abs(diffY) > 12) {
                swipeAxis = Math.abs(diffX) > Math.abs(diffY) ? 'x' : 'y';
            }
        }

        // Si es vertical (lectura) dejamos scroll nativo y no interferimos.
        if (swipeAxis !== 'x') return;

        // Si es horizontal pero la tarjeta no está al final del scroll, priorizar la lectura.
        const isScrollable = card.scrollHeight > card.clientHeight + 4;
        if (isScrollable && card.scrollTop + card.clientHeight < card.scrollHeight - 8 && diffY !== 0) return;

        if (e.cancelable) e.preventDefault();
        const rotate = diffX * 0.05;
        card.style.transform = `translateX(${diffX}px) rotate(${rotate}deg)`;
        card.style.opacity = Math.max(0, 1 - (Math.abs(diffX) / 500));
    }

    function handleEnd() {
        if (!isDragging) return;
        isDragging = false;
        const wasAxis = swipeAxis;
        swipeAxis = null;

        cards = Array.from(document.querySelectorAll('.card'));
        const card = cards[0];
        const diffX = currentX - startX;
        const diffY = currentY - startY;
        const timeDiff = Date.now() - startTime;

        // Tap para desplazarse dentro de la tarjeta.
        if (wasAxis !== 'x' && Math.abs(diffX) < 10 && Math.abs(diffY) < 10 && timeDiff < 300) {
            if (card && card.scrollHeight > card.clientHeight) {
                if (card.scrollTop + card.clientHeight >= card.scrollHeight - 10) {
                    card.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    card.scrollBy({ top: 140, behavior: 'smooth' });
                }
            }
            startX = 0; currentX = 0;
            return;
        }

        // Swipe derecho: siguiente.
        if (wasAxis === 'x' && diffX > threshold && card) {
            isAnimating = true;
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.transform = `translateX(100vw) rotate(20deg)`;
            card.style.opacity = 0;
            setTimeout(() => {
                nextCard();
                isAnimating = false;
            }, 280);
        }
        // Swipe izquierdo: anterior.
        else if (wasAxis === 'x' && diffX < -threshold && history.length > 0) {
            prevCard();
        }
        // Revertir.
        else if (card) {
            card.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
            card.style.transform = 'scale(1) translateY(0)';
            card.style.opacity = 1;
        }

        startX = 0; currentX = 0;
    }

    // --- Eventos ---
    document.addEventListener('touchstart', handleStart, { passive: true });
    document.addEventListener('touchmove', handleMove, { passive: false });
    document.addEventListener('touchend', handleEnd, { passive: true });

    document.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);

    btnNext.addEventListener('click', nextCard);
    btnPrev.addEventListener('click', prevCard);
    btnRestart.addEventListener('click', () => { closeMenu(); restart(); });

    document.addEventListener('keydown', (e) => {
        if (menuOverlay.classList.contains('open')) {
            if (e.key === 'Escape') closeMenu();
            return;
        }
        if (e.key === 'ArrowRight') { e.preventDefault(); nextCard(); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); prevCard(); }
        else if (e.key === 'Home') { e.preventDefault(); moveTo(0); }
        else if (e.key === 'End') { e.preventDefault(); moveTo(FINAL_INDEX); }
    });

    // --- Menú de índice ---
    function openMenu() {
        menuOverlay.classList.add('open');
        menuOverlay.querySelector('button').focus();
    }
    function closeMenu() {
        menuOverlay.classList.remove('open');
        btnMenu.focus();
    }
    btnMenu.addEventListener('click', openMenu);
    menuOverlay.addEventListener('click', (e) => {
        if (e.target === menuOverlay) closeMenu();
    });
    menuOverlay.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => {
            moveTo(parseInt(item.dataset.jump, 10));
            closeMenu();
        });
    });
    document.querySelectorAll('.jump-row button').forEach(btn => {
        btn.addEventListener('click', () => moveTo(parseInt(btn.dataset.jump, 10)));
    });

    // --- Arranque ---
    initMarkers();

    // Restaurar posición guardada (o comenzar de cero).
    const saved = loadSavedIndex();
    if (saved > 0) {
        // Reconstruir el estado para llegar a la tarjeta guardada.
        moveTo(saved);
    }
    refreshState();
});
