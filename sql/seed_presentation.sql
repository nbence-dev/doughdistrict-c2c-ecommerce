-- DoughDistrict — Presentation Seed
-- ─────────────────────────────────────────────────────────────────────────
-- Wipes all existing data and loads a full demo dataset.
-- Run with:  docker exec -i dough-db mysql -udough -psecret doughdistrict < sql/seed_presentation.sql
--
-- Accounts:
--   admin@doughdistrict.co.za  →  admin123
--   sarah@example.com          →  seller123  (Sarah's Sweet Spot — Cape Town)
--   liam@example.com           →  seller123  (Liam's Loaf Co. — Johannesburg)
--   zanele@example.com         →  seller123  (Zanele's Kitchen — Pretoria)
--   cookie@example.com         →  seller123  (Cape Cookie Co. — Stellenbosch)
--   priya@example.com          →  buyer123
--   tom@example.com            →  buyer123
--   amahle@example.com         →  buyer123
--   johan@example.com          →  buyer123
-- ─────────────────────────────────────────────────────────────────────────

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE reviews;
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
TRUNCATE TABLE products;
TRUNCATE TABLE seller_profiles;
TRUNCATE TABLE addresses;
TRUNCATE TABLE users;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

-- ── Users ─────────────────────────────────────────────────────────────────
-- id: 1=admin  2=sarah  3=liam  4=zanele  5=cookie  6=priya  7=tom  8=amahle  9=johan
INSERT INTO users (name, email, password, role, is_active) VALUES
  ('Admin',              'admin@doughdistrict.co.za', '$2y$12$6bYcYjDXCXunbNS0dGgtm.3GhZNSZhADbOgyDO6fDihn.WEfdTxeq', 'admin',  TRUE),
  ('Sarah Bakes',        'sarah@example.com',          '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
  ('Liam van Rooyen',    'liam@example.com',            '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
  ('Zanele Mokoena',     'zanele@example.com',          '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
  ('Cape Cookie Co.',    'cookie@example.com',          '$2y$12$SM5SC1pHLakOCX3gtTrpKev8JQp.APb2CUNjxjee5FgSqEaxIhXvy', 'seller', TRUE),
  ('Priya Naidoo',       'priya@example.com',           '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE),
  ('Tom Hendricks',      'tom@example.com',             '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE),
  ('Amahle Dlamini',     'amahle@example.com',          '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE),
  ('Johan van der Berg', 'johan@example.com',           '$2y$12$DydEFy0I70jmuZeV6pbsC.Ljmoj86PIJY7Q1dVWi.8SvQK1I7cV7u', 'buyer',  TRUE);

-- ── Seller profiles ───────────────────────────────────────────────────────
-- id: 1=sarah  2=liam  3=zanele  4=cookie
INSERT INTO seller_profiles (user_id, shop_name, bio, stripe_onboarding_complete) VALUES
  (2, 'Sarah''s Sweet Spot', 'Home baker based in Cape Town. Cakes, brownies, and treats baked with love and the finest local ingredients. Every order is made fresh to order.', FALSE),
  (3, 'Liam''s Loaf Co.',    'Artisan sourdough and rye breads slow-fermented and baked fresh every Friday out of Johannesburg. Seven-year-old starter, no shortcuts.', FALSE),
  (4, 'Zanele''s Kitchen',   'Traditional and modern bakes from Pretoria. Rusks, banana bread, and cinnamon rolls straight from my home oven — made the way my grandmother taught me.', FALSE),
  (5, 'Cape Cookie Co.',     'Small-batch cookies and biscotti hand-crafted in Stellenbosch using stone-ground flour and fair-trade chocolate. Certified: no margarine, ever.', FALSE);

-- ── Categories ────────────────────────────────────────────────────────────
-- id: 1=breads  2=cakes  3=cookies  4=pastries  5=rusks
INSERT INTO categories (name, slug) VALUES
  ('Breads & Rolls',     'breads-rolls'),
  ('Cakes & Cupcakes',   'cakes-cupcakes'),
  ('Cookies & Biscuits', 'cookies-biscuits'),
  ('Pastries & Pies',    'pastries-pies'),
  ('Rusks & Biscotti',   'rusks-biscotti');

-- ── Buyer addresses ───────────────────────────────────────────────────────
INSERT INTO addresses (user_id, label, street, city, province, postal_code, is_default) VALUES
  (6, 'Home', '12 Baobab Street',     'Durban',       'KwaZulu-Natal', '4001', TRUE),
  (7, 'Home', '7 Fynbos Avenue',      'Cape Town',    'Western Cape',  '7441', TRUE),
  (8, 'Home', '5 Jacaranda Crescent', 'Pretoria',     'Gauteng',       '0002', TRUE),
  (9, 'Home', '22 Protea Road',       'Stellenbosch', 'Western Cape',  '7600', TRUE);

-- ── Products ──────────────────────────────────────────────────────────────
-- seller_id = seller_profiles.id  |  category_id = categories.id
-- id: 1=RedVelvet  2=Brownies  3=LemonDrizzle  4=Sourdough  5=RyeLoaf  6=Focaccia
--     7=Rusks  8=BananaBread  9=CinnamonRolls  10=ChocChipCookies  11=Shortbread  12=Biscotti
INSERT INTO products (seller_id, category_id, name, description, price, stock_qty, status, image_url, weight_kg, length_cm, width_cm, height_cm, shipping_cost) VALUES

  -- Sarah's Sweet Spot (seller_id = 1)
  (1, 2, 'Red Velvet Cake',
   'A showstopping 20 cm round cake with three lush layers of crimson velvet sponge, each sandwiched and smothered in silky cream cheese frosting. Made with Dutch-process cocoa, real buttermilk, and a hint of white wine vinegar for that classic tang. Perfect for birthdays, anniversaries, or any excuse to celebrate. Serves 10–12.',
   320.00, 6, 'active',
   'https://images.unsplash.com/photo-1586788680434-30d324b2d46f?auto=format&fit=crop&w=600&q=80',
   1.500, 28.00, 28.00, 15.00, 95.00),

  (1, 3, 'Double Choc Brownies',
   'A dozen fudgy, deeply chocolatey brownies packed with Callebaut dark chocolate chunks. These are the thick, gooey kind — crinkly on top, molten in the middle. No cakey imposters here. Baked in small batches and cut by hand. Best enjoyed slightly warm with a scoop of vanilla ice cream or a cold glass of milk.',
   95.00, 30, 'active',
   'https://www.retrorecipebox.com/wp-content/uploads/2020/04/double-chocolate-brownies-4.jpg',
   0.500, 22.00, 16.00, 5.00, 75.00),

  (1, 2, 'Lemon Drizzle Cake',
   'A bright, zesty loaf cake made with fresh lemon zest and juice, drenched in a warm lemon sugar syrup the moment it leaves the oven — giving it that iconic crunchy, sticky top. Finished with a sharp lemon glaze. Light enough for afternoon tea, satisfying enough for dessert. Serves 8–10.',
   180.00, 10, 'active',
   'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?auto=format&fit=crop&w=600&q=80',
   0.900, 25.00, 12.00, 10.00, 80.00),

  -- Liam's Loaf Co. (seller_id = 2)
  (2, 1, 'Country Sourdough',
   'An 800 g round sourdough boule, slow-fermented for 36 hours using a seven-year-old wild yeast starter. The crust blisters and crunches when you slice it; the crumb is open and chewy with a balanced, complex tang. Scored by hand and baked in a Dutch oven. Baked every Friday — order by Wednesday to secure yours.',
   110.00, 12, 'active',
   'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80',
   0.900, 24.00, 24.00, 12.00, 80.00),

  (2, 1, 'Seeded Rye Loaf',
   'A dense, earthy 700 g loaf made with 80% stone-ground rye flour and a sourdough starter, packed with sunflower seeds, pumpkin seeds, and a touch of caraway. Slices beautifully thin and keeps well for up to a week. Pairs perfectly with aged cheddar, smoked trout, or just a thick spread of good butter.',
   95.00, 10, 'active',
   'https://www.sainsburysmagazine.co.uk/uploads/media/1080x1155/08/5048-Seed-Packed-Rye-Loaf-1120.jpg?v=1-0',
   0.800, 26.00, 14.00, 9.00, 80.00),

  (2, 4, 'Rosemary Focaccia',
   'A thick, pillowy slab of Ligurian-style focaccia dimpled all over and finished with South African extra virgin olive oil, sea salt flakes, and fresh rosemary from the garden. Crisp on the bottom, light and chewy inside. Roughly 35 × 25 cm — enough for a crowd as a starter, alongside soup, or as a sandwich base.',
   85.00, 8, 'active',
   'https://donuts2crumpets.com/wp-content/uploads/2025/09/rosemary-sea-salt-focaccia.jpg',
   0.700, 35.00, 25.00, 4.00, 80.00),

  -- Zanele's Kitchen (seller_id = 3)
  (3, 5, 'Mosbolletjie Rusks',
   'A beloved South African classic — buttermilk mosbolletjie rusks made with aniseed and a touch of sugar cane. Baked in pull-apart loaves, then pulled by hand and dried overnight in a low oven until perfectly crunchy all the way through. Twelve large rusks per bag. Dunk in your morning rooibos and thank yourself.',
   120.00, 15, 'active',
   'https://rainbowcooking.co.nz/sites/default/files/styles/standard_recipe_480_x_320_/public/2020-11/mosbolletjies-sm.jpg?itok=cON6eDC3',
   0.600, 22.00, 16.00, 10.00, 70.00),

  (3, 2, 'Brown Butter Banana Bread',
   'Made with four very ripe bananas, brown butter, and a ribbon of cinnamon-brown-sugar swirl through the middle. Moist, dense, and almost pudding-like inside. A generous dusting of demerara sugar on top creates a glittering, slightly crunchy crust. Still perfect four days later — though it never lasts that long. Serves 8.',
   150.00, 8, 'active',
   'https://justinesnacks.com/wp-content/uploads/2023/04/how-to-make-a-great-brown-butter-banana-bread.jpg',
   0.700, 24.00, 12.00, 9.00, 80.00),

  (3, 4, 'Cinnamon Rolls (6-pack)',
   'Six generously sized, pillowy rolls made from an enriched butter dough with a filling of cinnamon, brown sugar, and soft Medjool dates. Baked until golden and pulled from the oven straight into a pool of cream cheese glaze. Best eaten the day of, but a 20-second microwave brings them right back to life.',
   100.00, 10, 'active',
   'https://mccormick.widen.net/content/megysgsour/jpeg/Holiday_Cinnamon-Rolls_1376x774.jpeg',
   0.500, 30.00, 24.00, 8.00, 85.00),

  -- Cape Cookie Co. (seller_id = 4)
  (4, 3, 'Choc Chip Cookies (12-pack)',
   'Twelve thick, bakery-style cookies made with Callebaut dark chocolate chips, cultured butter, and a 24-hour chilled dough for deeper, more complex flavour. Crispy on the edges, chewy and slightly underdone in the centre. Finished with a pinch of fleur de sel. The standard for a reason.',
   85.00, 25, 'active',
   'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=600&q=80',
   0.400, 22.00, 16.00, 6.00, 70.00),

  (4, 3, 'Butter Shortbread Fingers (tin)',
   'Eighteen delicate shortbread fingers in a reusable tin, made from a four-ingredient recipe: stone-ground flour, cultured butter, icing sugar, and a pinch of salt. Baked low and slow until pale gold. The kind that crumbles perfectly on your tongue and disappears before you notice. No margarine. Not ever.',
   95.00, 20, 'active',
   'https://scottishscran.com/wp-content/uploads/2024/12/Shortbread-FIngers-Recipe-13.jpg',
   0.450, 20.00, 20.00, 6.00, 70.00),

  (4, 5, 'Almond & Orange Biscotti',
   'Crisp, twice-baked Stellenbosch biscotti packed with whole toasted almonds and infused with fresh orange zest and a splash of local brandy. Approximately 16 biscotti per resealable kraft bag. Ideal alongside espresso, a glass of Muscadel, or a Kaapse dop. A proper grown-up treat.',
   90.00, 18, 'active',
   'https://somebodyfeedseb.com/wp-content/uploads/2025/11/Square-2025.11.18-Orange-and-almond-sourdough-biscotti-9091.jpg',
   0.350, 20.00, 12.00, 8.00, 70.00);

-- ── Orders (all delivered for demo purposes) ──────────────────────────────
-- buyer_id = users.id  |  seller_id = seller_profiles.id
-- id: 1=priya→sarah  2=priya→liam  3=tom→sarah   4=tom→cookie   5=tom→liam
--     6=amahle→zanele  7=amahle→sarah  8=johan→liam  9=johan→zanele  10=johan→cookie
INSERT INTO orders (buyer_id, seller_id, status, total_amount, stripe_payment_intent_id, shipping_name, shipping_street, shipping_city, shipping_province, shipping_postal_code, shipping_cost) VALUES
  -- totals = items + shipping
  (6, 1, 'delivered', 595.00, 'pi_demo_001', 'Priya Naidoo',       '12 Baobab Street',     'Durban',       'KwaZulu-Natal', '4001', 95.00),  -- 320+180+95
  (6, 2, 'delivered', 190.00, 'pi_demo_002', 'Priya Naidoo',       '12 Baobab Street',     'Durban',       'KwaZulu-Natal', '4001', 80.00),  -- 110+80
  (7, 1, 'delivered', 265.00, 'pi_demo_003', 'Tom Hendricks',      '7 Fynbos Avenue',      'Cape Town',    'Western Cape',  '7441', 75.00),  -- 95*2+75
  (7, 4, 'delivered', 155.00, 'pi_demo_004', 'Tom Hendricks',      '7 Fynbos Avenue',      'Cape Town',    'Western Cape',  '7441', 70.00),  -- 85+70
  (7, 2, 'delivered', 190.00, 'pi_demo_005', 'Tom Hendricks',      '7 Fynbos Avenue',      'Cape Town',    'Western Cape',  '7441', 80.00),  -- 110+80
  (8, 3, 'delivered', 350.00, 'pi_demo_006', 'Amahle Dlamini',     '5 Jacaranda Crescent', 'Pretoria',     'Gauteng',       '0002', 80.00),  -- 120+150+80
  (8, 1, 'delivered', 415.00, 'pi_demo_007', 'Amahle Dlamini',     '5 Jacaranda Crescent', 'Pretoria',     'Gauteng',       '0002', 95.00),  -- 320+95
  (9, 2, 'delivered', 175.00, 'pi_demo_008', 'Johan van der Berg', '22 Protea Road',       'Stellenbosch', 'Western Cape',  '7600', 80.00),  -- 95+80
  (9, 3, 'delivered', 285.00, 'pi_demo_009', 'Johan van der Berg', '22 Protea Road',       'Stellenbosch', 'Western Cape',  '7600', 85.00),  -- 100*2+85
  (9, 4, 'delivered', 160.00, 'pi_demo_010', 'Johan van der Berg', '22 Protea Road',       'Stellenbosch', 'Western Cape',  '7600', 70.00);  -- 90+70

-- ── Order items ───────────────────────────────────────────────────────────
INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES
  (1,  1,  'Red Velvet Cake',              320.00, 1),
  (1,  3,  'Lemon Drizzle Cake',           180.00, 1),
  (2,  4,  'Country Sourdough',            110.00, 1),
  (3,  2,  'Double Choc Brownies',          95.00, 2),
  (4,  10, 'Choc Chip Cookies (12-pack)',   85.00, 1),
  (5,  4,  'Country Sourdough',            110.00, 1),
  (6,  7,  'Mosbolletjie Rusks',           120.00, 1),
  (6,  8,  'Brown Butter Banana Bread',    150.00, 1),
  (7,  1,  'Red Velvet Cake',              320.00, 1),
  (8,  5,  'Seeded Rye Loaf',              95.00, 1),
  (9,  9,  'Cinnamon Rolls (6-pack)',      100.00, 2),
  (10, 12, 'Almond & Orange Biscotti',     90.00, 1);

-- ── Reviews ───────────────────────────────────────────────────────────────
-- (product_id, buyer_id, order_id, rating, comment)
INSERT INTO reviews (product_id, buyer_id, order_id, rating, comment) VALUES

  -- Red Velvet Cake (product 1) — 2 reviews, avg 5.0
  (1, 6, 1, 5, 'Absolutely stunning cake. The cream cheese frosting is perfectly tangy and not too sweet. Arrived beautifully packaged and looked exactly like the photo. Will definitely be ordering again for my mother''s birthday next month.'),
  (1, 8, 7, 5, 'I brought this to a family braai and it was finished within minutes. Everyone asked where I got it from. The sponge is so moist and that colour is just gorgeous. 10 out of 10 from Pretoria!'),

  -- Double Choc Brownies (product 2) — 1 review, avg 5.0
  (2, 7, 3, 5, 'These are the real deal. Thick, fudgy, and properly chocolatey. I ordered two packs and both were gone within a day. The crinkly top is chef''s kiss. Genuinely dangerous to have in the house.'),

  -- Lemon Drizzle Cake (product 3) — 1 review, avg 4.0
  (3, 6, 1, 4, 'Light, zingy, and perfectly moist. The crunchy sugar top is completely addictive. Only minor thing is it was slightly smaller than I imagined, but the flavour absolutely makes up for it. Would order again.'),

  -- Country Sourdough (product 4) — 2 reviews, avg 5.0
  (4, 6, 2, 5, 'Best sourdough I''ve had outside of a proper bakery. The crust is incredible — it shatters when you slice it. The crumb is chewy and the tang is just right. Worth every rand and then some.'),
  (4, 7, 5, 5, 'I''ve been baking sourdough at home for years and this is better than anything I''ve managed. Liam clearly knows what he''s doing. The 36-hour ferment really comes through in the flavour depth.'),

  -- Seeded Rye Loaf (product 5) — 1 review, avg 4.0
  (5, 9, 8, 4, 'Dense and earthy — exactly what a good rye should be. The seeds add great texture throughout. Keeps incredibly well all week. Sliced thin with some brie it''s a perfect lunch. Would love a caraway-only version too.'),

  -- Mosbolletjie Rusks (product 7) — 1 review, avg 5.0
  (7, 8, 6, 5, 'These taste exactly like my ouma used to make. The aniseed is subtle — not too strong, just enough. Dunked one in my morning rooibos and it was pure nostalgia. Beautiful packaging too. Already on my second bag.'),

  -- Brown Butter Banana Bread (product 8) — 1 review, avg 5.0
  (8, 8, 6, 5, 'The brown butter and cinnamon swirl takes this banana bread to another level entirely. Incredibly moist — I had it four days later and it was still perfect. The demerara crust is an absolute genius touch.'),

  -- Cinnamon Rolls (product 9) — 1 review, avg 5.0
  (9, 9, 9, 5, 'Pillowy, gooey, and dangerously good. The cream cheese glaze is generous and the Medjool date filling adds a lovely depth you don''t get with plain cinnamon rolls. Heated for 20 seconds and they tasted fresh out the oven. Reordering immediately.'),

  -- Choc Chip Cookies (product 10) — 1 review, avg 4.0
  (10, 7, 4, 4, 'Solid cookies with a great chocolate-to-dough ratio. Crispy edges, chewy centre — exactly right. The sea salt on top is a very nice touch. Slightly pricier than I expected but the quality is clearly there.'),

  -- Almond & Orange Biscotti (product 12) — 1 review, avg 5.0
  (12, 9, 10, 5, 'Exceptional biscotti. The orange and brandy flavour is subtle and sophisticated — not overpowering at all. Perfect alongside a double espresso. The almonds are whole and toasted just right. A proper grown-up treat. Already gifted a bag to a friend.');
