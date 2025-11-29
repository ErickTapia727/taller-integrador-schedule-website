-- Crear base de datos
CREATE DATABASE IF NOT EXISTS taller_integrador_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taller_integrador_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rut VARCHAR(12) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Administrador', 'Cliente') NOT NULL DEFAULT 'Cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_rut (rut),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de mascotas
CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(100),
    age INT,
    weight DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de citas/agendamientos
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pet_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    service VARCHAR(100) NOT NULL,
    status ENUM('Pendiente', 'Confirmado', 'Completado', 'Cancelado', 'No Show') NOT NULL DEFAULT 'Pendiente',
    notes TEXT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_pet_id (pet_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_date_time (appointment_date, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Contraseña: Admin123!
INSERT INTO users (name, email, rut, phone, password, role) 
VALUES (
    'Administrador',
    'admin@dogcutespa.cl',
    '12345678-9',
    '+56912345678',
    '$2y$10$tIa3JlQABqXAn8zumHSZ4.Kig5OV5u5xugCgS.ngPSCh7hktNRWx6',
    'Administrador'
) ON DUPLICATE KEY UPDATE email=email;

-- Insertar usuario cliente de prueba
-- Contraseña: Cliente123!
INSERT INTO users (name, email, rut, phone, password, role) 
VALUES (
    'Juan Pérez',
    'cliente@example.cl',
    '98765432-1',
    '+56987654321',
    '$2y$10$9cA6AoW3ndTwjXx1x/ydEOpxwQam40i1X/jTKCyyLgtdZtPmLbYbq',
    'Cliente'
) ON DUPLICATE KEY UPDATE email=email;
