(function () {
    const API_URL = 'manage.php';

    function formatPrice(cents) {
        return (cents / 100).toFixed(2) + '$';
    }

    // --- LOGIKA PRZEŁĄCZANIA ZAKŁADEK ---
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('d-none');
            });
            
            document.getElementById(tabName).classList.remove('d-none');
            
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.color = '#a0a0b0';
                b.style.fontWeight = '400';
            });
            this.style.color = '#2e3d52';
            this.style.fontWeight = '700';

            if (tabName === 'orders') reloadOrders();
            if (tabName === 'products') reloadProducts();
            if (tabName === 'users') reloadUsers();
        });
    });
    
    const firstTab = document.querySelector('.tab-btn');
    if (firstTab) {
        firstTab.style.color = '#2e3d52';
        firstTab.style.fontWeight = '700';
    }

    // --- ZAKŁADKA: ORDERS ---
    async function reloadOrders() {
        const tbody = document.getElementById('orders-table-body');
        if (!tbody) return;

        try {
            const res = await fetch(`${API_URL}?action=get_orders`);
            const orders = await res.json();

            tbody.innerHTML = orders.map(o => `
                <tr style="color: #2e3d52; vertical-align: middle;">
                    <td>${o.name}</td>
                    <td style="color: #5a8e7a; font-weight: 600;">${String(o.id).padStart(2, '0')}</td>
                    <td>
                        <button class="btn btn-sm rounded-2 me-2" onclick="updateOrderStatus(${o.id}, 3)" style="background-color: #5a8e7a; color: white; border: none; width: 30px; height: 30px;">
                            <span style="font-size: 0.8rem;">✓</span>
                        </button>
                        <button class="btn btn-sm rounded-2" onclick="updateOrderStatus(${o.id}, 0)" style="background-color: #3b4257; color: white; border: none; width: 30px; height: 30px;">
                            <span style="font-size: 0.8rem;">×</span>
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">Błąd synchronizacji zamówień.</td></tr>`;
        }
    }

    window.updateOrderStatus = async function (id, statusNum) {
        try {
            const res = await fetch(`${API_URL}?action=update_order_status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status: statusNum })
            });
            if (res.ok) reloadOrders();
        } catch (e) {
            alert('Wystąpił błąd podczas zmiany statusu.');
        }
    };

    // --- ZAKŁADKA: PRODUCTS (OKNO MODALNE NA CZELE STRONY) ---
    async function reloadProducts() {
        const tbody = document.getElementById('products-table-body');
        if (!tbody) return;

        try {
            const res = await fetch(`${API_URL}?action=get_products`);
            const products = await res.json();

            tbody.innerHTML = products.map(p => `
                <tr style="color: #2e3d52; vertical-align: middle;">
                    <td class="fw-bold">${p.name}</td>
                    <td style="color: #8b8b9e;">${p.category}</td>
                    <td>${p.stock} pcs</td>
                    <td style="color: #5a8e7a; font-weight: 600;">${formatPrice(p.price_cents)}</td>
                    <td>
                        <button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" 
                                onclick="selectProductForEdit(${p.id}, '${p.name.replace(/'/g, "\\'")}', '${p.category.replace(/'/g, "\\'")}', ${p.stock}, ${p.price_cents}, ${p.discount_percent || 0})" 
                                style="background-color: #5a8e7a; color: white; border: none;">
                            Select
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center">Błąd pobierania bazy produktów.</td></tr>`;
        }
    }

    // Otwieranie dla Nowego Produktu
    window.openNewProductModal = function () {
        document.getElementById('product-form').reset();
        document.getElementById('prod-id').value = '';
        document.getElementById('form-product-title').textContent = 'Create New Product';
        document.getElementById('prod-submit-btn').textContent = 'Create';
        document.getElementById('prod-submit-btn').style.backgroundColor = '#5a8e7a';
        
        document.getElementById('product-modal').classList.add('show');
    };

    // Otwieranie dla Edycji istniejącego (Select)
    window.selectProductForEdit = function (id, name, category, stock, price, discount) {
        document.getElementById('prod-id').value = id;
        document.getElementById('prod-name').value = name;
        document.getElementById('prod-category').value = category;
        document.getElementById('prod-stock').value = stock;
        document.getElementById('prod-price').value = price;
        document.getElementById('prod-discount').value = discount;

        document.getElementById('form-product-title').textContent = `Edit Product (ID: #${id})`;
        document.getElementById('prod-submit-btn').textContent = 'Save Changes';
        document.getElementById('prod-submit-btn').style.backgroundColor = '#3b4257';

        document.getElementById('product-modal').classList.add('show');
    };

    // Funkcje zamykania
    window.closeProductModal = function () {
        document.getElementById('product-modal').classList.remove('show');
    };

    window.closeProductModalOnOutsideClick = function (event) {
        if (event.target.id === 'product-modal') {
            closeProductModal();
        }
    };

    // Obsługa zapisu
    document.getElementById('product-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const idVal = document.getElementById('prod-id').value;

        const payload = {
            id: idVal ? parseInt(idVal, 10) : null,
            name: document.getElementById('prod-name').value,
            category: document.getElementById('prod-category').value,
            stock: parseInt(document.getElementById('prod-stock').value, 10),
            price_cents: parseInt(document.getElementById('prod-price').value, 10),
            discount_percent: parseInt(document.getElementById('prod-discount').value, 10)
        };

        try {
            const res = await fetch(`${API_URL}?action=save_product`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                alert(idVal ? 'Zmiany zostały pomyślnie zapisane!' : 'Nowy produkt został dodany!');
                closeProductModal();
                reloadProducts();
            }
        } catch (e) {
            alert('Wystąpił błąd zapisu produktu.');
        }
    });

    // --- ZAKŁADKA: USERS ---
    async function reloadUsers() {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        try {
            const res = await fetch(`${API_URL}?action=get_users`);
            const users = await res.json();

            renderUsersList(users);

            document.getElementById('user-search').addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const filtered = users.filter(u => u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query));
                renderUsersList(filtered);
            });

        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-danger text-center">Błąd pobierania bazy użytkowników.</td></tr>`;
        }
    }

    function renderUsersList(usersArray) {
        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = usersArray.map(u => `
            <tr style="color: #2e3d52; vertical-align: middle;">
                <td>${u.name}</td>
                <td style="color: #8b8b9e; font-size: 0.9rem;">${u.email}</td>
                <td style="color: ${u.role_name === 'admin' ? '#5a8e7a' : '#a0a0b0'}; font-weight: ${u.role_name === 'admin' ? '600' : '400'}">${u.role_name}</td>
                <td>
                    <button class="btn btn-sm fw-semibold px-3 py-1 rounded-2" onclick="promptChangeRole(${u.id})" style="background-color: #5a8e7a; color: white; border: none;">
                        Select
                    </button>
                </td>
            </tr>
        `).join('');
    }

    window.promptChangeRole = async function(userId) {
        const targetRole = prompt("Wpisz numer nowej roli:\n1 - Admin\n2 - Customer\n3 - Manager");
        if (!targetRole || !['1', '2', '3'].includes(targetRole)) return;

        try {
            const res = await fetch(`${API_URL}?action=change_user_role`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userId, role_id: parseInt(targetRole, 10) })
            });
            if (res.ok) reloadUsers();
        } catch (e) {
            alert('Błąd modyfikacji roli użytkownika.');
        }
    };

    // --- ZAKŁADKA: MAIL ---
    document.getElementById('mail-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            title: document.getElementById('mail-title').value,
            content: document.getElementById('mail-content').value
        };

        try {
            const res = await fetch(`${API_URL}?action=send_notification`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                alert('Ogłoszenie zostało opublikowane!');
                this.reset();
            }
        } catch (e) {
            alert('Wystąpił błąd podczas wysyłania ogłoszenia.');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        reloadOrders();
    });
})();