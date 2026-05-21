document.querySelectorAll('.category-section').forEach(section => {
            const container = section.querySelector('.scroll-container');
            const arrow = section.querySelector('.scroll-arrow');

            // Funkcja sprawdzająca czy zawartość się przepełnia
            function checkOverflow() {
                if (container.scrollWidth > container.clientWidth) {
                    arrow.classList.remove('d-none');
                } else {
                    arrow.classList.add('d-none');
                }
            }

            // Obsługa kliknięcia w strzałkę (przewijanie o 200 pikseli)
            arrow.addEventListener('click', () => {
                container.scrollBy({ left: 200, behavior: 'smooth' });
            });

            // Sprawdzaj przepełnienie przy ładowaniu i zmianie rozmiaru okna
            window.addEventListener('resize', checkOverflow);
            // Mały timeout, żeby Bootstrap zdążył wyrenderować szerokości
            setTimeout(checkOverflow, 100);
        });