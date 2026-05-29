(function () {
    const API_URL = 'api.php';
    const OFFERS_API_URL = 'offers-api.php'; // Adres do nowego API ofert

    function centsToPrice(cents) {
        return (cents / 100).toFixed(2) + '$';
    }

    function getCart() {
        const stored = localStorage.getItem('zegowskaCart');
        if (!stored) return [];
        try { return JSON.parse(stored) || []; } catch (err) { return []; }
    }

    function saveCart(cart) {
        localStorage.setItem('zegowskaCart', JSON.stringify(cart));
    }

    function changeQuantity(productId, delta) {
        let cart = getCart();
        
        // Zabezpieczenie: Jeśli ktoś spróbuje zmienić ilość oferty przez tę funkcję, ignorujemy
        if (typeof productId === 'string' && productId.startsWith('offer_')) {
            return; 
        }

        const entry = cart.find(item => item.id === productId);
        if (entry) {
            entry.quantity += delta;
            if (entry.quantity <= 0) {
                cart = cart.filter(item => item.id !== productId);
            }
            saveCart(cart);
            loadAndRenderCheckout(); // Ponowne przeładowanie widoku
        }
    }

    // NOWA FUNKCJA: Całkowite usuwanie oferty z koszyka
    function removeOffer(offerId) {
        let cart = getCart();
        // Odfiltrowujemy (usuwamy) ofertę o danym ID z koszyka
        cart = cart.filter(item => item.id !== offerId);
        saveCart(cart);
        loadAndRenderCheckout(); // Ponowne przeładowanie widoku
    }

    // GŁÓWNA FUNKCJA RENDERUJĄCA W checkout.js
    function renderCheckoutRows(cartItems, allProducts, allOffers) {
        const tableContainer = document.querySelector('.order-table-container');
        if (!tableContainer) return;

        if (cartItems.length === 0) {
            tableContainer.innerHTML = '<div class="alert alert-warning text-center fs-5">Twój koszyk jest pusty.</div>';
            updateTotalPayment(0);
            return;
        }

        let html = `
            <table class="table table-dark table-striped align-middle fs-5" style="border-radius: 12px; overflow: hidden;">
                <thead>
                    <tr>
                        <th>Produkt / Oferta</th>
                        <th class="text-center" style="width: 150px;">Ilość</th>
                        <th class="text-end" style="width: 150px;">Cena</th>
                    </tr>
                </thead>
                <tbody>
        `;

        let totalCents = 0;

        cartItems.forEach(item => {
            let itemObject = null;
            let isOffer = typeof item.id === 'string' && item.id.startsWith('offer_');

            if (isOffer) {
                // LOGIKA DLA OFERT
                const rawId = parseInt(item.id.replace('offer_', ''), 10);
                const rawOffer = allOffers.find(o => parseInt(o.id, 10) === rawId);
                if (rawOffer) {
                    itemObject = new Offer(rawOffer);
                }
            } else {
                // LOGIKA DLA PRODUKTÓW
                const productId = parseInt(item.id, 10);
                const rawProduct = allProducts.find(p => p.id === productId);
                if (rawProduct) {
                    itemObject = new Product(rawProduct);
                }
            }

            if (!itemObject) return;

            const unitPrice = itemObject.applyDiscount(); 
            const rowTotal = unitPrice * item.quantity;
            totalCents += rowTotal;

            html += `
                    <tr>
                        <td>
                            <div class="fw-bold text-capitalize ${isOffer ? 'text-success' : ''}">
                                ${itemObject.name} 
                                ${isOffer ? '<span class="badge bg-secondary fs-6 ms-2">Oferta</span>' : ''}
                            </div>
                            <small class="text-muted text-lowercase">${itemObject.category}</small>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                ${isOffer ? `
                                    <span class="fw-bold px-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-danger btn-remove-offer ms-2" 
                                            data-id="${itemObject.id}" 
                                            style="padding: 2px 8px; font-size: 0.85rem;" 
                                            title="Usuń ofertę">
                                        Usuń
                                    </button>
                                ` : `
                                    <button class="btn btn-sm btn-outline-light btn-minus" data-id="${itemObject.id}" style="padding: 0px 8px;">-</button>
                                    <span class="fw-bold px-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-light btn-plus" data-id="${itemObject.id}" style="padding: 0px 8px;">+</button>
                                `}
                            </div>
                        </td>
                        <td class="text-end fw-bold">${centsToPrice(rowTotal)}</td>
                    </tr>
                `;
        })

        html += `</tbody></table>`;
        tableContainer.innerHTML = html;
        updateTotalPayment(totalCents);

        // Podpięcie eventów pod przyciski ilości dla zwykłych produktów
        tableContainer.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                changeQuantity(parseInt(this.getAttribute('data-id'), 10), 1);
            });
        });

        tableContainer.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                changeQuantity(parseInt(this.getAttribute('data-id'), 10), -1);
            });
        });

        tableContainer.querySelectorAll('.btn-remove-offer').forEach(btn => {
            btn.addEventListener('click', function() {
                const offerId = this.getAttribute('data-id'); // Pobieramy tekstowe ID np. 'offer_1'
                if (confirm('Czy na pewno chcesz usunąć tę ofertę specjalną z koszyka?')) {
                    removeOffer(offerId);
                }
            });
        });
    }

    function updateTotalPayment(totalCents) {
        const formattedPrice = centsToPrice(totalCents);

        // Krok 1: Aktualizacja ceny na podsumowaniu zamówienia (używamy nowego ID)
        const checkoutTotalEl = document.getElementById('checkout-total-price');
        if (checkoutTotalEl) {
            checkoutTotalEl.textContent = formattedPrice;
        }

        // Krok 2: Aktualizacja ceny w sekcji płatności (Please pay X at the register)
        // Najpierw szukamy dedykowanego ID, jeśli nie ma - wracamy do starego selektora
        let totalPaymentEl = document.getElementById('payment-total-price');
        if (!totalPaymentEl) {
            totalPaymentEl = document.querySelector('#payment-content .my-3 span');
        }
        
        if (totalPaymentEl) {
            totalPaymentEl.textContent = formattedPrice;
        }
    }

    // ŁADOWANIE DANYCH Z DWÓCH INTERFEJSÓW API RÓWNOLEGLE
    async function loadAndRenderCheckout() {
        try {
            const cartItems = getCart();

            // Pobieramy produkty oraz oferty w tym samym czasie
            const [resProducts, resOffers] = await Promise.all([
                fetch(API_URL),
                fetch(OFFERS_API_URL)
            ]);

            if (!resProducts.ok || !resOffers.ok) throw new Error('Problem z pobieraniem danych z bazy.');
            
            const allProducts = await resProducts.json();
            const allOffers = await resOffers.json();

            renderCheckoutRows(cartItems, allProducts, allOffers);
        } catch (err) {
            console.error('Błąd koszyka na checkoutcie:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAndRenderCheckout();
        // ... reszta Twoich zdarzeń (np. .btn-cancel) ...
    });
})();