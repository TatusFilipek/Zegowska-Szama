(function () {
    const API_URL = 'api.php';

    function centsToPrice(cents) {
        return (cents / 100).toFixed(2) + '$';
    }

    function imageUrl(picture) {
        if (!picture) return '';
        if (/^https?:\/\//i.test(picture)) return picture;
        // if path contains a slash, use as-is
        if (picture.indexOf('/') !== -1) return picture;
        // if filename already has .png extension, point to photos/<name.png>
        if (/\.png$/i.test(picture)) return 'photos/' + picture;
        // otherwise point to local photos folder with added .png
        return 'photos/' + picture + '.png';
    }

    function groupByCategory(products) {
        const map = new Map();
        products.forEach(p => {
            const cat = p.category || 'Uncategorized';
            if (!map.has(cat)) map.set(cat, []);
            map.get(cat).push(p);
        });
        return map;
    }

    function renderDesktop(categoriesMap) {
        const container = document.getElementById('desktop-categories');
        if (!container) return;
        container.innerHTML = '';

        categoriesMap.forEach((products, category) => {
            const section = document.createElement('div');
            section.className = 'mb-5';

            const h = document.createElement('div');
            h.className = 'display-6 border-bottom pb-2 mb-4 text-capitalize catHeader';
            h.textContent = category;
            section.appendChild(h);

            const scrollWrapper = document.createElement('div');
            scrollWrapper.className = 'position-relative';
            scrollWrapper.style.display = 'flex';
            scrollWrapper.style.alignItems = 'center';
            scrollWrapper.style.width = '100%';
            
            const row = document.createElement('div');
            row.className = 'd-flex overflow-x-auto flex-nowrap gap-5 scroll-container pe-5';
            row.style.width = '100%';

            products.forEach(p => {
                const item = document.createElement('div');
                item.className = 'd-flex gap-3';
                item.style.minWidth = '360px';
                item.style.minHeight = '120px';

                const originalPrice = centsToPrice(p.getPrice());
                const discountedPrice = centsToPrice(p.applyDiscount());
                const priceDisplay = p.discount > 0 
                    ? `<div class="mt-2 fw-semibold fs-3"><span style="text-decoration: line-through; color: #999; margin-right: 8px;">${originalPrice}</span><span style="color: #dc2626;">${discountedPrice}</span></div>`
                    : `<div class="mt-2 text-muted fw-semibold fs-3">${originalPrice}</div>`;

                const inner = `
                    <div style="width: 120px; height: 120px; overflow: hidden;">
                        <img src="${imageUrl(p.picture)}" style="width: 120px; height: 120px; object-fit: cover; background-color: #3b4257; border-radius: 0.375rem;" alt="${p.name}">
                    </div>
                    <div>
                        <div class="fw-bold text-dark mb-0 fs-3">${p.name}</div>
                        <small class="d-block text-success mb-2 fs-6">${p.discount}% OFF!</small>
                        ${priceDisplay}
                    </div>
                `;

                item.innerHTML = inner;
                row.appendChild(item);
            });
            
            const arrowLeft = document.createElement('div');
            arrowLeft.className = 'scroll-arrow-left d-none position-absolute start-0 d-flex align-items-center h-100 top-0 ps-2';
            arrowLeft.style.cursor = 'pointer';
            arrowLeft.style.background = 'linear-gradient(270deg, transparent 0%, white 40%)';
            arrowLeft.style.zIndex = '5';
            arrowLeft.style.paddingRight = '20px';
            arrowLeft.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-left text-dark fw-bold" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>`;
            
            const arrowRight = document.createElement('div');
            arrowRight.className = 'scroll-arrow-right d-none position-absolute end-0 d-flex align-items-center h-100 top-0 pe-2';
            arrowRight.style.cursor = 'pointer';
            arrowRight.style.background = 'linear-gradient(90deg, transparent 0%, white 40%)';
            arrowRight.style.zIndex = '5';
            arrowRight.style.paddingLeft = '20px';
            arrowRight.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-right text-dark fw-bold" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>`;

            scrollWrapper.appendChild(arrowLeft);
            scrollWrapper.appendChild(row);
            scrollWrapper.appendChild(arrowRight);
            section.appendChild(scrollWrapper);
            container.appendChild(section);
        });
    }

    function renderMobile(categoriesMap) {
        const container = document.getElementById('mobile-categories');
        if (!container) return;
        container.innerHTML = '';

        categoriesMap.forEach((products, category) => {
            const section = document.createElement('section');
            section.className = 'mb-4';

            const h = document.createElement('div');
            h.className = 'display-6 border-bottom pb-2 mb-3 text-capitalize catHeader';
            h.textContent = category;
            section.appendChild(h);

            const scrollWrapper = document.createElement('div');
            scrollWrapper.className = 'position-relative';
            scrollWrapper.style.display = 'flex';
            scrollWrapper.style.alignItems = 'center';
            scrollWrapper.style.width = '100%';
            
            const scroller = document.createElement('div');
            scroller.className = 'd-flex overflow-x-auto flex-nowrap gap-3 scroll-container pe-5';
            scroller.style.width = '100%';

            products.forEach(p => {
                const item = document.createElement('div');
                item.style.minWidth = '120px';
                item.style.minHeight = '220px';
                item.style.display = 'flex';
                item.style.flexDirection = 'column';

                const originalPrice = centsToPrice(p.price_cents);
                const discountedPrice = centsToPrice(p.applyDiscount());
                const priceDisplay = p.discount > 0 
                    ? `<div style="font-size: 0.875rem; font-weight: 500;"><span style="text-decoration: line-through; color: #999; margin-right: 4px;">${originalPrice}</span><span style="color: #dc2626;">${discountedPrice}</span></div>`
                    : `<div class="text-muted fw-semibold" style="font-size: 0.875rem;">${originalPrice}</div>`;

                const inner = `
                    <div style="width: 100%; height: 120px; overflow: hidden; flex-shrink: 0;">
                        <img src="${imageUrl(p.picture)}" style="width:100%; height:100%; object-fit:cover; background-color: #3b4257;" alt="${p.name}">
                    </div>
                    <div style="height: 80px; display: flex; flex-direction: column; justify-content: space-between; padding-top: 8px;">
                        <div class="fw-bold text-dark mb-0" style="font-size: 0.875rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.2;">${p.name}</div>
                        <small class="d-block text-success" style="font-size: 0.75rem;">${p.discount}% OFF!</small>
                        ${priceDisplay}
                    </div>
                `;

                item.innerHTML = inner;
                scroller.appendChild(item);
            });

            const arrowLeft = document.createElement('div');
            arrowLeft.className = 'scroll-arrow-left d-none position-absolute start-0 d-flex align-items-center h-100 top-0 ps-2';
            arrowLeft.style.cursor = 'pointer';
            arrowLeft.style.background = 'linear-gradient(270deg, transparent 0%, white 40%)';
            arrowLeft.style.zIndex = '5';
            arrowLeft.style.paddingRight = '20px';
            arrowLeft.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-left text-dark fw-bold" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>`;
            
            const arrowRight = document.createElement('div');
            arrowRight.className = 'scroll-arrow-right d-none position-absolute end-0 d-flex align-items-center h-100 top-0 pe-2';
            arrowRight.style.cursor = 'pointer';
            arrowRight.style.background = 'linear-gradient(90deg, transparent 0%, white 40%)';
            arrowRight.style.zIndex = '5';
            arrowRight.style.paddingLeft = '20px';
            arrowRight.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-right text-dark fw-bold" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>`;

            scrollWrapper.appendChild(arrowLeft);
            scrollWrapper.appendChild(scroller);
            scrollWrapper.appendChild(arrowRight);
            section.appendChild(scrollWrapper);
            container.appendChild(section);
        });
    }

    function createProductInstances(raw) {
        return raw.map(r => new Product(r));
    }

    async function loadAndRender() {
        try {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error('Failed fetching products');
            const json = await res.json();
            // json might be object or array
            const arr = Array.isArray(json) ? json : [json];
            const products = createProductInstances(arr);
            const grouped = groupByCategory(products);
            renderDesktop(grouped);
            renderMobile(grouped);
            window.initializeScrollButtons?.();
        } catch (err) {
            console.error(err);
        }
    }

    // expose for debugging
    window.ProductsRenderer = { loadAndRender };

    // auto-run after DOM ready
    document.addEventListener('DOMContentLoaded', loadAndRender);
})();