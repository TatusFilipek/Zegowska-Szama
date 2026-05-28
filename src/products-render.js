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
            const section = document.createElement('section');
            section.className = 'mb-4';

            const h = document.createElement('h2');
            h.className = 'display-6 border-bottom pb-2';
            h.style.color = '#4a5568';
            h.style.borderColor = '#3b4257';
            h.textContent = category;
            section.appendChild(h);

            const row = document.createElement('div');
            row.className = 'd-flex flex-row gap-3 pb-2';

            products.forEach(p => {
                const card = document.createElement('div');
                card.className = 'card border-0 flex-shrink-0';
                card.style.minWidth = '600px';

                const inner = `
                    <div class="row g-0 align-items-center">
                        <div class="col-auto" style="width:300px;">
                            <div style="width:300px; height:300px; overflow:hidden;">
                                <img src="${imageUrl(p.picture)}" class="img-fluid" style="object-fit:cover; width:100%; height:100%;" alt="${p.name}">
                            </div>
                        </div>
                        <div class="col ps-3">
                            <div style="font-size:48px; font-weight:600; line-height:1;">${p.name}</div>
                            <div style="font-size:32px; font-weight:400; color:#198754; margin-top:8px;">${p.discount}% OFF</div>
                            <div style="font-size:64px; font-weight:600; margin-top:12px;">${centsToPrice(p.applyDiscount())}</div>
                        </div>
                    </div>
                `;

                card.innerHTML = inner;
                row.appendChild(card);
            });

            section.appendChild(row);
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

            const h = document.createElement('h2');
            h.className = 'display-6 border-bottom pb-2';
            h.style.color = '#4a5568';
            h.style.borderColor = '#3b4257';
            h.textContent = category;
            section.appendChild(h);

            const scroller = document.createElement('div');
            scroller.className = 'd-flex overflow-x-auto gap-3 pb-2';

            products.forEach(p => {
                const item = document.createElement('div');
                item.style.minWidth = '140px';

                const inner = `
                    <div class="fw-bold text-dark mb-0">${p.name}</div>
                    <small class="d-block text-success mb-2" style="font-size: 0.75rem;">${p.discount}% OFF!</small>
                    <div style="width: 100%; aspect-ratio: 1/1; overflow:hidden;">
                        <img src="${imageUrl(p.picture)}" style="width:100%; height:100%; object-fit:cover;" alt="${p.name}">
                    </div>
                    <div class="mt-2 text-muted text-center fw-semibold">${centsToPrice(p.applyDiscount())}</div>
                `;

                item.innerHTML = inner;
                scroller.appendChild(item);
            });

            section.appendChild(scroller);
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
        } catch (err) {
            console.error(err);
        }
    }

    // expose for debugging
    window.ProductsRenderer = { loadAndRender };

    // auto-run after DOM ready
    document.addEventListener('DOMContentLoaded', loadAndRender);
})();