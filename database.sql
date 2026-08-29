CREATE DATABASE IF NOT EXISTS prime2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE prime2026;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('admin','editor') NOT NULL DEFAULT 'editor', active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO users (id,name,email,password_hash,role,active) VALUES (1,'Đỗ Hà Lương','dohaluong@gmail.com','$2y$05$mmxV2OPvB14u4knqI6vYvO9FtL4C72CNym/Zys18vQIHq6KcJJuWK','admin',1);
CREATE TABLE IF NOT EXISTS categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, code VARCHAR(80) NOT NULL UNIQUE,
 image VARCHAR(500) DEFAULT NULL, coefficient DECIMAL(8,2) NOT NULL DEFAULT 1.00, notes TEXT DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS collections (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, code VARCHAR(80) NOT NULL UNIQUE,
 image VARCHAR(500) DEFAULT NULL, coefficient DECIMAL(8,2) NOT NULL DEFAULT 1.00, notes TEXT DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS materials (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, code VARCHAR(80) NOT NULL UNIQUE,
 coefficient DECIMAL(8,2) NOT NULL DEFAULT 1.00, notes TEXT DEFAULT NULL, image VARCHAR(500) DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS material_colors (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, material_id INT UNSIGNED NOT NULL, code VARCHAR(100) NOT NULL,
 image VARCHAR(500) NOT NULL, hex_code CHAR(7) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_material_color_code (material_id,code), CONSTRAINT fk_material_colors_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
);
INSERT IGNORE INTO categories (name,code,image,coefficient,notes) VALUES
('Sofa giường mở điện','sofa-giuong-mo-dien','https://ima.vn/assets/uploads/media/Famigo_000275.jpg',1.00,'Các mẫu sofa chuyển thành giường bằng cơ cấu điện.'),
('Sofa chỉnh điện','sofa-chinh-dien','https://ima.vn/assets/uploads/media/6a151bce2644c_IMA04360.jpg',1.05,'Dòng recliner có motor ngả độc lập.'),
('Sofa modular / góc','sofa-modular','https://ima.vn/assets/uploads/media/Trio_000289.jpg',1.10,'Các mẫu sofa góc, linh hoạt theo không gian.'),
('Giường nâng điện','giuong-nang-dien','https://ima.vn/assets/uploads/media/Famigo_000271.jpg',1.15,'Giường điều chỉnh tư thế bằng điện.');
INSERT IGNORE INTO collections (name,code,image,coefficient,notes) VALUES
('Bộ sưu tập FAMIGO','famigo','https://ima.vn/assets/uploads/media/Famigo_000275.jpg',1.00,'Sofa giường tiện nghi cho căn hộ.'),
('Bộ sưu tập CASA','casa','https://ima.vn/assets/uploads/media/6a151bce2644c_IMA04360.jpg',1.05,'Thiết kế recliner hiện đại.'),
('Bộ sưu tập NAPOLI','napoli','https://ima.vn/assets/uploads/media/Napoli_000153.jpg',1.00,'Tối ưu cho không gian phòng khách đô thị.');
INSERT IGNORE INTO materials (name,code,coefficient,notes,image) VALUES
('Vải bố kháng nước','vai-bo-khang-nuoc',1.00,'Bề mặt dệt mịn, dễ vệ sinh, phù hợp sử dụng hàng ngày.','/Prime-2/uploads/products/prime-sample/Famigo_000271.jpg'),
('Da công nghiệp','da-cong-nghiep',1.15,'Bề mặt mềm, chống thấm nhẹ.','/Prime-2/uploads/products/prime-sample/6a151bc8eb507_IMA04350.jpg');
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(120) UNIQUE NOT NULL, name VARCHAR(255) NOT NULL,
 type VARCHAR(120) NOT NULL, category_id INT UNSIGNED DEFAULT NULL, collection_id INT UNSIGNED DEFAULT NULL, description TEXT, detailed_description TEXT NULL, specifications_json JSON NULL, price DECIMAL(12,0) NOT NULL, rating DECIMAL(2,1) DEFAULT 5.0,
 reviews INT DEFAULT 0, image VARCHAR(500), video_url VARCHAR(500) DEFAULT NULL, status VARCHAR(100), fast TINYINT(1) DEFAULT 1, featured TINYINT(1) DEFAULT 0, active TINYINT(1) DEFAULT 1
);
CREATE TABLE IF NOT EXISTS product_images (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, image_url VARCHAR(500) NOT NULL,
 is_featured TINYINT(1) NOT NULL DEFAULT 0, sort_order INT UNSIGNED NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_product_image (product_id,image_url),
 CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS product_size_options (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, name VARCHAR(80) NOT NULL, details_json JSON NULL, sort_order INT UNSIGNED NOT NULL DEFAULT 0,
 UNIQUE KEY uq_product_size (product_id,name), CONSTRAINT fk_product_sizes_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS product_variant_prices (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, material_id INT UNSIGNED NOT NULL, size_option_id INT UNSIGNED NOT NULL, price DECIMAL(12,0) DEFAULT NULL,
 UNIQUE KEY uq_variant_price (product_id,material_id,size_option_id), CONSTRAINT fk_variant_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT fk_variant_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE, CONSTRAINT fk_variant_size FOREIGN KEY (size_option_id) REFERENCES product_size_options(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS product_variant_materials (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, material_id INT UNSIGNED NOT NULL, color_ids_json JSON NULL,
 UNIQUE KEY uq_product_variant_material (product_id,material_id), CONSTRAINT fk_variant_row_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT fk_variant_row_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS product_reviews (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id INT UNSIGNED NOT NULL, customer_name VARCHAR(120) NOT NULL, rating TINYINT UNSIGNED NOT NULL DEFAULT 5, content TEXT NOT NULL, status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS product_review_images (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, review_id BIGINT UNSIGNED NOT NULL, image_url VARCHAR(500) NOT NULL, sort_order INT UNSIGNED NOT NULL DEFAULT 0,
 CONSTRAINT fk_review_images_review FOREIGN KEY (review_id) REFERENCES product_reviews(id) ON DELETE CASCADE
);
INSERT IGNORE INTO products (slug,name,type,description,price,rating,reviews,image,status,fast,featured,active) VALUES
('famigo','Sofa băng FAMIGO','Sofa giường mở điện','Sofa giường mở điện · hộc đồ dưới đệm',24400000,4.9,12,'/Prime-2/uploads/products/prime-sample/Famigo_000271.jpg','Giao nhanh 2-3 ngày',1,1,1),
('casa','Sofa băng CASA','Sofa chỉnh điện','Sofa chỉnh điện recliner · 2 ngả độc lập',19700000,5.0,8,'/Prime-2/uploads/products/prime-sample/6a151bc8eb507_IMA04350.jpg','Giao nhanh 2-3 ngày',1,1,1),
('trio','Sofa băng TRIO','Sofa modular / góc đa năng','Sofa modular · ghế nghỉ tháo rời được',20700000,4.8,9,'/Prime-2/uploads/products/prime-sample/Trio_000289.jpg','Giao nhanh 2-3 ngày',1,1,1),
('napoli','Sofa băng NAPOLI','Sofa chỉnh điện','Thiết kế tối ưu không gian phòng khách · 2 phiên bản',16500000,4.9,15,'/Prime-2/uploads/products/prime-sample/Napoli_000153.jpg','Giao nhanh 2-3 ngày',1,1,1),
('casa-large','Sofa băng CASA 2.6m','Sofa góc chỉnh điện','Bản góc lớn · chỉnh điện 3 vị trí',23400000,4.9,6,'https://ima.vn/assets/uploads/media/Casa_000048.jpg','Đặt hàng — giao sau 14 ngày',0,0,1),
('napoli-compact','Sofa băng NAPOLI 1.8m','Sofa giường mở điện','Bản gọn cho căn hộ 1–2 phòng ngủ',13500000,4.8,11,'https://ima.vn/assets/uploads/media/Napoli_000326.jpg','Giao nhanh 2-3 ngày',1,0,1),
('luna-bed','Giường nâng điện LUNA','Giường nâng điện','Nâng đầu và chân độc lập · điều khiển không dây',28900000,4.9,4,'https://ima.vn/assets/uploads/media/Famigo_000271.jpg','Đặt hàng — giao sau 14 ngày',0,0,0),
('famigo-ottoman','Đôn FAMIGO','Phụ kiện & mua kèm','Đôn 60 × 60cm · cùng vải với sofa FAMIGO',3200000,5.0,3,'https://ima.vn/assets/uploads/media/Famigo_000275.jpg','Giao nhanh 2-3 ngày',1,0,1);
INSERT IGNORE INTO product_images (product_id,image_url,is_featured,sort_order) SELECT id,'/Prime-2/uploads/products/prime-sample/Famigo_000271.jpg',1,1 FROM products WHERE slug='famigo';
INSERT IGNORE INTO product_images (product_id,image_url,is_featured,sort_order) SELECT id,'/Prime-2/uploads/products/prime-sample/6a151bc8eb507_IMA04350.jpg',1,1 FROM products WHERE slug='casa';
INSERT IGNORE INTO product_images (product_id,image_url,is_featured,sort_order) SELECT id,'/Prime-2/uploads/products/prime-sample/Trio_000289.jpg',1,1 FROM products WHERE slug='trio';
INSERT IGNORE INTO product_images (product_id,image_url,is_featured,sort_order) SELECT id,'/Prime-2/uploads/products/prime-sample/Napoli_000153.jpg',1,1 FROM products WHERE slug='napoli';
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(40) NOT NULL UNIQUE,
  customer_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(160) NULL,
  city VARCHAR(120) NULL,
  district VARCHAR(120) NULL,
  address VARCHAR(255) NOT NULL,
  delivery_date DATE NULL,
  delivery_time VARCHAR(80) NULL,
  payment_method VARCHAR(80) NULL,
  notes TEXT NULL,
  subtotal DECIMAL(14,0) NOT NULL DEFAULT 0,
  status VARCHAR(40) NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  product_image VARCHAR(500) NULL,
  option_text VARCHAR(255) NULL,
  unit_price DECIMAL(14,0) NOT NULL DEFAULT 0,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS about_content (
  id INT UNSIGNED PRIMARY KEY,
  hero_title VARCHAR(255) NULL,
  hero_image VARCHAR(500) NULL,
  lede_html TEXT NULL,
  duo_image_1 VARCHAR(500) NULL,
  duo_image_2 VARCHAR(500) NULL,
  section1_heading VARCHAR(255) NULL,
  section1_html TEXT NULL,
  stats_json JSON NULL,
  section2_heading VARCHAR(255) NULL,
  section2_html TEXT NULL,
  trio_image_1 VARCHAR(500) NULL,
  trio_image_2 VARCHAR(500) NULL,
  trio_image_3 VARCHAR(500) NULL,
  cta_heading VARCHAR(255) NULL,
  cta_text TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
