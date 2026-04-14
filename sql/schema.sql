-- DoughDistrict Schema
-- Roles: admin | seller | buyer
-- Images: Cloudflare R2 (store CDN URL)
-- Payments: Stripe Connect (no platform fee for MVP)
-- Courier: The Courier Guy API (seller-triggered)

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'seller', 'buyer') NOT NULL DEFAULT 'buyer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE seller_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    shop_name VARCHAR(100) NOT NULL,
    bio TEXT,
    stripe_account_id VARCHAR(100) NULL DEFAULT NULL,
    stripe_onboarding_complete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    label VARCHAR(50) DEFAULT 'Home',
    street VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    image_url VARCHAR(1000),
    status ENUM('pending', 'active', 'rejected') NOT NULL DEFAULT 'pending',
    weight_g INT DEFAULT 0,
    length_cm INT DEFAULT 0,
    width_cm INT DEFAULT 0,
    height_cm INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    status ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL,
    stripe_payment_intent_id VARCHAR(100) NULL DEFAULT NULL,
    tracking_number VARCHAR(100) NULL DEFAULT NULL,
    -- Snapshot of shipping address at time of order (survives address deletion)
    shipping_name VARCHAR(150) NOT NULL,
    shipping_street VARCHAR(200) NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_province VARCHAR(100) NOT NULL,
    shipping_postal_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE RESTRICT
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NULL,               -- NULL if product later deleted
    product_name VARCHAR(150) NOT NULL, -- snapshot at time of purchase
    unit_price DECIMAL(10, 2) NOT NULL, -- snapshot at time of purchase
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    order_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (product_id, buyer_id, order_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
