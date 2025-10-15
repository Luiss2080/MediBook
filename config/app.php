<?php
/**
 * Configuración general de la aplicación MediBook
 */

return [
    'name' => 'MediBook',
    'version' => '1.0.0',
    'environment' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => 'America/Mexico_City',
    
    // Configuración de sesiones
    'session' => [
        'name' => 'medibook_session',
        'lifetime' => 7200, // 2 horas
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
    ],
    
    // Configuración de seguridad
    'security' => [
        'csrf_token_name' => '_token',
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutos
    ]
];