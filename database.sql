CREATE DATABASE IF NOT EXISTS canteen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE canteen;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'canteen_manager', 'system_admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT
);

INSERT IGNORE INTO users (name, email, password, role) VALUES
('Administrator', 'admin@canteen.local', '@Hamidu12', 'system_admin'),
('Canteen Manager', 'manager@canteen.local', '@Manager12', 'canteen_manager'),
('Student User', 'student@canteen.local', '@Student12', 'student');

INSERT IGNORE INTO menu_items (name, description, price) VALUES
('Chicken Roll', 'Grilled chicken with fresh vegetables.', 2.50),
('Chapati', 'Freshly made chapati with sides.', 1.80),
('Soft Drink', 'Cold bottled soft drink.', 1.00);
