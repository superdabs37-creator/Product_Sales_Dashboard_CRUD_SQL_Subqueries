CREATE DATABASE IF NOT EXISTS sales_dashboard;
USE sales_dashboard;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  order_date DATE DEFAULT CURRENT_DATE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Sample products
INSERT INTO products (name, price) VALUES
('Laptop', 35000.00),
('Smartphone', 18000.00),
('Headphones', 2500.00);

-- Sample orders
INSERT INTO orders (product_id, quantity, order_date) VALUES
(1, 5, '2025-09-10'),
(1, 2, '2025-10-05'),
(2, 3, '2025-09-20'),
(3, 8, '2025-10-01');
