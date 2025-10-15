<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MediBook</title>
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
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Registro</h1>
                <p>Crear cuenta en MediBook</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/MediBook/public/register">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">Nombre</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">Apellido</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" required>
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
                <p>¿Ya tienes cuenta? <a href="/MediBook/public/login">Iniciar Sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>