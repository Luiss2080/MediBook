-- Seed: Datos iniciales para tabla users
-- Fecha: 2025-10-14
-- Descripción: Usuarios iniciales con roles admin, doctor y patient

-- Insertar usuarios iniciales (contraseñas hasheadas para 'password123')
INSERT INTO users (
    username, 
    email, 
    password, 
    first_name, 
    last_name, 
    role, 
    phone, 
    status, 
    email_verified
) VALUES 
-- Usuario Administrador
(
    'admin', 
    'admin@medibook.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Administrador', 
    'Sistema', 
    'admin', 
    '+1234567890', 
    'active', 
    TRUE
),
-- Usuario Doctor
(
    'doctor1', 
    'doctor@medibook.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Dr. Juan', 
    'Pérez', 
    'doctor', 
    '+1234567891', 
    'active', 
    TRUE
),
-- Usuario Paciente
(
    'patient1', 
    'patient@medibook.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'María', 
    'González', 
    'patient', 
    '+1234567892', 
    'active', 
    TRUE
),
-- Doctor adicional
(
    'doctor2', 
    'doctor2@medibook.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Dra. Ana', 
    'López', 
    'doctor', 
    '+1234567893', 
    'active', 
    TRUE
),
-- Paciente adicional
(
    'patient2', 
    'patient2@medibook.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Carlos', 
    'Rodríguez', 
    'patient', 
    '+1234567894', 
    'active', 
    TRUE
);

-- Actualizar la fecha de último login para simular actividad reciente
UPDATE users SET last_login = NOW() WHERE username IN ('admin', 'doctor1', 'patient1');