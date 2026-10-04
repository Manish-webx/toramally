-- =====================================================================
-- Tōramally — database schema
-- Import once in phpMyAdmin (Import tab) into an empty database,
-- then import seed.sql. MySQL 5.7+ / MariaDB 10.3+, utf8mb4.
-- Covers every stage: catalogue, customers, orders, payments,
-- shipping, content, admin and security.
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE settings (
  k VARCHAR(80) NOT NULL PRIMARY KEY,
  v TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Collections hold both the craft ladder (type='craft') and editorial
-- collections such as "Hand Painted" (type='collection').
CREATE TABLE collections (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  type ENUM('craft','collection') NOT NULL DEFAULT 'collection',
  name VARCHAR(120) NOT NULL,
  scale_word VARCHAR(60) NULL,
  tagline VARCHAR(160) NULL,
  description TEXT NULL,
  how_1 TEXT NULL, how_2 TEXT NULL, how_3 TEXT NULL,
  from_price INT UNSIGNED NULL,
  lead_weeks VARCHAR(30) NULL,
  buy_mode_note VARCHAR(160) NULL,
  on_ladder TINYINT(1) NOT NULL DEFAULT 0,
  sort INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  seo_title VARCHAR(160) NULL,
  seo_desc VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  poetic VARCHAR(255) NULL,
  story TEXT NULL,
  category ENUM('Men','Women','Accessories','Everyday','Service') NOT NULL,
  line ENUM('','Classic','Special Occasion') NOT NULL DEFAULT '',
  silhouette VARCHAR(80) NOT NULL,
  last_name VARCHAR(40) NULL,
  craft_id INT UNSIGNED NULL,
  personalisation_level ENUM('House Design','Personalised','Bespoke','One of One') NOT NULL DEFAULT 'House Design',
  construction VARCHAR(60) NULL,
  material VARCHAR(60) NULL DEFAULT 'Calf',
  base_price INT UNSIGNED NOT NULL,
  availability ENUM('Ready to Ship','Made to Order','Commission') NOT NULL DEFAULT 'Made to Order',
  lead_min TINYINT UNSIGNED NOT NULL DEFAULT 5,
  lead_max TINYINT UNSIGNED NOT NULL DEFAULT 7,
  buy_mode ENUM('Add to Bag','Commission') NOT NULL DEFAULT 'Add to Bag',
  occasions VARCHAR(120) NULL,
  size_type ENUM('shoe_men','shoe_women','belt','none') NOT NULL DEFAULT 'shoe_men',
  patron_eligible TINYINT(1) NOT NULL DEFAULT 1,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  drawing_json VARCHAR(255) NULL,
  hsn_code VARCHAR(12) NULL DEFAULT '6403',
  weight_g INT UNSIGNED NULL,
  customs_desc VARCHAR(160) NULL,
  status ENUM('Draft','Published','Sold','Archived') NOT NULL DEFAULT 'Published',
  seo_title VARCHAR(160) NULL,
  seo_desc VARCHAR(255) NULL,
  sort INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_cat (category, line, status),
  CONSTRAINT fk_prod_craft FOREIGN KEY (craft_id) REFERENCES collections(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_collection (
  product_id INT UNSIGNED NOT NULL,
  collection_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, collection_id),
  CONSTRAINT fk_pc_p FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_pc_c FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_colours (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  name VARCHAR(60) NOT NULL,
  hex CHAR(7) NOT NULL DEFAULT '#5e1f2c',
  price_diff INT NOT NULL DEFAULT 0,
  sort INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_col_p FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  colour_id INT UNSIGNED NULL,
  path VARCHAR(255) NOT NULL,
  alt VARCHAR(255) NULL,
  kind ENUM('hero','angle','sole','macro','box','editorial','video') NOT NULL DEFAULT 'angle',
  sort INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_img_p FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ready stock per size. Sizes not listed are made to order.
CREATE TABLE product_stock (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  colour_id INT UNSIGNED NULL,
  size VARCHAR(20) NOT NULL,
  qty INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_stock (product_id, colour_id, size),
  CONSTRAINT fk_stock_p FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NULL,
  first_name VARCHAR(80) NULL,
  last_name VARCHAR(80) NULL,
  phone VARCHAR(40) NULL,
  country VARCHAR(60) NULL,
  is_patron TINYINT(1) NOT NULL DEFAULT 0,
  patron_since DATE NULL,
  marketing_opt_in TINYINT(1) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  status ENUM('active','guest','disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  label VARCHAR(40) NULL,
  name VARCHAR(120) NOT NULL,
  line1 VARCHAR(190) NOT NULL,
  line2 VARCHAR(190) NULL,
  city VARCHAR(80) NOT NULL,
  state VARCHAR(80) NULL,
  postcode VARCHAR(20) NOT NULL,
  country VARCHAR(60) NOT NULL DEFAULT 'India',
  phone VARCHAR(40) NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_addr_c FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_sizes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  label VARCHAR(60) NULL,
  size_uk VARCHAR(10) NOT NULL,
  last_name VARCHAR(40) NULL,
  foot_cm DECIMAL(4,1) NULL,
  notes VARCHAR(255) NULL,
  CONSTRAINT fk_size_c FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlists (
  customer_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (customer_id, product_id),
  CONSTRAINT fk_wl_c FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  CONSTRAINT fk_wl_p FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Owner registration: pairs a client already owns.
CREATE TABLE owned_pairs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  pair_name VARCHAR(160) NOT NULL,
  purchased_from VARCHAR(60) NOT NULL DEFAULT 'Kolkata flagship',
  purchase_year SMALLINT UNSIGNED NULL,
  size_uk VARCHAR(10) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_own_c FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  user_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  KEY idx_tok (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NULL,
  session_key CHAR(64) NULL,
  data_json MEDIUMTEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart_c (customer_id),
  KEY idx_cart_s (session_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(20) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NULL,
  ship_name VARCHAR(120) NOT NULL,
  ship_line1 VARCHAR(190) NOT NULL,
  ship_line2 VARCHAR(190) NULL,
  ship_city VARCHAR(80) NOT NULL,
  ship_state VARCHAR(80) NULL,
  ship_postcode VARCHAR(20) NOT NULL,
  ship_country VARCHAR(60) NOT NULL,
  bill_same TINYINT(1) NOT NULL DEFAULT 1,
  bill_json TEXT NULL,
  gstin_buyer VARCHAR(20) NULL,
  currency CHAR(3) NOT NULL DEFAULT 'INR',
  fx_rate DECIMAL(10,4) NOT NULL DEFAULT 1,
  subtotal_inr INT UNSIGNED NOT NULL,
  patron_benefit_inr INT UNSIGNED NOT NULL DEFAULT 0,
  code_benefit_inr INT UNSIGNED NOT NULL DEFAULT 0,
  shipping_inr INT UNSIGNED NOT NULL DEFAULT 0,
  total_inr INT UNSIGNED NOT NULL,
  total_charged INT UNSIGNED NOT NULL,
  gift_note VARCHAR(300) NULL,
  status ENUM('Pending Payment','Order Placed','In Workshop','Dispatched','Delivered','Cancelled') NOT NULL DEFAULT 'Pending Payment',
  payment_status ENUM('Unpaid','Paid','Failed','Cancelled','Refunded') NOT NULL DEFAULT 'Unpaid',
  gateway_code VARCHAR(30) NULL,
  is_international TINYINT(1) NOT NULL DEFAULT 0,
  shipping_rate_id INT UNSIGNED NULL,
  admin_notes TEXT NULL,
  access_token CHAR(32) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_ord_c (customer_id),
  KEY idx_ord_s (status, payment_status),
  CONSTRAINT fk_ord_c FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  colour VARCHAR(60) NULL,
  size VARCHAR(20) NULL,
  qty SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  unit_price_inr INT UNSIGNED NOT NULL,
  hsn_code VARCHAR(12) NULL,
  personalisation_json TEXT NULL,
  is_custom TINYINT(1) NOT NULL DEFAULT 0,
  made_to_order TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_oi_o FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_status_history (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  status VARCHAR(40) NOT NULL,
  note VARCHAR(500) NULL,
  photo_path VARCHAR(255) NULL,
  customer_notified TINYINT(1) NOT NULL DEFAULT 0,
  admin_id INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_osh_o FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL UNIQUE,
  invoice_no VARCHAR(30) NOT NULL UNIQUE,
  issued_at DATETIME NOT NULL,
  data_json MEDIUMTEXT NOT NULL,
  CONSTRAINT fk_inv_o FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payment_gateways (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  mode ENUM('test','live') NOT NULL DEFAULT 'test',
  region ENUM('domestic','international','both') NOT NULL DEFAULT 'both',
  credentials_enc TEXT NULL,
  display_label VARCHAR(120) NULL,
  sort INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  gateway_code VARCHAR(30) NOT NULL,
  gateway_ref VARCHAR(120) NULL,
  gateway_payment_id VARCHAR(120) NULL,
  amount INT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL,
  status ENUM('created','success','failed','cancelled','refunded') NOT NULL DEFAULT 'created',
  verified TINYINT(1) NOT NULL DEFAULT 0,
  raw_json MEDIUMTEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_pay_ref (gateway_code, gateway_ref),
  CONSTRAINT fk_pay_o FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipping_partners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  driver VARCHAR(30) NOT NULL DEFAULT 'manual',
  scope ENUM('domestic','international','both') NOT NULL DEFAULT 'domestic',
  account_no VARCHAR(80) NULL,
  credentials_enc TEXT NULL,
  tracking_url_format VARCHAR(255) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipping_rates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  scope ENUM('domestic','international') NOT NULL,
  name VARCHAR(80) NOT NULL,
  countries VARCHAR(500) NULL,
  rate_inr INT UNSIGNED NOT NULL DEFAULT 0,
  free_above_inr INT UNSIGNED NULL,
  partner_id INT UNSIGNED NULL,
  eta_text VARCHAR(80) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  partner_id INT UNSIGNED NULL,
  tracking_no VARCHAR(80) NULL,
  last_status VARCHAR(120) NULL,
  shipped_at DATETIME NULL,
  delivered_at DATETIME NULL,
  last_sync_at DATETIME NULL,
  CONSTRAINT fk_sh_o FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE commissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(20) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  contact VARCHAR(190) NOT NULL,
  build_json TEXT NOT NULL,
  estimate_inr INT UNSIGNED NULL,
  quote_inr INT UNSIGNED NULL,
  status ENUM('Enquiry received','Consultation','Design proof and quote','Approved','In making','Final photographs','Dispatched','Delivered','Closed') NOT NULL DEFAULT 'Enquiry received',
  admin_notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kind VARCHAR(30) NOT NULL,
  name VARCHAR(120) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(60) NULL,
  payload_json TEXT NOT NULL,
  status ENUM('New','Replied','Closed') NOT NULL DEFAULT 'New',
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_enq (kind, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer uploads (reference images, owned-pair photos). Stored privately in /storage.
CREATE TABLE uploads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_type ENUM('enquiry','commission','owned_pair','order_item','customer') NOT NULL,
  owner_id INT UNSIGNED NOT NULL,
  path VARCHAR(255) NOT NULL,
  original_name VARCHAR(190) NULL,
  mime VARCHAR(80) NULL,
  size_bytes INT UNSIGNED NULL,
  kind VARCHAR(30) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_up (owner_type, owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  source VARCHAR(40) NULL,
  status ENUM('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  title VARCHAR(160) NOT NULL,
  body_html MEDIUMTEXT NULL,
  seo_title VARCHAR(160) NULL,
  seo_desc VARCHAR(255) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page sections: reorderable, hideable, text editable from the admin.
CREATE TABLE content_blocks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page VARCHAR(40) NOT NULL DEFAULT 'home',
  block_key VARCHAR(40) NOT NULL,
  data_json TEXT NULL,
  sort INT NOT NULL DEFAULT 0,
  visible TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_block (page, block_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE press_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  note VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  logo_path VARCHAR(255) NULL,
  kind ENUM('press','stockist','event') NOT NULL DEFAULT 'press',
  sort INT NOT NULL DEFAULT 0,
  visible TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE celebrities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  occasion VARCHAR(160) NULL,
  pair_worn VARCHAR(160) NULL,
  product_id INT UNSIGNED NULL,
  photo_path VARCHAR(255) NULL,
  consent_on_file TINYINT(1) NOT NULL DEFAULT 0,
  sort INT NOT NULL DEFAULT 0,
  visible TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE videos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  url VARCHAR(255) NOT NULL,
  poster_path VARCHAR(255) NULL,
  caption VARCHAR(255) NULL,
  sort INT NOT NULL DEFAULT 0,
  visible TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE journal_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  category ENUM('Craft','Style','Materials','People','Places','Stories') NOT NULL DEFAULT 'Craft',
  title VARCHAR(190) NOT NULL,
  dek VARCHAR(255) NULL,
  body_html MEDIUMTEXT NULL,
  craft_id INT UNSIGNED NULL,
  cover_path VARCHAR(255) NULL,
  status ENUM('Draft','Published','In preparation') NOT NULL DEFAULT 'Published',
  published_at DATETIME NULL,
  seo_title VARCHAR(160) NULL,
  seo_desc VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('owner','sales','workshop','content','developer') NOT NULL DEFAULT 'sales',
  active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  scope ENUM('customer','admin','form') NOT NULL,
  identifier VARCHAR(190) NOT NULL,
  ip VARCHAR(45) NOT NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_la (scope, identifier, created_at),
  KEY idx_la_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id INT UNSIGNED NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(40) NULL,
  entity_id INT UNSIGNED NULL,
  details VARCHAR(500) NULL,
  ip VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE email_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  to_email VARCHAR(190) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  template VARCHAR(60) NULL,
  status ENUM('sent','failed') NOT NULL,
  error VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
