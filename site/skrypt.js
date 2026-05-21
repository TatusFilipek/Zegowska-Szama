document.querySelectorAll('.category-section').forEach(section => {
            const container = section.querySelector('.scroll-container');
            const arrowLeft = section.querySelector('.scroll-arrow-left');
            const arrowRight = section.querySelector('.scroll-arrow-right');

            function updateArrows() {
                const scrollLeft = container.scrollLeft;
                const maxScrollLeft = container.scrollWidth - container.clientWidth;

                // Pokazuj lewą strzałkę tylko, gdy przewinięto w prawo (scrollLeft > 0)
                if (scrollLeft > 5) {
                    arrowLeft.classList.remove('d-none');
                } else {
                    arrowLeft.classList.add('d-none');
                }

                // Pokazuj prawą strzałkę tylko, gdy jest jeszcze co przewijać w prawo
                if (maxScrollLeft > scrollLeft + 5) {
                    arrowRight.classList.remove('d-none');
                } else {
                    arrowRight.classList.add('d-none');
                }
            }

            // Przewijanie w lewo
            arrowLeft.addEventListener('click', () => {
                container.scrollBy({ left: -200, behavior: 'smooth' });
            });

            // Przewijanie w prawo
            arrowRight.addEventListener('click', () => {
                container.scrollBy({ left: 200, behavior: 'smooth' });
            });

            // Reaguj na przewijanie palcem/myszką oraz zmiany rozmiaru okna
            container.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);
            
            // Inicjalne sprawdzenie po załadowaniu makiety
            setTimeout(updateArrows, 100);
        });