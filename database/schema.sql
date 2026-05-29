-- Create Database
CREATE DATABASE IF NOT EXISTS assignment_system;
USE assignment_system;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Occupations Table
CREATE TABLE IF NOT EXISTS occupations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Levels Table
CREATE TABLE IF NOT EXISTS levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment Centers Table
CREATE TABLE IF NOT EXISTS assessment_centers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Candidates Table
CREATE TABLE IF NOT EXISTS candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    gender ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    qualification VARCHAR(100),
    experience INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    is_supervisor BOOLEAN DEFAULT false,
    is_assessor BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supervisor (is_supervisor),
    INDEX idx_assessor (is_assessor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assignments Table
CREATE TABLE IF NOT EXISTS assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    occupation_id INT NOT NULL,
    level_id INT NOT NULL,
    center_id INT NOT NULL,
    assessment_date DATE NOT NULL,
    num_supervisors INT NOT NULL,
    num_assessors INT NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (occupation_id) REFERENCES occupations(id),
    FOREIGN KEY (level_id) REFERENCES levels(id),
    FOREIGN KEY (center_id) REFERENCES assessment_centers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_date (assessment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assignment Details Table
CREATE TABLE IF NOT EXISTS assignment_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    candidate_id INT NOT NULL,
    role ENUM('supervisor', 'assessor') NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('assigned', 'completed', 'cancelled') DEFAULT 'assigned',
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    UNIQUE KEY unique_assignment (assignment_id, candidate_id, role),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reports Table
CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    type VARCHAR(50) NOT NULL,
    assignment_id INT,
    file_path VARCHAR(255),
    format VARCHAR(20),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_type (type),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Audit Log Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Data
INSERT INTO levels (name, description) VALUES
('Beginner', 'Beginner Level'),
('Intermediate', 'Intermediate Level'),
('Advanced', 'Advanced Level'),
('Expert', 'Expert Level');

INSERT INTO occupations (name, description) VALUES
('Electrician', 'Electrical Work'),
('Plumber', 'Plumbing Work'),
('Carpenter', 'Carpentry Work'),
('Welder', 'Welding Work'),
('Mechanic', 'Mechanical Work');

INSERT INTO assessment_centers (name, location, address) VALUES
('Center 1', 'Downtown', '123 Main Street'),
('Center 2', 'North', '456 North Avenue'),
('Center 3', 'South', '789 South Boulevard');

-- Create admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@assignment.com', '$2y$10$VsIGSqG2dH5iJn5qYqWR3uQ8V6.EfKfSZP6qLM2zG1yJ8B7fN6Jey', 'System Administrator', 'admin', 'active');
