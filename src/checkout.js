(function () {
    const API_URL = 'api.php';

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

    // Główna funkcja renderująca widok zamówienia
    function renderCheckoutRows(cartItems, allProducts) {
        const tableContainer = document.querySelector('.order-table-container');
        if (!tableContainer) return;

        // Zachowujemy tylko nagłówek tabeli
        const header = tableContainer.querySelector('.order-table-header');
        tableContainer.innerHTML = '';
        if (header) tableContainer.appendChild(header);

        if (cartItems.length === 0) {
            const emptyMsg = document.createElement('div');
            emptyMsg.className = 'text-center py-5 text-muted fs-4';
            emptyMsg.textContent = 'Your cart is empty.';
            tableContainer.appendChild(emptyMsg);
            updatePricesDisplay(0);
            return;
        }

        let totalCartCents = 0;

        cartItems.forEach(cartItem => {
            // Znalezienie surowych danych w bazie produktów
            const rawProduct = allProducts.find(p => Number(p.id) === Number(cartItem.id));
            if (!rawProduct) return;

            // Tworzenie instancji zaimportowanej klasy Product
            const p = new window.Product(rawProduct);
            
            // Wyliczenie ceny z uwzględnieniem rabatu (w centach)
            const unitPriceCents = p.discount > 0 ? p.applyDiscount() : p.getPrice();
            const totalItemCents = unitPriceCents * cartItem.quantity;
            totalCartCents += totalItemCents;

            // Tworzenie wiersza dokładnie w strukturze CSS Grid z Twojego pliku checkout.php
            const row = document.createElement('div');
            row.className = 'order-table-row';
            row.style.display = 'grid';
            row.style.gridTemplateColumns = '2.5fr 1fr 1fr 1.2fr';
            row.style.gap = '1rem';
            row.style.padding = '0.75rem 0';
            row.style.borderBottom = '1px solid #eee';
            row.style.alignItems = 'center';

            row.innerHTML = `
                <div style="color: #2e3d52; font-weight: 500;">${p.name}</div>
                <div style="color: #a0a0b0;">${cartItem.quantity}x</div>
                <div style="color: #a0a0b0;">${centsToPrice(unitPriceCents)}</div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                    <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                </div>
            `;

            // Podpięcie akcji pod przyciski zwiększania i zmniejszania ilości
            row.querySelector('.action-btn-plus').addEventListener('click', () => changeQuantity(p.id, 1));
            row.querySelector('.action-btn-minus').addEventListener('click', () => changeQuantity(p.id, -1));

            tableContainer.appendChild(row);
        });

        updatePricesDisplay(totalCartCents);
    }

    // Aktualizacja łącznej kwoty w kroku 1 (Podsumowanie) oraz kroku 2 (Płatność)
    function updatePricesDisplay(totalCents) {
        const formattedPrice = centsToPrice(totalCents);

        // Krok 1: Total price
        const totalOrderEl = document.querySelector('.order-table-container + div span:last-child');
        if (totalOrderEl) {
            totalOrderEl.textContent = formattedPrice;
        }

        // Krok 2: Please pay X at the register
        const totalPaymentEl = document.querySelector('#payment-content .my-3 span');
        if (totalPaymentEl) {
            totalPaymentEl.textContent = formattedPrice;
        }
    }

    // Ładowanie danych z API i zestawianie ich z localStorage
    async function loadAndRenderCheckout() {
        try {
            const cartItems = getCart();

            const res = await fetch(API_URL);
            if (!res.ok) throw new Error('Problem z pobieraniem produktów z bazy.');
            
            const json = await res.json();
            const allProducts = Array.isArray(json) ? json : [json];

            renderCheckoutRows(cartItems, allProducts);
        } catch (err) {
            console.error('Błąd koszyka na checkoutcie:', err);
        }
    }

    // Inicjalizacja po załadowaniu drzewa DOM
    document.addEventListener('DOMContentLoaded', () => {
        loadAndRenderCheckout();

        // Obsługa przycisku "Cancel" (wyczyszczenie koszyka lub powrót)
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Czy chcesz opróżnić koszyk i anulować zamówienie?')) {
                    localStorage.removeItem('zegowskaCart');
                    window.location.href = 'index.php'; // Lub inna strona główna sklepu
                }
            });
        });
    });
})();