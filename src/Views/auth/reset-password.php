<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - MediBook</title>
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
    require_once __DIR__ . '/../../../config/constants.php';
    require_once __DIR__ . '/../../Controllers/AuthController.php';
    
    use MediBook\Controllers\AuthController;
    
    $authController = new AuthController();
    $message = '';
    $messageType = '';
    $token = $_GET['token'] ?? '';
    
    // Verificar token
    if (empty($token)) {
        $message = 'Token de recuperación no válido o faltante.';
        $messageType = 'danger';
    } else {
        $isValidToken = $authController->validateResetToken($token);
        if (!$isValidToken) {
            $message = 'Token de recuperación expirado o no válido.';
            $messageType = 'danger';
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($message)) {
        $result = $authController->resetPassword();
        $message = $result['message'];
        $messageType = $result['type'];
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
                
                <?php if (empty($message) || $messageType !== 'danger'): ?>
                    <div class="password-requirements">
                        <h6><i class="fas fa-shield-alt me-2"></i>Requisitos de Contraseña</h6>
                        <div class="requirement">
                            <i class="fas fa-check text-muted"></i>
                            Al menos 8 caracteres
                        </div>
                        <div class="requirement">
                            <i class="fas fa-check text-muted"></i>
                            Una letra mayúscula
                        </div>
                        <div class="requirement">
                            <i class="fas fa-check text-muted"></i>
                            Una letra minúscula
                        </div>
                        <div class="requirement">
                            <i class="fas fa-check text-muted"></i>
                            Un número
                        </div>
                    </div>
                    
                    <form method="POST" action="" id="resetForm">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
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
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <small class="text-muted mt-1" id="strengthText">Ingresa una contraseña</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm" class="form-label">
                                <i class="fas fa-lock me-2"></i>
                                Confirmar Contraseña
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirm" 
                                name="password_confirm" 
                                placeholder="Confirma tu nueva contraseña"
                                required
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-reset">
                            <i class="fas fa-save me-2"></i>
                            Guardar Nueva Contraseña
                        </button>
                        
                        <a href="/MediBook/" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver al Inicio
                        </a>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <a href="/MediBook/src/Views/auth/forgot-password.php" class="btn btn-reset">
                            <i class="fas fa-redo me-2"></i>
                            Solicitar Nuevo Token
                        </a>
                        
                        <a href="/MediBook/" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver al Inicio
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de fortaleza de contraseña
        document.getElementById('password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let text = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            
            strengthFill.className = 'strength-fill';
            
            switch (strength) {
                case 1:
                    strengthFill.classList.add('strength-weak');
                    text = 'Muy débil';
                    break;
                case 2:
                    strengthFill.classList.add('strength-medium');
                    text = 'Débil';
                    break;
                case 3:
                    strengthFill.classList.add('strength-good');
                    text = 'Buena';
                    break;
                case 4:
                    strengthFill.classList.add('strength-strong');
                    text = 'Fuerte';
                    break;
                default:
                    text = 'Ingresa una contraseña';
            }
            
            strengthText.textContent = text;
        });
        
        // Validación de confirmación de contraseña
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                document.getElementById('password_confirm').focus();
            }
        });
    </script>
</body>
</html>