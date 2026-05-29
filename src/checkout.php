<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php require_once __DIR__ . '/header.php'; ?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-flex flex-column align-items-center">
        <!-- Progress bar -->
        <div class="mb-2 w-100" style="max-width: 600px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #2e3d52;">Checkout</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #5a8e7a; color: white; font-weight: bold;">1</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #5a8e7a;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #2e3d52;">Payment</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #37645D; color: white; font-weight: bold;">2</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #5a8e7a;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #a0a0b0;">Processing</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #3b4257; color: #a2a2bd; font-weight: bold;">3</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #c9a3a3;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #a0a0b0;">Collect</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #7a7a8e; color: white; font-weight: bold;">4</div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="checkout-content" class="content-section vh-100 d-flex flex-column" style="width: 100%; max-width: 650px;">
            <h2 class="fw-bold mb-4 text-start pb-2 ps-1" style="color: #2e3d52; border-bottom: 1px solid #2e3d52; font-size: 2rem;">Your order</h2>
            
            <div class="order-table-container flex-fill">
                <div class="order-table-header" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #ddd; color: #a0a0b0; font-size: 0.9rem; font-weight: 500; text-transform: uppercase;">
                    <div>Name</div>
                    <div>count</div>
                    <div>price</div>
                    <div></div>
                </div>

                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;">Product 1</div>
                    <div style="color: #a0a0b0;">1x</div>
                    <div style="color: #a0a0b0;">0.99$</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                        <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                    </div>
                </div>

                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;">Product 2</div>
                    <div style="color: #a0a0b0;">2x</div>
                    <div style="color: #a0a0b0;">0.99$</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                        <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                    </div>
                </div>

                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;">Product 3</div>
                    <div style="color: #a0a0b0;">4x</div>
                    <div style="color: #a0a0b0;">0.99$</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                        <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                    </div>
                </div>

                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;">Product 4</div>
                    <div style="color: #a0a0b0;">8x</div>
                    <div style="color: #a0a0b0;">0.99$</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                        <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                    </div>
                </div>

                <div class="order-table-row" style="display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.2fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="color: #2e3d52;">Product 5</div>
                    <div style="color: #a0a0b0;">16x</div>
                    <div style="color: #a0a0b0;">0.99$</div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button class="action-btn-plus" style="width: 36px; height: 36px; background-color: #5a8e7a; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">+</button>
                        <button class="action-btn-minus" style="width: 36px; height: 36px; background-color: #3b4257; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">−</button>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center; align-items: center; padding: 1.5rem 0; color: #a0a0b0;">
                <span style="margin-right: 1rem;">Total price:</span>
                <span id="checkout-total-price" style="font-size: 1.6rem; font-weight: bold; color: #2e3d52;">5.99$</span>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button class="btn-next" style="flex: 1; padding: 0.75rem 1.5rem; background-color: #5a8e7a; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 1rem; cursor: pointer;">Order</button>
                <button class="btn-cancel" style="flex: 1; padding: 0.75rem 1.5rem; background-color: #3b4257; color: #a2a2bd; border: none; border-radius: 12px; font-weight: bold; font-size: 1rem; cursor: pointer;">Cancel</button>
            </div>
        </div>

        <div id="payment-content" class="content-section d-none vh-100 d-flex flex-column" style="width: 100%; max-width: 650px;">
            <div class="flex-fill">
            <div class="fw-bold fs-2 text-center my-3">Please pay <span id="checkout-total-price" style="color: #87718B;">5.99$</span> at the register</div>
            <div class="fw-bold fs-2 text-center my-5">An employee will confirm your <span style="color: #87718B;">payment</span></div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button class="btn-next" style="flex: 1; padding: 0.75rem 1.5rem; background-color: #5a8e7a; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 1rem; cursor: pointer;">Continue</button>
                <button class="btn-cancel" style="flex: 1; padding: 0.75rem 1.5rem; background-color: #3b4257; color: #a2a2bd; border: none; border-radius: 12px; font-weight: bold; font-size: 1rem; cursor: pointer;">Cancel</button>
            </div>
        </div>

        <div id="proccessing-content" class="content-section d-none vh-100 d-flex flex-column" style="width: 100%; max-width: 650px;">
            <div class="flex-fill">
            <div class="fw-bold fs-2 text-center my-3">Payment complete</div>
            <div class="fw-bold fs-2 text-center my-5">Your number is <span style="color: #87718B;">51</span></div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button class="btn-next" style="flex: 1; padding: 0.75rem 1.5rem; background-color: #5a8e7a; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 1rem; cursor: pointer;">Continue</button>
            </div>
        </div>

        <div id="collect-content" class="content-section d-none vh-100 d-flex flex-column" style="width: 100%; max-width: 650px;">
            <div class="flex-fill">
            <div class="fw-bold fs-2 text-center my-3">Please collect your order at the register</div>
            <div class="fw-bold fs-2 text-center my-5">Thank you</div>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="product.js"></script>
    <script src="offer.js"></script>
    <script src="checkout.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ids = ['checkout-content','payment-content','proccessing-content','collect-content'];
        const sections = ids.map(id => document.getElementById(id)).filter(Boolean);

        function getCurrentSection() {
            return sections.find(s => !s.classList.contains('d-none')) || null;
        }

        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const current = btn.closest('.content-section') || getCurrentSection();
                const idx = sections.indexOf(current);
                if (idx >= 0 && idx < sections.length - 1) {
                    current.classList.add('d-none');
                    const next = sections[idx + 1];
                    next.classList.remove('d-none');
                    next.scrollIntoView({behavior: 'smooth', block: 'start'});
                }
            });
        });
    });
    </script>
</body>
</html>
