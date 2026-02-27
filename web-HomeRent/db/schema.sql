-- إيجار ويب - Schema v2 (MySQL 8+, utf8mb4)

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('site_admin','site_admin_staff','owner','staff','tenant') NOT NULL,
  owner_id INT NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_owner_id (owner_id),
  CONSTRAINT fk_users_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  name VARCHAR(180) NOT NULL,
  city VARCHAR(120) NULL,
  district VARCHAR(120) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_properties_owner (owner_id),
  FOREIGN KEY (owner_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS units (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  unit_type VARCHAR(60) NOT NULL DEFAULT 'شقة',
  name VARCHAR(180) NOT NULL,
  rent_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('available','rented') NOT NULL DEFAULT 'available',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_units_property (property_id),
  FOREIGN KEY (property_id) REFERENCES properties(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contracts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  tenant_id INT NOT NULL,
  property_id INT NOT NULL,
  unit_id INT NOT NULL,
  contract_number VARCHAR(60) NOT NULL,
  start_date DATE NOT NULL,
  months INT NOT NULL,
  due_day TINYINT NOT NULL,
  payment_plan ENUM('one','monthly','two','three','four') NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('active','ended','canceled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_owner_contractnum (owner_id, contract_number),
  INDEX idx_contracts_owner (owner_id),
  FOREIGN KEY (owner_id) REFERENCES users(id),
  FOREIGN KEY (tenant_id) REFERENCES users(id),
  FOREIGN KEY (property_id) REFERENCES properties(id),
  FOREIGN KEY (unit_id) REFERENCES units(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contract_schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contract_id INT NOT NULL,
  seq INT NOT NULL,
  due_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('unpaid','pending','paid') NOT NULL DEFAULT 'unpaid',
  paid_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_contract_seq (contract_id, seq),
  INDEX idx_schedule_contract (contract_id),
  FOREIGN KEY (contract_id) REFERENCES contracts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payment_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contract_id INT NOT NULL,
  schedule_id INT NOT NULL,
  tenant_id INT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  notes VARCHAR(255) NULL,
  uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pr_contract (contract_id),
  FOREIGN KEY (contract_id) REFERENCES contracts(id),
  FOREIGN KEY (schedule_id) REFERENCES contract_schedules(id),
  FOREIGN KEY (tenant_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attachments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  payment_request_id INT NOT NULL,
  kind ENUM('image','pdf') NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  original_name VARCHAR(255) NULL,
  mime_type VARCHAR(120) NULL,
  size_bytes INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_att_pr (payment_request_id),
  FOREIGN KEY (payment_request_id) REFERENCES payment_requests(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contract_id INT NOT NULL,
  schedule_id INT NOT NULL,
  tenant_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  source ENUM('request_approved','manual') NOT NULL DEFAULT 'request_approved',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pay_contract (contract_id),
  FOREIGN KEY (contract_id) REFERENCES contracts(id),
  FOREIGN KEY (schedule_id) REFERENCES contract_schedules(id),
  FOREIGN KEY (tenant_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  target_role ENUM('owner','staff','tenant','site_admin','site_admin_staff') NOT NULL,
  target_user_id INT NULL,
  owner_scope_id INT NULL,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  send_email TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notif_target (target_role, target_user_id),
  INDEX idx_notif_owner_scope (owner_scope_id),
  FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (owner_scope_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE OR REPLACE VIEW v_payments AS
SELECT
  p.id AS payment_id,
  p.amount,
  p.paid_at,
  p.source,
  c.contract_number,
  c.id AS contract_id,
  cs.seq,
  cs.due_date,
  u.full_name AS tenant_name,
  u.email AS tenant_email,
  pr.name AS property_name,
  un.unit_type,
  un.name AS unit_name,
  c.owner_id
FROM payments p
JOIN contracts c ON c.id = p.contract_id
JOIN contract_schedules cs ON cs.id = p.schedule_id
JOIN users u ON u.id = p.tenant_id
JOIN properties pr ON pr.id = c.property_id
JOIN units un ON un.id = c.unit_id;


CREATE TABLE IF NOT EXISTS cron_runs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_key VARCHAR(120) NOT NULL,
  run_date DATE NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_job_date (job_key, run_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
