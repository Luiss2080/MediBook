<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediBook - Sistema Integral de Gestión Médica</title>
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
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 500px;
            width: 100%;
        }
        
        .header-section {
            text-align: center;
            padding: 40px 30px 30px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .title {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        .access-section {
            padding: 30px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .btn-custom {
            width: 100%;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 15px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-login {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            color: white;
        }
        
        .btn-login:hover {
            background: linear-gradient(45deg, #ff5252, #ff7979);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
            color: white;
        }
        
        .btn-register {
            background: linear-gradient(45deg, #51cf66, #69db7c);
            color: white;
        }
        
        .btn-register:hover {
            background: linear-gradient(45deg, #40c057, #51cf66);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(81, 207, 102, 0.3);
            color: white;
        }
        
        .btn-recover {
            background: linear-gradient(45deg, #868e96, #adb5bd);
            color: white;
        }
        
        .btn-recover:hover {
            background: linear-gradient(45deg, #6c757d, #868e96);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(134, 142, 150, 0.3);
            color: white;
        }
        
        .btn-demo {
            background: linear-gradient(45deg, #845ec2, #9775fa);
            color: white;
        }
        
        .btn-demo:hover {
            background: linear-gradient(45deg, #7048e8, #845ec2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(132, 94, 194, 0.3);
            color: white;
        }
        
        .credentials-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .credentials-title {
            color: #495057;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .credential-item {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .credential-email {
            color: #0d6efd;
            font-weight: 500;
        }
        
        .modules-section {
            padding: 30px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 0 0 20px 20px;
        }
        
        .modules-title {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .module-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .module-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            color: white;
        }
        
        .module-content h6 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .module-content p {
            margin: 0;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="welcome-card">
            <!-- Header Section -->
            <div class="header-section">
                <div class="logo">🏥</div>
                <h1 class="title">MediBook</h1>
                <p class="subtitle">Sistema Integral de Gestión Médica</p>
            </div>
            
            <!-- Access Section -->
            <div class="access-section">
                <h4 class="section-title">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Acceso al Sistema
                </h4>
                
                <a href="/MediBook/src/Views/auth/login.php" class="btn btn-custom btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
                
                <a href="/MediBook/src/Views/auth/register.php" class="btn btn-custom btn-register">
                    <i class="fas fa-user-plus"></i>
                    Registrarse
                </a>
                
                <a href="/MediBook/src/Views/auth/forgot-password.php" class="btn btn-custom btn-recover">
                    <i class="fas fa-key"></i>
                    Recuperar Contraseña
                </a>
                
                <a href="/MediBook/demo.php" class="btn btn-custom btn-demo">
                    <i class="fas fa-play-circle"></i>
                    Probar Sistema
                </a>
                
                <!-- Credentials Section -->
                <div class="credentials-section">
                    <div class="credentials-title">
                        <i class="fas fa-info-circle me-1"></i>
                        Credenciales de Prueba
                    </div>
                    <div class="credential-item">
                        <strong>Administrador:</strong> 
                        <span class="credential-email">admin@medibook.com</span> / password123
                    </div>
                    <div class="credential-item">
                        <strong>Doctor:</strong> 
                        <span class="credential-email">doctor@medibook.com</span> / password123
                    </div>
                    <div class="credential-item">
                        <strong>Paciente:</strong> 
                        <span class="credential-email">patient@medibook.com</span> / password123
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            Todos los usuarios demo tienen el email verificado
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Modules Section -->
            <div class="modules-section">
                <h4 class="modules-title">
                    <i class="fas fa-th-large me-2"></i>
                    Módulos MediBook
                </h4>
                
                <div class="module-item">
                    <div class="module-icon" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="module-content">
                        <h6>Gestión de Pacientes</h6>
                        <p>Registro, renovaciones, check-in y control de historiales médicos</p>
                    </div>
                </div>
                
                <div class="module-item">
                    <div class="module-icon" style="background: linear-gradient(45deg, #51cf66, #69db7c);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="module-content">
                        <h6>Sistema de Citas</h6>
                        <p>Programación de consultas médicas y gestión de agenda</p>
                    </div>
                </div>
                
                <div class="module-item">
                    <div class="module-icon" style="background: linear-gradient(45deg, #ff6b6b, #ff8e8e);">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="module-content">
                        <h6>Portal Médico</h6>
                        <p>Gestión de doctores, especialidades y horarios de atención</p>
                    </div>
                </div>
                
                <div class="module-item">
                    <div class="module-icon" style="background: linear-gradient(45deg, #845ec2, #9775fa);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="module-content">
                        <h6>Reportes y Dashboard</h6>
                        <p>Métricas de ingresos, asistencia e inventario en tiempo real</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        © 2025 MediBook - Sistema de Gestión Médica
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
