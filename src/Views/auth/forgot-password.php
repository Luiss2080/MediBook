<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - MediBook</title>
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
        
        .recovery-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
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
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-recovery {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-recovery:hover {
            background: linear-gradient(45deg, #5a6fd8, #6c4298);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
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
        
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-box i {
            color: #1976d2;
            margin-right: 8px;
        }
        
        .info-text {
            color: #1565c0;
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    require_once __DIR__ . '/../../../config/constants.php';
    require_once __DIR__ . '/../../../config/Connection.php';
    require_once __DIR__ . '/../../Models/User.php';
    require_once __DIR__ . '/../../Services/EmailService.php';
    require_once __DIR__ . '/../../Helpers/SecurityHelper.php';

    use MediBook\Models\User;
    use MediBook\Services\EmailService;
    use MediBook\Helpers\SecurityHelper;

    $message = '';
    $messageType = '';

    // Procesar formulario de recuperación de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        SecurityHelper::requireValidCsrfToken('forgot-password');

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Por favor ingresa una dirección de correo electrónico válida';
            $messageType = 'error';
        } else {
            try {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user) {
                    $result = $userModel->createPasswordResetToken($email);

                    if ($result) {
                        $emailService = new EmailService();
                        $emailService->sendPasswordResetEmail($email, $result['token']);
                    }
                }

                // Respuesta idéntica exista o no la cuenta: evita que un atacante
                // pueda usar este formulario para enumerar correos registrados.
                // El enlace de recuperación NUNCA se muestra en la respuesta HTTP;
                // solo se envía por correo al titular de la cuenta.
                $message = 'Si el correo está registrado, recibirás un enlace de recuperación en unos minutos. El enlace expirará en 1 hora.';
                $messageType = 'success';
            } catch (Exception $e) {
                error_log('Forgot-password error: ' . $e->getMessage());
                $message = 'Error del sistema. Por favor intenta más tarde.';
                $messageType = 'error';
            }
        }
    }
    ?>
    
    <div class="main-container">
        <div class="recovery-card">
            <div class="card-header">
                <i class="fas fa-key"></i>
                <h3 class="mb-0">Recuperar Contraseña</h3>
                <p class="mb-0 mt-2">Te ayudaremos a recuperar tu cuenta</p>
            </div>
            
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p class="info-text">
                        Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>
                
                <form method="POST" action="">
                    <?= SecurityHelper::csrfField('forgot-password') ?>
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>
                            Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="tu@email.com"
                            required
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-recovery">
                        <i class="fas fa-paper-plane me-2"></i>
                        Enviar Enlace de Recuperación
                    </button>
                    
                    <a href="/MediBook/" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Inicio
                    </a>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        El enlace expirará en 1 hora
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
