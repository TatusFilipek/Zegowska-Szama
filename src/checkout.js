(function () {
    const API_URL = 'api.php';
    const OFFERS_API_URL = 'offers-api.php';
    const CHECKOUT_API_URL = 'checkout.php';

    let currentOrderId = null;
    let statusInterval = null; // Zmienna przechowująca interwał odpytywania

    function centsToPrice(cents) {
        return (cents / 100).toFixed(2) + '$';
    }

    function getCart() {
        const stored = localStorage.getItem('zegowskaCart');
        if (!stored) return [];
        try { return JSON.parse(stored) || []; } catch (err) { return []; }
    }

    function renderCheckoutRows(cartItems, allProducts, allOffers) {
        const tableContainer = document.querySelector('.order-table-container');
        if (!tableContainer) return;

        if (cartItems.length === 0) {
            tableContainer.innerHTML = '<div class="alert alert-warning text-center">Twój koszyk jest pusty!</div>';
            const submitBtn = document.getElementById('btn-submit-order');
            if (submitBtn) submitBtn.classList.add('disabled');
            return;
        }

        let totalCents = 0;
        let html = `
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 10px; border-bottom: 2px solid #2e3d52; padding-bottom: 8px; font-weight: bold; color: #2e3d52;">
                <div>Product</div>
                <div class="text-center">Price</div>
                <div class="text-center">Quantity</div>
                <div class="text-end">Total</div>
            </div>
        `;

        cartItems.forEach(item => {
            let name = 'Nieznany element';
            let unitPriceCents = 0;
            const isOffer = typeof item.id === 'string' && item.id.startsWith('offer_');

            if (isOffer) {
                const offerId = parseInt(item.id.replace('offer_', ''), 10);
                const foundOffer = allOffers.find(o => parseInt(o.id, 10) === offerId);
                if (foundOffer) {
                    name = foundOffer.name;
                    unitPriceCents = parseInt(foundOffer.price, 10);
                }
            } else {
                const foundProd = allProducts.find(p => parseInt(p.id, 10) === parseInt(item.id, 10));
                if (foundProd) {
                    name = foundProd.name;
                    const discount = parseInt(foundProd.discount_percent, 10) || 0;
                    const basePrice = parseInt(foundProd.price_cents, 10);
                    unitPriceCents = discount > 0 ? Math.round(basePrice * (1 - discount / 100)) : basePrice;
                }
            }

            const itemTotalCents = unitPriceCents * item.quantity;
            totalCents += itemTotalCents;

            html += `
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 10px; align-items: center; padding: 10px 0; border-bottom: 1px solid #cbd5e1;">
                    <div class="fw-semibold" style="color: #2e3d52;">${name}</div>
                    <div class="text-center">${centsToPrice(unitPriceCents)}</div>
                    <div class="text-center"><span>${item.quantity}</span></div>
                    <div class="text-end fw-bold" style="color: #5a8e7a;">${centsToPrice(itemTotalCents)}</div>
                </div>
            `;
        });

        tableContainer.innerHTML = html;

        const totalPaymentEl = document.getElementById('payment-total-amount');
        if (totalPaymentEl) {
            totalPaymentEl.textContent = centsToPrice(totalCents);
        }
    }

    async function loadAndRenderCheckout() {
        try {
            const cartItems = getCart();
            const [resProducts, resOffers] = await Promise.all([
                fetch(API_URL),
                fetch(OFFERS_API_URL)
            ]);
            if (!resProducts.ok || !resOffers.ok) return;
            renderCheckoutRows(cartItems, await resProducts.json(), await resOffers.json());
        } catch (err) {
            console.error(err);
        }
    }

    // --- FUNKCJA PRZEŁĄCZANIA WIDOKU NA PODSTAWIE STATUSU SQL (0, 1, 2, 3) ---
    function updateUIVisuallyByStatus(status) {
        const sections = {
            1: 'checkout-content',  // Nowe zamówienie
            2: 'proccessing-content',// Opłacone / Kuchnia (Zgodnie z Twoim panelem)
            3: 'collect-content'     // Gotowe do odbioru
        };

        // Wyjątek: Jeśli zamówienie ma status 1, ale koszyk został już wysłany, 
        // to znaczy, że klient czeka na płatność (krok 2 na frontendzie: payment-content)
        let activeId = sections[status];
        if (status === 1 && currentOrderId !== null) {
            activeId = 'payment-content';
        }

        if (!activeId) return;

        // Ukryj wszystkie sekcje i pokaż aktywną
        ['checkout-content', 'payment-content', 'proccessing-content', 'collect-content'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('d-none');
        });
        
        const activeEl = document.getElementById(activeId);
        if (activeEl) activeEl.classList.remove('d-none');

        // Wizualna aktualizacja kroku na pasku progresu (zmiana kolorów)
        updateProgressBar(status, activeId);
    }

    function updateProgressBar(status, activeId) {
        const step1 = document.querySelector('#step-icon-1 div');
        const step2 = document.querySelector('#step-icon-2 div');
        const step3 = document.querySelector('#step-icon-3 div');
        const step4 = document.querySelector('#step-icon-4 div');

        // Reset kolorów pasków
        [step1, step2, step3, step4].forEach(el => {
            if(el) {
                el.style.backgroundColor = '#3b4257';
                el.style.color = '#a2a2bd';
            }
        });

        if (activeId === 'checkout-content') {
            if(step1) { step1.style.backgroundColor = '#5a8e7a'; step1.style.color = 'white'; }
        } else if (activeId === 'payment-content') {
            if(step2) { step2.style.backgroundColor = '#5a8e7a'; step2.style.color = 'white'; }
        } else if (activeId === 'proccessing-content') {
            if(step3) { step3.style.backgroundColor = '#5a8e7a'; step3.style.color = 'white'; }
        } else if (activeId === 'collect-content') {
            if(step4) { step4.style.backgroundColor = '#5a8e7a'; step4.style.color = 'white'; }
            // Zamówienie skończone -> stopujemy odpytywanie
            clearInterval(statusInterval);
        }
    }

    // --- FUNKCJA ODPOWIEDZIALNA ZA CYKLICZNE ODPYTYWANIE BAZY ---
    function startOrderStatusPolling() {
        if (statusInterval) clearInterval(statusInterval);

        statusInterval = setInterval(async () => {
            if (!currentOrderId) return;

            try {
                const res = await fetch(`${CHECKOUT_API_URL}?action=check_status&order_id=${currentOrderId}`);
                if (!res.ok) return;

                const data = await res.json();
                if (data.success) {
                    // Wywołaj aktualizację widoku, jeżeli status zmienił się w panelu managera
                    updateUIVisuallyByStatus(data.status);
                }
            } catch (e) {
                console.error("Błąd odpytywania o status zamówienia: ", e);
            }
        }, 2000); // Odpytywanie co 2 sekundy
    }

    function setupWorkflow() {
        const submitOrderBtn = document.getElementById('btn-submit-order');
        if (submitOrderBtn) {
            submitOrderBtn.addEventListener('click', async () => {
                const cart = getCart();
                if (cart.length === 0) return;

                submitOrderBtn.disabled = true;

                try {
                    const response = await fetch(`${CHECKOUT_API_URL}?action=place_order`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cart: cart })
                    });
                    const result = await response.json();

                    if (response.ok && result.success) {
                        currentOrderId = result.order_id; 
                        localStorage.removeItem('zegowskaCart'); // Wyczyszczenie koszyka
                        
                        // Przejdź do kroku oczekiwania na płatność
                        updateUIVisuallyByStatus(1);

                        // Uruchom pętlę sprawdzającą zmiany z pliku manage.php
                        startOrderStatusPolling();
                    } else {
                        alert(result.error || 'Błąd przy składaniu zamówienia.');
                        submitOrderBtn.disabled = false;
                    }
                } catch (e) {
                    alert('Błąd sieci.');
                    submitOrderBtn.disabled = false;
                }
            });
        }

        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Czy chcesz anulować transakcję?')) {
                    localStorage.removeItem('zegowskaCart');
                    window.location.href = 'main.php';
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAndRenderCheckout();
        setupWorkflow();
    });
})();