(function () {
    const API_URL = 'api.php';
    const OFFERS_API_URL = 'offers-api.php';

    // Pomocnicza konwersja groszy/centów na format ceny
    function centsToPrice(cents) {
        return (cents / 100).toFixed(2) + '$';
    }

    // Pobieranie koszyka
    function getCart() {
        const stored = localStorage.getItem('zegowskaCart');
        if (!stored) return [];
        try {
            return JSON.parse(stored) || [];
        } catch (err) {
            return [];
        }
    }

    // Zapisywanie koszyka
    function saveCart(cart) {
        localStorage.setItem('zegowskaCart', JSON.stringify(cart));
    }

    // Modyfikacja ilości produktu (+1 / -1)
    function changeQuantity(productId, delta) {
        let cart = getCart();
        
        // Blokada zmiany ilości dla ofert (zawsze 1)
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
            loadAndRenderCheckout();
        }
    }

    // Funkcja usuwania oferty
    function removeOffer(offerId) {
        let cart = getCart();
        cart = cart.filter(item => item.id !== offerId);
        saveCart(cart);
        loadAndRenderCheckout();
    }

    // Główna funkcja renderująca widok zamówienia w NOWYM UKŁADZIE CSS GRID
    function renderCheckoutRows(cartItems, allProducts, allOffers) {
        const tableContainer = document.querySelector('.order-table-container');
        if (!tableContainer) return;

        // Szukamy sekcji przycisków akcji na dole (Order / Cancel)
        const actionButtonsContainer = document.querySelector('#checkout-content .d-flex.gap-3.mb-4');
        
        // Szukamy elementu zawierającego podsumowanie "Total price:"
        // Przeszukujemy divy wewnątrz #checkout-content, które zawierają tekst 'Total price:'
        const totalPriceContainer = Array.from(document.querySelectorAll('#checkout-content div')).find(div => 
            div.textContent.includes('Total price:') && div.children.length > 0
        );

        if (cartItems.length === 0) {
            tableContainer.innerHTML = '<div class="alert alert-warning text-center fs-5 mt-3">Twój koszyk jest pusty.</div>';
            
            // UKRYWAMY PRZYCISKI ORAZ CENĘ KOŃCOWĄ
            if (actionButtonsContainer) {
                actionButtonsContainer.style.setProperty('display', 'none', 'important');
            }
            if (totalPriceContainer) {
                totalPriceContainer.style.setProperty('display', 'none', 'important');
            }
            
            updateTotalPayment(0);
            return;
        }

        // JEŚLI KOSZYK NIE JEST PUSTY: Upewniamy się, że przyciski i cena są widoczne
        if (actionButtonsContainer) {
            actionButtonsContainer.style.setProperty('display', 'flex', 'important');
        }
        if (totalPriceContainer) {
            totalPriceContainer.style.setProperty('display', 'flex', 'important');
        }

        // Generujemy nagłówek nowej tabeli opartej na Gridzie
        let html = `
            <div class="order-table-header" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #ddd; color: #a0a0b0; font-size: 0.9rem; font-weight: 500; text-transform: uppercase;">
                <div>Name</div>
                <div>count</div>
                <div>price</div>
                <div></div>
            </div>
        `;

        let totalCents = 0;

        cartItems.forEach(item => {
            let itemObject = null;
            let isOffer = typeof item.id === 'string' && item.id.startsWith('offer_');

            if (isOffer) {
                // Mapowanie oferty
                const rawId = parseInt(item.id.replace('offer_', ''), 10);
                const rawOffer = allOffers.find(o => parseInt(o.id, 10) === rawId);
                if (rawOffer) {
                    itemObject = new Offer(rawOffer);
                }
            } else {
                // Mapowanie standardowego produktu
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

            // Renderowanie pojedynczego wiersza za pomocą CSS Grid
            html += `
                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;" class="text-capitalize">${itemObject.name}</div>
                    <div style="color: #a0a0b0;">${item.quantity}x</div>
                    <div style="color: #a0a0b0;">${centsToPrice(rowTotal)}</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        ${isOffer ? `
                            <button class="btn-remove-offer" data-id="${itemObject.id}" style="height: 36px; padding: 0 12px; background-color: #dc3545; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 0.9rem;">Usuń</button>
                        ` : `
                            <button class="action-btn-plus" data-id="${itemObject.id}" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                            <button class="action-btn-minus" data-id="${itemObject.id}" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                        `}
                    </div>
                </div>
            `;
        });

        tableContainer.innerHTML = html;
        
        // Aktualizacja sumy płatności
        updateTotalPayment(totalCents);

        // Podpięcie zdarzeń pod nowe przyciski plusów (+)
        tableContainer.querySelectorAll('.action-btn-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                changeQuantity(parseInt(this.getAttribute('data-id'), 10), 1);
            });
        });

        // Podpięcie zdarzeń pod nowe przyciski minusów (−)
        tableContainer.querySelectorAll('.action-btn-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                changeQuantity(parseInt(this.getAttribute('data-id'), 10), -1);
            });
        });

        // Podpięcie zdarzenia usuwania ofert specjalnych
        tableContainer.querySelectorAll('.btn-remove-offer').forEach(btn => {
            btn.addEventListener('click', function() {
                const offerId = this.getAttribute('data-id');
                if (confirm('Czy na pewno chcesz usunąć tę ofertę z koszyka?')) {
                    removeOffer(offerId);
                }
            });
        });
    }

    // Funkcja aktualizacji ceny dostosowana do nowego id i struktury
    function updateTotalPayment(totalCents) {
        const formattedPrice = centsToPrice(totalCents);

        // Krok 1: Aktualizacja ceny całkowitej w komponencie podsumowania
        const checkoutTotalEl = document.getElementById('checkout-total-price');
        if (checkoutTotalEl) {
            checkoutTotalEl.textContent = formattedPrice;
        }

        // Krok 2: Aktualizacja kwoty w sekcji płatności (krok 2 interfejsu zamówienia)
        const totalPaymentEl = document.querySelector('#payment-content .my-3 span');
        if (totalPaymentEl) {
            totalPaymentEl.textContent = formattedPrice;
        }
    }

    // Ładowanie danych z API i zestawianie ich z localStorage
    async function loadAndRenderCheckout() {
        try {
            const cartItems = getCart();

            // Równoległe pobieranie produktów i ofert
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

    // Inicjalizacja po załadowaniu drzewa DOM
    document.addEventListener('DOMContentLoaded', () => {
        loadAndRenderCheckout();

        // Obsługa przycisku "Cancel" (anulowanie i czyszczenie)
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Czy chcesz opróżnić koszyk i anulować zamówienie?')) {
                    localStorage.removeItem('zegowskaCart');
                    window.location.href = 'checkout.php';
                }
            });
        });
    });
})();