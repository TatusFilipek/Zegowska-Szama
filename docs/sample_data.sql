-- Sample data for import into the Zegowska-Szama database
INSERT INTO roles (name) VALUES ('admin');
SET @role_admin = LAST_INSERT_ID();
INSERT INTO roles (name) VALUES ('customer');
SET @role_customer = LAST_INSERT_ID();
INSERT INTO roles (name) VALUES ('manager');
SET @role_manager = LAST_INSERT_ID();

INSERT INTO permissions (name) VALUES ('manage_products');
SET @perm_manage_products = LAST_INSERT_ID();
INSERT INTO permissions (name) VALUES ('manage_orders');
SET @perm_manage_orders = LAST_INSERT_ID();
INSERT INTO permissions (name) VALUES ('view_reports');
SET @perm_view_reports = LAST_INSERT_ID();
INSERT INTO permissions (name) VALUES ('claim_offers');
SET @perm_claim_offers = LAST_INSERT_ID();

INSERT INTO role_permissions (role_id, permission_id) VALUES
    (@role_admin, @perm_manage_products),
    (@role_admin, @perm_manage_orders),
    (@role_admin, @perm_view_reports),
    (@role_admin, @perm_claim_offers),
    (@role_manager, @perm_manage_orders),
    (@role_manager, @perm_view_reports),
    (@role_customer, @perm_claim_offers);

INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Chleb razowy', 'pieczywo', 'bread_razowy.jpg', 500, 0, 100);
SET @prod1 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Bułka maślana', 'pieczywo', 'bulka_maslana.jpg', 150, 10, 200);
SET @prod2 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Masło 200g', 'nabiał', 'maslo_200g.jpg', 800, 5, 50);
SET @prod3 = LAST_INSERT_ID();

INSERT INTO offers (name, price, created_at) VALUES ('Zestaw śniadaniowy', 1200, NOW());
SET @offer1 = LAST_INSERT_ID();
INSERT INTO offers (name, price, created_at) VALUES ('Promocja masła', 700, NOW());
SET @offer2 = LAST_INSERT_ID();

INSERT INTO offer_products (offer_id, product_id, quantity) VALUES
    (@offer1, @prod1, 1),
    (@offer1, @prod3, 1),
    (@offer2, @prod3, 1);

INSERT INTO announcements (title, content, created_at) VALUES
    ('Otwarcie sklepu', 'Witamy w naszym nowym sklepie!', NOW());

INSERT INTO users (name, password, email, role_id, created_at) VALUES
    ('Anna', 'password123', 'anna@example.com', @role_customer, NOW());
SET @user_anna = LAST_INSERT_ID();
INSERT INTO users (name, password, email, role_id, created_at) VALUES
    ('Piotr', 'adminpass', 'piotr@example.com', @role_admin, NOW());
SET @user_piotr = LAST_INSERT_ID();

INSERT INTO settings (user_id, theme) VALUES
    (@user_anna, 'light'),
    (@user_piotr, 'dark');

INSERT INTO orders (user_id, status, created_at) VALUES
    (@user_anna, 0, NOW());
SET @order1 = LAST_INSERT_ID();
INSERT INTO orders (user_id, status, created_at) VALUES
    (@user_piotr, 1, NOW());
SET @order2 = LAST_INSERT_ID();

INSERT INTO order_items (order_id, product_id, quantity, unit_price_snapshot, discount_percent_snapshot) VALUES
    (@order1, @prod2, 3, 150, 10),
    (@order1, @prod1, 1, 500, 0),
    (@order2, @prod3, 2, 800, 5);

INSERT INTO order_offers (order_id, offer_id, quantity, unit_price_snapshot) VALUES
    (@order2, @offer2, 1, 700);

INSERT INTO user_claimed_offers (user_id, offer_id, claimed_at) VALUES
    (@user_anna, @offer1, NOW());
