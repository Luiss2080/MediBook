<?php
/**
 * MediBook - Sistema de Gestión Médica
 * Punto de entrada principal
 */

// Iniciar sesión
session_start();

// Incluir configuración
require_once __DIR__ . '/config/constants.php';

// Si el usuario ya está autenticado, redirigir según su rol
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case ROLE_ADMIN:
            header('Location: /MediBook/src/Views/admin/dashboard.php');
            exit;
        case ROLE_DOCTOR:
            header('Location: /MediBook/src/Views/doctor/dashboard.php');
            exit;
        case ROLE_PATIENT:
            header('Location: /MediBook/src/Views/patient/dashboard.php');
            exit;
    }
}

// Mostrar página de bienvenida
require_once __DIR__ . '/src/Views/Home/welcome.php';
