<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MediBook</title>
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
        
        .login-card {
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
        
        .btn-login {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: linear-gradient(45deg, #5a6fd8, #6c4298);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .links-section {
            text-align: center;
            margin-top: 20px;
        }
        
        .links-section a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .links-section a:hover {
            color: #5a6fd8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    
    // Procesar login si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Intentar conectar a la base de datos
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=medibook;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            // Buscar usuario
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                // Redirigir según el rol
                switch ($user['role']) {
                    case 'admin':
                        header('Location: ../admin/dashboard.php');
                        exit;
                    case 'doctor':
                        header('Location: ../doctor/dashboard.php');
                        exit;
                    case 'patient':
                        header('Location: ../patient/dashboard.php');
                        exit;
                    default:
                        $error = "Rol de usuario no válido";
                }
            } else {
                $error = "Credenciales incorrectas";
            }
        } catch (Exception $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    }
    
    // Si ya está autenticado, redirigir
    if (isset($_SESSION['user_id'])) {
        switch ($_SESSION['role']) {
            case 'admin':
                header('Location: ../admin/dashboard.php');
                exit;
            case 'doctor':
                header('Location: ../doctor/dashboard.php');
                exit;
            case 'patient':
                header('Location: ../patient/dashboard.php');
                exit;
        }
    }
    ?>
    
    <div class="main-container">
        <div class="login-card">
            <div class="card-header">
                <i class="fas fa-sign-in-alt"></i>
                <h3 class="mb-0">Iniciar Sesión</h3>
                <p class="mb-0 mt-2">Accede a tu cuenta MediBook</p>
            </div>
            
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>
                            Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="tu@email.com"
                            required
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>
                            Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Tu contraseña"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </button>
                </form>
                
                <div class="links-section">
                    <div class="row">
                        <div class="col-6">
                            <a href="/MediBook/src/Views/auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>
                                Registrarse
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/MediBook/src/Views/auth/forgot-password.php">
                                <i class="fas fa-key me-1"></i>
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>
                    <hr class="my-3">
                    <a href="/MediBook/">
                        <i class="fas fa-home me-1"></i>
                        Volver al Inicio
                    </a>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-info-circle me-1"></i>Credenciales de Prueba:</strong><br>
                    <small>
                        <strong>Admin:</strong> admin@medibook.com / password123<br>
                        <strong>Doctor:</strong> doctor@medibook.com / password123<br>
                        <strong>Paciente:</strong> patient@medibook.com / password123
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>