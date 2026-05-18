-- DoughDistrict Seed Data
-- Passwords (bcrypt cost 12):
--   admin@doughdistrict.co.za  → admin123
--   sarah@example.com          → seller123
--   liam@example.com           → seller123
--   priya@example.com          → buyer123
--   tom@example.com            → buyer123

-- -------------------------------------------------------
-- Users
-- -------------------------------------------------------
INSERT INTO users (name, email, password, role, is_active) VALUES
    ('Admin',        'admin@doughdistrict.co.za', '$2y$12$6bYcYjDXCXunbNS0dGgtm.3GhZNSZhADbOgyDO6fDihn.WEfdTxeq', 'admin',  TRUE),
    ('Sarah Bakes',  'sarah@example.com',          '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
    ('Liam\'s Loaf', 'liam@example.com',            '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
    ('Priya Naidoo', 'priya@example.com',           '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE),
    ('Tom Hendricks','tom@example.com',             '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE);

-- -------------------------------------------------------
-- Seller profiles
-- -------------------------------------------------------
INSERT INTO seller_profiles (user_id, shop_name, bio, stripe_onboarding_complete) VALUES
    (2, 'Sarah\'s Sweet Spot',  'Home baker in Cape Town. Cakes, cookies, and brownies made with love.', FALSE),
    (3, 'Liam\'s Loaf Co.',     'Artisan sourdough and rye breads baked fresh every Friday, Johannesburg.', FALSE);

-- -------------------------------------------------------
-- Categories
-- -------------------------------------------------------
INSERT INTO categories (name, slug) VALUES
    ('Breads & Rolls',  'breads-rolls'),
    ('Cakes & Cupcakes','cakes-cupcakes'),
    ('Cookies & Biscuits','cookies-biscuits'),
    ('Pastries & Pies', 'pastries-pies'),
    ('Rusks & Biscotti','rusks-biscotti');

-- -------------------------------------------------------
-- Products  (seller_id references seller_profiles.id)
-- -------------------------------------------------------
INSERT INTO products (seller_id, category_id, name, description, price, stock_qty, status, image_url, weight_kg, length_cm, width_cm, height_cm) VALUES
    -- Sarah (seller_profiles.id = 1)
    (1, 2, 'Red Velvet Cake',      'Classic red velvet with cream cheese frosting. Whole 20 cm cake.',  320.00, 5,  'active', 'https://picsum.photos/seed/redvelvet/400/400',  1.20, 25, 25, 12),
    (1, 3, 'Double Choc Brownies', 'Fudgy brownies, box of 12. Rich Belgian chocolate.',                 95.00, 20, 'active', 'https://picsum.photos/seed/brownies/400/400',   0.40, 20, 15,  5),
    (1, 2, 'Lemon Drizzle Cake',   'Zesty lemon sponge with a sweet glaze. 18 cm loaf.',                180.00, 8,  'active', 'https://picsum.photos/seed/lemondrizzle/400/400', 0.80, 22, 10, 10),
    -- Liam (seller_profiles.id = 2)
    (2, 1, 'Country Sourdough',    'Long-fermented sourdough. 800 g round loaf. Crispy crust.',         110.00, 10, 'active', 'https://picsum.photos/seed/sourdough/400/400',  0.80, 22, 22, 10),
    (2, 1, 'Seeded Rye Loaf',      'Dense rye with sunflower and pumpkin seeds. 700 g loaf.',            95.00, 8,  'active', 'https://picsum.photos/seed/ryeloaf/400/400',    0.70, 24, 12,  8);

-- -------------------------------------------------------
-- Buyer addresses
-- -------------------------------------------------------
INSERT INTO addresses (user_id, label, street, city, province, postal_code, is_default) VALUES
    (4, 'Home', '12 Baobab Street', 'Durban',       'KwaZulu-Natal', '4001', TRUE),
    (5, 'Home', '7 Fynbos Avenue',  'Cape Town',    'Western Cape',  '7441', TRUE);
