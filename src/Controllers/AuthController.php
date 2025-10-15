<?php
namespace MediBook\Controllers;

require_once __DIR__ . '/../../config/constants.php';

use MediBook\Models\User;
use MediBook\Services\EmailService;

class AuthController
{
    private $userModel;
    private $emailService;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->emailService = new EmailService();
    }
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor, complete todos los campos.';
                require_once __DIR__ . '/../Views/auth/login.php';
                return;
            }
            
            // Verificar intentos de login previos
            if ($this->userModel->checkLoginAttempts($email)) {
                $error = 'Demasiados intentos fallidos. Intente nuevamente en 15 minutos.';
                require_once __DIR__ . '/../Views/auth/login.php';
                return;
            }
            
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                // Crear sesión de usuario
                $sessionToken = $this->userModel->createUserSession($user['id']);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['session_token'] = $sessionToken;
                
                // Registrar intento exitoso
                $this->userModel->logLoginAttempt($email, true);
                
                // Redirigir según el rol del usuario
                switch ($user['role']) {
                    case ROLE_ADMIN:
                        header('Location: /MediBook/src/Views/admin/dashboard.php');
                        break;
                    case ROLE_PATIENT:
                        header('Location: /MediBook/src/Views/patient/dashboard.php');
                        break;
                    case ROLE_DOCTOR:
                        header('Location: /MediBook/src/Views/doctor/dashboard.php');
                        break;
                    default:
                        header('Location: /MediBook/');
                }
                exit;
            } else {
                // Registrar intento fallido
                $this->userModel->logLoginAttempt($email, false);
                $error = 'Credenciales incorrectas.';
                require_once __DIR__ . '/../Views/auth/login.php';
                return;
            }
        }
        
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    
    public function logout()
    {
        if (isset($_SESSION['session_token'])) {
            $this->userModel->endUserSession($_SESSION['session_token']);
        }
        
        session_destroy();
        header('Location: /MediBook/');
        exit;
    }
    
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'logout':
                    $this->logout();
                    break;
                default:
                    header('Location: /MediBook/');
                    exit;
            }
        }
    }
    
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'role' => 'patient' // Por defecto
            ];
            
            // Validaciones básicas
            if (empty($data['username']) || empty($data['email']) || 
                empty($data['password']) || empty($data['first_name']) || 
                empty($data['last_name'])) {
                $error = 'Por favor, complete todos los campos.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }
            
            if ($this->userModel->findByEmail($data['email'])) {
                $error = 'El email ya está registrado.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }
            
            if ($this->userModel->create($data)) {
                $success = 'Usuario registrado exitosamente. Puede iniciar sesión.';
                require_once __DIR__ . '/../Views/auth/login.php';
                return;
            } else {
                $error = 'Error al registrar el usuario.';
                require_once __DIR__ . '/../Views/auth/register.php';
                return;
            }
        }
        
        require_once __DIR__ . '/../Views/auth/register.php';
    }
    
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'type' => 'danger',
                    'message' => 'Por favor, ingrese una dirección de email válida.'
                ];
            }
            
            // Verificar si el usuario existe
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                // Por seguridad, no revelamos si el email existe o no
                return [
                    'type' => 'success',
                    'message' => 'Si el email está registrado, recibirás un enlace de recuperación en unos minutos.'
                ];
            }
            
            // Generar token de recuperación
            $result = $this->userModel->createPasswordResetToken($email);
            
            if ($result) {
                // Enviar email de recuperación
                $emailSent = $this->emailService->sendPasswordResetEmail($email, $result['token']);
                
                if ($emailSent) {
                    return [
                        'type' => 'success',
                        'message' => 'Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.'
                    ];
                } else {
                    return [
                        'type' => 'danger',
                        'message' => 'Error al enviar el email. Intenta nuevamente más tarde.'
                    ];
                }
            } else {
                return [
                    'type' => 'danger',
                    'message' => 'Error al procesar la solicitud. Intenta nuevamente.'
                ];
            }
        }
        
        return ['type' => '', 'message' => ''];
    }
    
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            if (empty($token) || empty($password) || empty($passwordConfirm)) {
                return [
                    'type' => 'danger',
                    'message' => 'Todos los campos son obligatorios.'
                ];
            }
            
            if ($password !== $passwordConfirm) {
                return [
                    'type' => 'danger',
                    'message' => 'Las contraseñas no coinciden.'
                ];
            }
            
            if (strlen($password) < 8) {
                return [
                    'type' => 'danger',
                    'message' => 'La contraseña debe tener al menos 8 caracteres.'
                ];
            }
            
            // Validar fortaleza de contraseña
            if (!$this->isStrongPassword($password)) {
                return [
                    'type' => 'danger',
                    'message' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.'
                ];
            }
            
            // Verificar token y resetear contraseña
            $result = $this->userModel->resetPasswordWithToken($token, $password);
            
            if ($result) {
                return [
                    'type' => 'success',
                    'message' => '¡Contraseña actualizada exitosamente! Ya puedes iniciar sesión con tu nueva contraseña.'
                ];
            } else {
                return [
                    'type' => 'danger',
                    'message' => 'Token inválido o expirado. Solicita un nuevo enlace de recuperación.'
                ];
            }
        }
        
        return ['type' => '', 'message' => ''];
    }
    
    public function validateResetToken($token)
    {
        return $this->userModel->validatePasswordResetToken($token);
    }

    
    private function isStrongPassword($password)
    {
        return strlen($password) >= 8 &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/\d/', $password);
    }
}