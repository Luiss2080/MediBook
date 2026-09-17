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

    require_once __DIR__ . '/../../../config/Connection.php';

    $message = '';
    $messageType = '';

    // Procesar formulario de recuperación de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $message = 'Por favor ingresa tu correo electrónico';
            $messageType = 'error';
        } else {
            try {
                $pdo = \MediBook\Database\Connection::getInstance()->getConnection();
                
                // Verificar si el usuario existe
                $stmt = $pdo->prepare("SELECT id, email, first_name FROM users WHERE email = ? AND status = 'active'");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Generar token de recuperación
                    $token = bin2hex(random_bytes(32));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Guardar token en la base de datos
                    $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
                    $stmt->execute([$user['id'], $token, $expires_at]);
                    
                    // Simular envío de email (en producción aquí iría el envío real)
                    $resetLink = "http://localhost/MediBook/src/Views/auth/reset-password.php?token=" . $token;
                    
                    $message = 'Se ha enviado un enlace de recuperación a tu correo electrónico. El enlace expirará en 1 hora.';
                    $messageType = 'success';
                    
                    // En desarrollo, mostrar el enlace directamente
                    $message .= '<br><br><strong>Enlace de recuperación (solo para desarrollo):</strong><br><a href="' . $resetLink . '" target="_blank">' . $resetLink . '</a>';
                } else {
                    $message = 'No se encontró una cuenta asociada a ese correo electrónico';
                    $messageType = 'error';
                }
            } catch (Exception $e) {
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
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p class="info-text">
                        Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>
                
                <form method="POST" action="">
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
