-- Migración: Crear tabla de perfiles de usuario
-- Fecha: 2025-10-14
-- Descripción: Información adicional de perfiles según el rol de usuario

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    specialization VARCHAR(100) NULL, -- Para doctores
    license_number VARCHAR(50) NULL, -- Para doctores
    medical_history TEXT NULL, -- Para pacientes
    emergency_contact VARCHAR(100) NULL, -- Para pacientes
    emergency_phone VARCHAR(20) NULL, -- Para pacientes
    address TEXT NULL,
    city VARCHAR(50) NULL,
    state VARCHAR(50) NULL,
    postal_code VARCHAR(10) NULL,
    country VARCHAR(50) DEFAULT 'México',
    birth_date DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
    allergies TEXT NULL,
    current_medications TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_profile (user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_specialization (specialization)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;