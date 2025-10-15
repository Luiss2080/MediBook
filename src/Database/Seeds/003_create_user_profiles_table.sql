-- Seed: Datos iniciales para tabla user_profiles
-- Fecha: 2025-10-14
-- Descripción: Perfiles iniciales para los usuarios de prueba

-- Insertar perfiles para los usuarios existentes
INSERT INTO user_profiles (
    user_id,
    specialization,
    license_number,
    medical_history,
    emergency_contact,
    emergency_phone,
    address,
    city,
    state,
    postal_code,
    birth_date,
    gender,
    blood_type,
    allergies,
    current_medications
) VALUES 
-- Perfil para Admin (ID 1)
(1, NULL, NULL, NULL, NULL, NULL, 'Calle Principal 123', 'Ciudad de México', 'CDMX', '12345', '1980-01-01', 'male', NULL, NULL, NULL),

-- Perfil para Doctor 1 (ID 2)
(2, 'Cardiología', 'MED-001-2023', NULL, NULL, NULL, 'Av. Médica 456', 'Ciudad de México', 'CDMX', '12346', '1975-05-15', 'male', NULL, NULL, NULL),

-- Perfil para Patient 1 (ID 3)
(3, NULL, NULL, 'Sin antecedentes relevantes', 'Ana González', '+1234567895', 'Calle Paciente 789', 'Ciudad de México', 'CDMX', '12347', '1990-08-20', 'female', 'O+', 'Ninguna conocida', 'Vitaminas'),

-- Perfil para Doctor 2 (ID 4)
(4, 'Pediatría', 'MED-002-2023', NULL, NULL, NULL, 'Blvd. Hospital 321', 'Ciudad de México', 'CDMX', '12348', '1982-03-10', 'female', NULL, NULL, NULL),

-- Perfil para Patient 2 (ID 5)
(5, NULL, NULL, 'Hipertensión controlada', 'Laura Rodríguez', '+1234567896', 'Calle Salud 654', 'Ciudad de México', 'CDMX', '12349', '1985-12-03', 'male', 'A+', 'Penicilina', 'Losartán 50mg');