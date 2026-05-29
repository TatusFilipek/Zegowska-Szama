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

-- Additional sample products
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Jabłka czerwone 1kg', 'owoce', 'jablka_czerwone.jpg', 400, 0, 150);
SET @prod4 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Banany 1kg', 'owoce', 'banany.jpg', 350, 5, 120);
SET @prod5 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Marchew 1kg', 'warzywa', 'marchew.jpg', 250, 0, 80);
SET @prod6 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Ziemniaki 2kg', 'warzywa', 'ziemniaki_2kg.jpg', 300, 0, 200);
SET @prod7 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Sok pomarańczowy 1L', 'napoje', 'sok_pomaranczowy_1l.jpg', 600, 10, 60);
SET @prod8 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Woda mineralna 1.5L', 'napoje', 'woda_1_5l.jpg', 200, 0, 300);
SET @prod9 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Czekolada mleczna 100g', 'słodycze', 'czekolada_mleczna_100g.jpg', 450, 15, 90);
SET @prod10 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Ciasteczka owsiane 200g', 'słodycze', 'ciasteczka_owsiane_200g.jpg', 500, 0, 70);
SET @prod11 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Ser żółty 250g', 'nabiał', 'ser_zolty_250g.jpg', 1100, 20, 40);
SET @prod12 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kurczak świeży 1kg', 'mięso', 'kurczak_1kg.jpg', 1500, 0, 30);
SET @prod13 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Łosoś wędzony 200g', 'ryby', 'losos_wedzony_200g.jpg', 2200, 10, 25);
SET @prod14 = LAST_INSERT_ID();

-- Optionally link some new products into an offer for demo
INSERT INTO offers (name, price, created_at) VALUES ('Zestaw owoce i napoje', 1200, NOW());
SET @offer3 = LAST_INSERT_ID();
INSERT INTO offer_products (offer_id, product_id, quantity) VALUES
    (@offer3, @prod4, 1),
    (@offer3, @prod8, 1);

-- Ensure each existing category has 10 products total
-- Additional pieczywo (existing: @prod1, @prod2)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Bagietka francuska', 'pieczywo', 'bagietka_francuska.jpg', 220, 0, 120);
SET @prod15 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Chleb pszenny', 'pieczywo', 'chleb_pszeny.jpg', 480, 0, 80);
SET @prod16 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Bułka kajzerka', 'pieczywo', 'bulka_kajzerka.jpg', 120, 0, 250);
SET @prod17 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Pumpernikiel 500g', 'pieczywo', 'pumpernikiel_500g.jpg', 650, 5, 60);
SET @prod18 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Chleb orkiszowy', 'pieczywo', 'chleb_orkiszowy.jpg', 700, 0, 40);
SET @prod19 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Bułka z sezamem', 'pieczywo', 'bulka_sezam.jpg', 140, 0, 180);
SET @prod20 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Chleb na zakwasie 500g', 'pieczywo', 'chleb_zakwas_500g.jpg', 900, 10, 50);
SET @prod21 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Bułka z makiem', 'pieczywo', 'bulka_mak.jpg', 130, 0, 140);
SET @prod22 = LAST_INSERT_ID();

-- Additional nabiał (existing: @prod3, @prod12)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Jogurt naturalny 400g', 'nabiał', 'jogurt_naturalny_400g.jpg', 300, 0, 120);
SET @prod23 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Mleko 1L', 'nabiał', 'mleko_1l.jpg', 280, 0, 200);
SET @prod24 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Śmietana 18% 200g', 'nabiał', 'smietana_18_200g.jpg', 220, 0, 80);
SET @prod25 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Serek wiejski 200g', 'nabiał', 'serek_wiejski_200g.jpg', 450, 0, 70);
SET @prod26 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Masło ekstra 250g', 'nabiał', 'maslo_ekstra_250g.jpg', 1200, 15, 30);
SET @prod27 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Twaróg półtusty 250g', 'nabiał', 'twarog_pol_250g.jpg', 600, 0, 60);
SET @prod28 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kefir 500ml', 'nabiał', 'kefir_500ml.jpg', 240, 0, 100);
SET @prod29 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Mleko roślinne 1L', 'nabiał', 'mleko_rozs_1l.jpg', 700, 0, 90);
SET @prod30 = LAST_INSERT_ID();

-- Additional owoce (existing: @prod4, @prod5)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Gruszki 1kg', 'owoce', 'gruszki_1kg.jpg', 450, 0, 100);
SET @prod31 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Winogrona 500g', 'owoce', 'winogrona_500g.jpg', 550, 5, 80);
SET @prod32 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Truskawki 250g', 'owoce', 'truskawki_250g.jpg', 700, 0, 60);
SET @prod33 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Cytryny 1kg', 'owoce', 'cytryny_1kg.jpg', 650, 0, 90);
SET @prod34 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Mandarynki 1kg', 'owoce', 'mandarynki_1kg.jpg', 480, 0, 110);
SET @prod35 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Ananas świeży', 'owoce', 'ananas_swiezy.jpg', 1200, 10, 30);
SET @prod36 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kiwi 1kg', 'owoce', 'kiwi_1kg.jpg', 900, 0, 50);
SET @prod37 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Śliwki 1kg', 'owoce', 'sliwki_1kg.jpg', 550, 0, 40);
SET @prod38 = LAST_INSERT_ID();

