<?php
require_once __DIR__ . '/../../config/constants.php';

namespace MediBook\Middleware;

use MediBook\Models\User;

class AuthMiddleware
{
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    public function handle($requiredRole = null)
    {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si hay una sesión activa
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            $this->redirectToLogin();
            return false;
        }
        
        // Validar la sesión en base de datos
        $session = $this->userModel->validateSession($_SESSION['session_token']);
        
        if (!$session) {
            $this->destroySession();
            $this->redirectToLogin();
            return false;
        }
        
        // Verificar rol si es requerido
        if ($requiredRole && $_SESSION['user_role'] !== $requiredRole) {
            $this->redirectToUnauthorized();
            return false;
        }
        
        return true;
    }
    
    public function requireAdmin()
    {
        return $this->handle(ROLE_ADMIN);
    }
    
    public function requireDoctor()
    {
        return $this->handle(ROLE_DOCTOR);
    }
    
    public function requirePatient()
    {
        return $this->handle(ROLE_PATIENT);
    }
    
    public function requireAnyRole()
    {
        return $this->handle();
    }
    
    private function destroySession()
    {
        if (isset($_SESSION['session_token'])) {
            $this->userModel->endUserSession($_SESSION['session_token']);
        }
        
        session_destroy();
    }
    
    private function redirectToLogin()
    {
        header('Location: /MediBook/src/Views/auth/login.php');
        exit;
    }
    
    private function redirectToUnauthorized()
    {
        header('HTTP/1.0 403 Forbidden');
        echo '<h1>403 - Acceso No Autorizado</h1>';
        echo '<p>No tiene permisos para acceder a esta página.</p>';
        echo '<a href="/MediBook/">Volver al inicio</a>';
        exit;
    }
    
    public static function getCurrentUser()
    {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ];
        }
        
        return null;
    }
    
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_ADMIN;
    }
    
    public static function isDoctor()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_DOCTOR;
    }
    
    public static function isPatient()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_PATIENT;
    }
}