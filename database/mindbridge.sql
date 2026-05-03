-- MindBridge Database Schema
-- MySQL with PDO

CREATE DATABASE IF NOT EXISTS mindbridge;
USE mindbridge;

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Patients table (extends users)
CREATE TABLE patients (
    user_id INT PRIMARY KEY,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    medical_history TEXT,
    current_medications TEXT,
    allergies TEXT,
    insurance_provider VARCHAR(100),
    insurance_id VARCHAR(50),
    preferred_language VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Therapists table (extends users)
CREATE TABLE therapists (
    user_id INT PRIMARY KEY,
    license_number VARCHAR(50) NOT NULL UNIQUE,
    specialization VARCHAR(100),
    years_experience INT,
    bio TEXT,
    availability_schedule TEXT, -- JSON or serialized
    hourly_rate DECIMAL(10,2),
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Admins table (extends users)
CREATE TABLE admins (
    user_id INT PRIMARY KEY,
    department VARCHAR(100),
    permissions TEXT, -- JSON
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Clinic Managers table (extends users)
CREATE TABLE clinic_managers (
    user_id INT PRIMARY KEY,
    clinic_name VARCHAR(100),
    clinic_address TEXT,
    clinic_phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sessions table
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    therapist_id INT NOT NULL,
    scheduled_date DATETIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    status ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    session_type ENUM('individual', 'group', 'family') DEFAULT 'individual',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(user_id),
    FOREIGN KEY (therapist_id) REFERENCES therapists(user_id)
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'insurance', 'online') DEFAULT 'online',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);

-- Refund Requests table
CREATE TABLE refund_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    reason TEXT,
    amount DECIMAL(10,2),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id)
);

-- Intake Forms table
CREATE TABLE intake_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    form_data TEXT, -- JSON
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'submitted', 'reviewed') DEFAULT 'draft',
    FOREIGN KEY (patient_id) REFERENCES patients(user_id)
);

-- Consent Forms table
CREATE TABLE consent_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    form_type VARCHAR(100),
    consent_given BOOLEAN DEFAULT FALSE,
    signed_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    FOREIGN KEY (patient_id) REFERENCES patients(user_id)
);

-- Mood Tracker table
CREATE TABLE mood_trackers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    mood_level INT NOT NULL, -- 1-10
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(user_id)
);

-- Journals table
CREATE TABLE journals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    title VARCHAR(255),
    content TEXT,
    is_private BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(user_id)
);

-- Resources table
CREATE TABLE resources (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    type ENUM('article', 'video', 'audio', 'pdf', 'link') DEFAULT 'article',
    category VARCHAR(100),
    author_id INT,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Forums table
CREATE TABLE forums (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    forum_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (forum_id) REFERENCES forums(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Comments table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Crisis Alerts table
CREATE TABLE crisis_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    alert_type VARCHAR(100),
    message TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    triggered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    handled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (patient_id) REFERENCES patients(user_id)
);

-- Feedback table
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    session_id INT,
    rating INT, -- 1-5
    comments TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id),
    FOREIGN KEY (to_user_id) REFERENCES users(id),
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);

-- Audit Logs table
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values TEXT, -- JSON
    new_values TEXT, -- JSON
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Reports table
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    type ENUM('session', 'mood', 'therapist', 'audit') DEFAULT 'session',
    generated_by INT NOT NULL,
    parameters TEXT, -- JSON
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id)
);

-- Disputes table
CREATE TABLE disputes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    raised_by INT NOT NULL,
    reason TEXT,
    status ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
    resolution TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id),
    FOREIGN KEY (raised_by) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('reminder', 'alert', 'system', 'emergency') DEFAULT 'system',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('patient', 'Mental health patient'),
('therapist', 'Licensed therapist'),
('admin', 'System administrator'),
('clinic_manager', 'Clinic manager');

-- Insert sample users (passwords are hashed for 'password123')
INSERT INTO users (username, email, password, role_id, first_name, last_name) VALUES
('admin', 'admin@mindbridge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'System', 'Admin'),
('therapist1', 'therapist@mindbridge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Dr. Jane', 'Smith'),
('patient1', 'patient@mindbridge.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'John', 'Doe');

-- Insert corresponding role-specific data
INSERT INTO admins (user_id, department) VALUES (1, 'IT');
INSERT INTO therapists (user_id, license_number, specialization) VALUES (2, 'LIC12345', 'Clinical Psychology');
INSERT INTO patients (user_id) VALUES (3);