function initializeScrollButtons() {
    document.querySelectorAll('.scroll-container').forEach(container => {
        if (container.dataset.scrollButtonsInitiated === 'true') return;
        const wrapper = container.closest('.position-relative');
        if (!wrapper) return;
        
        const arrowLeft = wrapper.querySelector('.scroll-arrow-left');
        const arrowRight = wrapper.querySelector('.scroll-arrow-right');
        
        if (!arrowLeft || !arrowRight) return;

        function updateArrows() {
            const scrollLeft = container.scrollLeft;
            const maxScrollLeft = container.scrollWidth - container.clientWidth;

            if (scrollLeft > 5) {
                arrowLeft.classList.remove('d-none');
            } else {
                arrowLeft.classList.add('d-none');
            }

            if (maxScrollLeft > scrollLeft + 5) {
                arrowRight.classList.remove('d-none');
            } else {
                arrowRight.classList.add('d-none');
            }
        }

        arrowLeft.addEventListener('click', () => {
            container.scrollBy({ left: -200, behavior: 'smooth' });
        });

        arrowRight.addEventListener('click', () => {
            container.scrollBy({ left: 200, behavior: 'smooth' });
        });

        container.addEventListener('scroll', updateArrows);
        window.addEventListener('resize', updateArrows);

        container.dataset.scrollButtonsInitiated = 'true';
        setTimeout(updateArrows, 100);
    });
}

window.initializeScrollButtons = initializeScrollButtons;

initializeScrollButtons();
