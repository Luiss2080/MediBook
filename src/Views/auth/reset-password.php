<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - MediBook</title>
    <link rel="icon" type="image/png" href="../../../public/assets/img/LogoMediBook.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(45deg, #51cf66, #69db7c);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .card-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .card-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #51cf66;
            box-shadow: 0 0 0 0.2rem rgba(81, 207, 102, 0.25);
        }
        
        .btn-reset {
            background: linear-gradient(45deg, #51cf66, #69db7c);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background: linear-gradient(45deg, #40c057, #51cf66);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(81, 207, 102, 0.3);
            color: white;
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid #6c757d;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            color: #6c757d;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-back:hover {
            background: #6c757d;
            color: white;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .password-requirements h6 {
            color: #495057;
            margin-bottom: 10px;
        }
        
        .requirement {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .requirement i {
            width: 15px;
            margin-right: 8px;
        }
        
        .password-strength {
            margin-top: 10px;
        }
        
        .strength-bar {
            height: 5px;
            border-radius: 3px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background: #dc3545; width: 25%; }
        .strength-medium { background: #ffc107; width: 50%; }
        .strength-good { background: #fd7e14; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }
    </style>
</head>
<body>
    <?php
    session_start();

    require_once __DIR__ . '/../../../config/Connection.php';
    require_once __DIR__ . '/../../Models/User.php';

    use MediBook\Models\User;

    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $message = '';
    $messageType = '';
    $validToken = false;
    $userModel = new User();

    // Verificar token contra el esquema real (password_reset_tokens.email,
    // no password_reset_tokens.user_id, que no existe en la migración).
    if ($token) {
        try {
            $validToken = $userModel->validatePasswordResetToken($token);

            if (!$validToken) {
                $message = 'El enlace de recuperación no es válido o ha expirado';
                $messageType = 'danger';
            }
        } catch (Exception $e) {
            error_log('Reset-password validation error: ' . $e->getMessage());
            $message = 'Error del sistema. Por favor intenta más tarde.';
            $messageType = 'danger';
        }
    } else {
        $message = 'Enlace de recuperación no válido';
        $messageType = 'danger';
    }

    // Procesar formulario de cambio de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirmPassword)) {
            $message = 'Por favor completa todos los campos';
            $messageType = 'danger';
        } elseif (strlen($password) < 8 ||
                  !preg_match('/[a-z]/', $password) ||
                  !preg_match('/[A-Z]/', $password) ||
                  !preg_match('/\d/', $password)) {
            $message = 'La contraseña debe tener al menos 8 caracteres, con mayúscula, minúscula y número';
            $messageType = 'danger';
        } elseif ($password !== $confirmPassword) {
            $message = 'Las contraseñas no coinciden';
            $messageType = 'danger';
        } else {
            try {
                if ($userModel->resetPasswordWithToken($token, $password)) {
                    $message = 'Tu contraseña ha sido actualizada exitosamente. Ahora puedes iniciar sesión con tu nueva contraseña.';
                    $messageType = 'success';
                    $validToken = false; // Para ocultar el formulario
                } else {
                    $message = 'El enlace de recuperación no es válido o ha expirado';
                    $messageType = 'danger';
                    $validToken = false;
                }
            } catch (Exception $e) {
                error_log('Reset-password update error: ' . $e->getMessage());
                $message = 'Error al actualizar la contraseña. Por favor intenta más tarde.';
                $messageType = 'danger';
            }
        }
    }
    ?>
    
    <div class="main-container">
        <div class="reset-card">
            <div class="card-header">
                <i class="fas fa-lock"></i>
                <h3 class="mb-0">Restablecer Contraseña</h3>
                <p class="mb-0 mt-2">Ingresa tu nueva contraseña</p>
            </div>
            
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($validToken): ?>
                    <div class="password-requirements">
                        <h6><i class="fas fa-shield-alt me-2"></i>Requisitos de Contraseña</h6>
                        <div class="requirement">
                            <i class="fas fa-check text-success"></i>
                            Mínimo 8 caracteres
                        </div>
                        <div class="requirement">
                            <i class="fas fa-info-circle text-info"></i>
                            Se recomienda incluir letras, números y símbolos
                        </div>
                    </div>
                    
                    <form method="POST" action="" id="resetForm">
                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Nueva Contraseña
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Ingresa tu nueva contraseña"
                                required
                                minlength="8"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Confirmar Contraseña
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="Confirma tu nueva contraseña"
                                required
                                minlength="8"
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-reset">
                            <i class="fas fa-save me-2"></i>
                            Guardar Nueva Contraseña
                        </button>
                    </form>
                <?php elseif ($messageType === 'success'): ?>
                    <div class="text-center">
                        <a href="/MediBook/src/Views/auth/login.php" class="btn btn-reset">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Ir a Iniciar Sesión
                        </a>
                    </div>
                <?php endif; ?>
                
                <a href="/MediBook/" class="btn-back">
                    <i class="fas fa-home me-2"></i>
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validar que las contraseñas coincidan en tiempo real
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Validación adicional en envío del formulario
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                document.getElementById('confirm_password').focus();
            }
        });
    </script>
</body>
</html>