-- Additional warzywa (existing: @prod6, @prod7)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Papryka czerwona 1kg', 'warzywa', 'papryka_czerwona_1kg.jpg', 900, 0, 60);
SET @prod39 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Ogórki 1kg', 'warzywa', 'ogorki_1kg.jpg', 300, 0, 120);
SET @prod40 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Cebula 1kg', 'warzywa', 'cebula_1kg.jpg', 200, 0, 180);
SET @prod41 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Pomidory 1kg', 'warzywa', 'pomidory_1kg.jpg', 850, 0, 90);
SET @prod42 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Sałata świeża', 'warzywa', 'salata_swieza.jpg', 300, 0, 70);
SET @prod43 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Czosnek 100g', 'warzywa', 'czosnek_100g.jpg', 180, 0, 200);
SET @prod44 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Buraki 1kg', 'warzywa', 'buraki_1kg.jpg', 220, 0, 80);
SET @prod45 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Rzodkiewka pęczek', 'warzywa', 'rzodkiewka_peczek.jpg', 150, 0, 140);
SET @prod46 = LAST_INSERT_ID();

-- Additional napoje (existing: @prod8, @prod9)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Herbata czarna 50 torebek', 'napoje', 'herbata_czarna_50.jpg', 500, 0, 150);
SET @prod47 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kawa mielona 250g', 'napoje', 'kawa_mielona_250g.jpg', 1400, 5, 80);
SET @prod48 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Sok jabłkowy 1L', 'napoje', 'sok_jablkowy_1l.jpg', 550, 0, 90);
SET @prod49 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Napój gazowany 330ml', 'napoje', 'napoj_gazowany_330ml.jpg', 250, 0, 400);
SET @prod50 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kakao instant 200g', 'napoje', 'kakao_instant_200g.jpg', 800, 0, 60);
SET @prod51 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Sok pomidorowy 1L', 'napoje', 'sok_pomidorowy_1l.jpg', 420, 0, 70);
SET @prod52 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Izotonik 500ml', 'napoje', 'izotonik_500ml.jpg', 450, 0, 120);
SET @prod53 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kompocik 1L', 'napoje', 'kompocik_1l.jpg', 350, 0, 60);
SET @prod54 = LAST_INSERT_ID();

-- Additional słodycze (existing: @prod10, @prod11)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Guma do żucia 30szt', 'słodycze', 'guma_30szt.jpg', 200, 0, 300);
SET @prod55 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Lizaki 10szt', 'słodycze', 'lizaki_10szt.jpg', 180, 0, 200);
SET @prod56 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Baton kokosowy 50g', 'słodycze', 'baton_kokosowy_50g.jpg', 350, 5, 120);
SET @prod57 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Cukierki miętowe 200g', 'słodycze', 'cukierki_mietowe_200g.jpg', 400, 0, 90);
SET @prod58 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Batony zbożowe 6szt', 'słodycze', 'batony_zbozowe_6szt.jpg', 900, 0, 80);
SET @prod59 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Czekoladki praliny 200g', 'słodycze', 'czekoladki_praliny_200g.jpg', 1400, 10, 50);
SET @prod60 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Żelki mix 250g', 'słodycze', 'zelki_mix_250g.jpg', 600, 0, 110);
SET @prod61 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Herbatniki maślane 200g', 'słodycze', 'herbatniki_maslane_200g.jpg', 480, 0, 130);
SET @prod62 = LAST_INSERT_ID();

-- Additional mięso (existing: @prod13)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Wołowina mielona 500g', 'mięso', 'wolowina_mielona_500g.jpg', 1800, 0, 40);
SET @prod63 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Karkówka 1kg', 'mięso', 'karkowka_1kg.jpg', 1600, 0, 35);
SET @prod64 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Szynka gotowana 200g', 'mięso', 'szynka_gotowana_200g.jpg', 900, 5, 60);
SET @prod65 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kiełbasa wiejska 500g', 'mięso', 'kielbasa_wiejska_500g.jpg', 1200, 0, 45);
SET @prod66 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Polędwica 300g', 'mięso', 'poledwica_300g.jpg', 2200, 10, 20);
SET @prod67 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Boczek wędzony 250g', 'mięso', 'boczek_wedzony_250g.jpg', 950, 0, 50);
SET @prod68 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Udka z kurczaka 1kg', 'mięso', 'udka_kurczaka_1kg.jpg', 1000, 0, 70);
SET @prod69 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Kaczka świeża 1kg', 'mięso', 'kaczka_1kg.jpg', 2000, 0, 15);
SET @prod70 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Polędwiczki wieprzowe 500g', 'mięso', 'poledwiczki_wieprzowe_500g.jpg', 2100, 0, 25);
SET @prod71 = LAST_INSERT_ID();

-- Additional ryby (existing: @prod14)
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Tilapia świeża 1kg', 'ryby', 'tilapia_1kg.jpg', 1400, 0, 20);
SET @prod72 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Pstrąg świeży 1kg', 'ryby', 'pstrag_1kg.jpg', 1600, 0, 18);
SET @prod73 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Śledź solony 200g', 'ryby', 'sledz_solony_200g.jpg', 300, 0, 100);
SET @prod74 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Krewetki 300g', 'ryby', 'krewetki_300g.jpg', 1800, 10, 30);
SET @prod75 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Świeży dorsz 1kg', 'ryby', 'dorsz_1kg.jpg', 2000, 0, 15);
SET @prod76 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Małże świeże 500g', 'ryby', 'malze_500g.jpg', 1200, 0, 25);
SET @prod77 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Tuńczyk w kawałkach 200g', 'ryby', 'tunczyk_200g.jpg', 900, 0, 60);
SET @prod78 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Filet z pstrąga 200g', 'ryby', 'filet_pstrag_200g.jpg', 700, 0, 40);
SET @prod79 = LAST_INSERT_ID();
INSERT INTO products (name, category, picture, price_cents, discount_percent, stock) VALUES
    ('Marynowany śledź 300g', 'ryby', 'marynowany_sledz_300g.jpg', 450, 0, 50);
SET @prod80 = LAST_INSERT_ID();
