-- MindBridge database schema
CREATE DATABASE IF NOT EXISTS mindbridge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mindbridge;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('patient','therapist','admin') NOT NULL DEFAULT 'patient',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patient_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  timezone VARCHAR(50) DEFAULT 'UTC',
  preferences TEXT,
  intake_status ENUM('pending','completed','review') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE therapist_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  specialty VARCHAR(120) DEFAULT 'General',
  license_number VARCHAR(120) DEFAULT NULL,
  availability VARCHAR(120) DEFAULT 'weekdays',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  therapist_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  timezone VARCHAR(80) NOT NULL DEFAULT 'UTC',
  status ENUM('scheduled','confirmed','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  notes TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (therapist_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  patient_id INT NOT NULL,
  therapist_id INT NOT NULL,
  session_state ENUM('pending','in_progress','completed','missed') NOT NULL DEFAULT 'pending',
  record_link VARCHAR(255) DEFAULT NULL,
  scheduled_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (therapist_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  patient_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'USD',
  status ENUM('completed','pending','failed','refunded') NOT NULL DEFAULT 'completed',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE refund_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  payment_id INT NOT NULL,
  patient_id INT NOT NULL,
  reason TEXT,
  status ENUM('pending','approved','denied') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE intake_forms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  preferences TEXT,
  goals TEXT,
  history_notes TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE consent_forms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  consent_type VARCHAR(120) NOT NULL,
  content TEXT NOT NULL,
  given_at DATETIME NOT NULL,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mood_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  mood_level TINYINT NOT NULL,
  note TEXT,
  mood_date DATE NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE journal_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  content TEXT NOT NULL,
  mood_tag VARCHAR(80) DEFAULT 'neutral',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE resources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  description TEXT,
  type VARCHAR(80) NOT NULL,
  file_path VARCHAR(255),
  created_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  subject VARCHAR(180),
  body TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE forums (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  description TEXT,
  is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
  created_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  forum_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  target_type VARCHAR(120) NOT NULL,
  target_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comments TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_type VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  title VARCHAR(180) NOT NULL,
  content TEXT NOT NULL,
  category VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE disputes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  appointment_id INT NOT NULL,
  issue TEXT NOT NULL,
  status ENUM('pending','resolved','rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  file_type VARCHAR(80) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE crisis_alerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  detected_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, role) VALUES
('Admin MindBridge', 'admin@mindbridge.local', '$2y$10$7wIvzorqUEOitjpkbBWyZOlVI5.Ta2zamjjkO49IPz0HwfcL0iC/i', 'admin'),
('Patient One', 'patient@mindbridge.local', '$2y$10$7wIvzorqUEOitjpkbBWyZOlVI5.Ta2zamjjkO49IPz0HwfcL0iC/i', 'patient'),
('Therapist One', 'therapist@mindbridge.local', '$2y$10$7wIvzorqUEOitjpkbBWyZOlVI5.Ta2zamjjkO49IPz0HwfcL0iC/i', 'therapist');

INSERT INTO forums (title, description, created_by) VALUES
('Stress Management Support', 'A safe anonymous space for sharing healthy coping strategies.', 1);
