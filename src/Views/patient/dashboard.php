<?php
session_start();

// Verificar que el usuario esté autenticado y sea paciente
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: ../../../src/Views/auth/login.php');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'],
    'email' => $_SESSION['email'],
    'role' => $_SESSION['role'],
    'name' => $_SESSION['name'] ?? $_SESSION['email']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Paciente - MediBook</title>
    <link rel="icon" type="image/png" href="../../../public/assets/img/LogoMediBook.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(45deg, #007bff, #6610f2);
        }
        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-heartbeat me-2"></i>
                <strong>MediBook</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-medical me-2"></i>Mi Historial</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="/MediBook/src/auth_handler.php" style="display: inline;">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Bienvenida -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-body text-center p-4">
                        <h2 class="text-primary mb-2">
                            <i class="fas fa-user stat-icon me-2"></i>
                            Portal del Paciente
                        </h2>
                        <p class="lead mb-0">Bienvenido, <?= htmlspecialchars($user['name']) ?></p>
                        <small class="text-muted">Tu salud es nuestra prioridad</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información del Paciente -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Citas Programadas</p>
                            </div>
                            <i class="fas fa-calendar-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Consultas Completadas</p>
                            </div>
                            <i class="fas fa-check-circle stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">O+</h4>
                                <p class="mb-0">Tipo de Sangre</p>
                            </div>
                            <i class="fas fa-tint stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">Activo</h4>
                                <p class="mb-0">Estado</p>
                            </div>
                            <i class="fas fa-user-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Servicios para el Paciente -->
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-plus text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5>Agendar Cita</h5>
                        <p class="text-muted">Programa una nueva consulta médica</p>
                        <button class="btn btn-primary" onclick="alert('Próximamente')">
                            <i class="fas fa-plus me-2"></i>Nueva Cita
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-day text-success mb-3" style="font-size: 3rem;"></i>
                        <h5>Mis Citas</h5>
                        <p class="text-muted">Ver y gestionar citas programadas</p>
                        <button class="btn btn-success" onclick="alert('Próximamente')">
                            <i class="fas fa-calendar me-2"></i>Ver Citas
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-medical-alt text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5>Historial Médico</h5>
                        <p class="text-muted">Consulta tu historial y resultados</p>
                        <button class="btn btn-warning" onclick="alert('Próximamente')">
                            <i class="fas fa-history me-2"></i>Ver Historial
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información Adicional -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-envelope me-2"></i>Email</span>
                                <span class="text-muted"><?= htmlspecialchars($user['email']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-phone me-2"></i>Teléfono</span>
                                <span class="text-muted">No especificado</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-birthday-cake me-2"></i>Edad</span>
                                <span class="text-muted">No especificada</span>
                            </li>
                        </ul>
                        <button class="btn btn-outline-primary w-100 mt-3" onclick="alert('Próximamente')">
                            <i class="fas fa-edit me-2"></i>Actualizar Información
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Acceso Rápido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/MediBook/" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i>Inicio
                            </a>
                            <button class="btn btn-outline-success" onclick="alert('Próximamente')">
                                <i class="fas fa-calendar-plus me-2"></i>Agendar Cita Urgente
                            </button>
                            <button class="btn btn-outline-warning" onclick="alert('Próximamente')">
                                <i class="fas fa-download me-2"></i>Descargar Resultados
                            </button>
                            <button class="btn btn-outline-info" onclick="alert('Próximamente')">
                                <i class="fas fa-question-circle me-2"></i>Centro de Ayuda
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Próximas Citas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Próximas Citas</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted">
                            <i class="fas fa-calendar-times mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p>No tienes citas programadas</p>
                            <button class="btn btn-primary" onclick="alert('Próximamente')">
                                <i class="fas fa-plus me-2"></i>Agendar Primera Cita
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>