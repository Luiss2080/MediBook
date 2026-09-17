<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MediBook</title>
    <link rel="icon" type="image/png" href="../../../public/assets/img/LogoMediBook.png">
    <link rel="stylesheet" href="/MediBook/public/assets/css/app.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }
        
        .register-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .register-header p {
            color: var(--secondary-color);
            margin: 0;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <?php
    require_once __DIR__ . '/../../../config/Connection.php';
    require_once __DIR__ . '/../../Models/User.php';
    require_once __DIR__ . '/../../Helpers/SecurityHelper.php';
    require_once __DIR__ . '/../../Helpers/SessionHelper.php';

    use MediBook\Models\User;
    use MediBook\Helpers\SecurityHelper;
    use MediBook\Helpers\SessionHelper;

    SessionHelper::start();

    $error = null;
    $success = null;
    $old = ['username' => '', 'email' => '', 'first_name' => '', 'last_name' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        SecurityHelper::requireValidCsrfToken('register');

        $old['username'] = trim($_POST['username'] ?? '');
        $old['first_name'] = trim($_POST['first_name'] ?? '');
        $old['last_name'] = trim($_POST['last_name'] ?? '');
        $old['email'] = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($old['username']) || empty($old['email']) || empty($old['first_name'])
            || empty($old['last_name']) || empty($password)) {
            $error = 'Por favor, completa todos los campos.';
        } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Ingresa una dirección de correo electrónico válida.';
        } elseif (!SecurityHelper::isStrongPassword($password)) {
            $error = 'La contraseña debe tener al menos 8 caracteres, con mayúscula, minúscula y número.';
        } else {
            try {
                $userModel = new User();

                if ($userModel->findByEmail($old['email'])) {
                    $error = 'El email ya está registrado.';
                } else {
                    // El rol SIEMPRE se fija en el servidor: nunca se acepta
                    // un campo "role" del formulario, para que nadie pueda
                    // auto-registrarse como admin/doctor manipulando el POST.
                    $created = $userModel->create([
                        'username' => $old['username'],
                        'email' => $old['email'],
                        'password' => $password,
                        'first_name' => $old['first_name'],
                        'last_name' => $old['last_name'],
                        'role' => 'patient',
                    ]);

                    if ($created) {
                        $success = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.';
                        $old = ['username' => '', 'email' => '', 'first_name' => '', 'last_name' => ''];
                    } else {
                        $error = 'No se pudo crear la cuenta. Intenta nuevamente.';
                    }
                }
            } catch (\PDOException $e) {
                // Violación de UNIQUE(username) u otra restricción de datos:
                // nunca se muestra el mensaje crudo de PDO al usuario.
                error_log('Register error: ' . $e->getMessage());
                $error = (str_contains($e->getMessage(), 'username'))
                    ? 'Ese nombre de usuario ya está en uso.'
                    : 'No se pudo crear la cuenta. Intenta nuevamente.';
            }
        }
    }
    ?>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Registro</h1>
                <p>Crear cuenta en MediBook</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <a href="/MediBook/src/Views/auth/login.php">Iniciar sesión</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= SecurityHelper::csrfField('register') ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">Nombre</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required
                               value="<?= htmlspecialchars($old['first_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Apellido</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required
                               value="<?= htmlspecialchars($old['last_name']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" required
                           value="<?= htmlspecialchars($old['username']) ?>">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($old['email']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Registrarse
                </button>
            </form>
            
            <div class="text-center mt-3">
                <p>¿Ya tienes cuenta? <a href="/MediBook/src/Views/auth/login.php">Iniciar Sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>