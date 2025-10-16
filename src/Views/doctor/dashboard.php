<?php
session_start();

// Verificar que el usuario esté autenticado y sea doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
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
    <title>Dashboard Doctor - MediBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(45deg, #28a745, #20c997);
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
                <i class="fas fa-stethoscope me-2"></i>
                <strong>MediBook</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-md me-2"></i>
                        Dr. <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt me-2"></i>Mi Horario</a></li>
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
                        <h2 class="text-success mb-2">
                            <i class="fas fa-user-md stat-icon me-2"></i>
                            Portal Médico
                        </h2>
                        <p class="lead mb-0">Bienvenido, Dr. <?= htmlspecialchars($user['name']) ?></p>
                        <small class="text-muted">Consultas del día: 0 • Próxima cita: No programada</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas del Doctor -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Citas Hoy</p>
                            </div>
                            <i class="fas fa-calendar-day stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Mis Pacientes</p>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Pendientes</p>
                            </div>
                            <i class="fas fa-clock stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">Cardiología</h4>
                                <p class="mb-0">Especialidad</p>
                            </div>
                            <i class="fas fa-heartbeat stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Módulos del Doctor -->
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check text-success mb-3" style="font-size: 3rem;"></i>
                        <h5>Agenda de Citas</h5>
                        <p class="text-muted">Ver y gestionar tus citas programadas</p>
                        <button class="btn btn-success" onclick="alert('Próximamente')">
                            <i class="fas fa-calendar me-2"></i>Ver Agenda
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-injured text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5>Mis Pacientes</h5>
                        <p class="text-muted">Historiales e información de pacientes</p>
                        <button class="btn btn-primary" onclick="alert('Próximamente')">
                            <i class="fas fa-users me-2"></i>Ver Pacientes
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-medical text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5>Expedientes</h5>
                        <p class="text-muted">Crear y actualizar expedientes médicos</p>
                        <button class="btn btn-warning" onclick="alert('Próximamente')">
                            <i class="fas fa-folder-medical me-2"></i>Gestionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Horario de Atención -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Horario de Atención</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Lunes - Viernes</span>
                                <span class="text-muted">8:00 AM - 5:00 PM</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Sábados</span>
                                <span class="text-muted">9:00 AM - 1:00 PM</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Domingos</span>
                                <span class="text-muted">Cerrado</span>
                            </li>
                        </ul>
                        <button class="btn btn-outline-success w-100 mt-3" onclick="alert('Próximamente')">
                            <i class="fas fa-edit me-2"></i>Modificar Horario
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
                                <i class="fas fa-calendar-plus me-2"></i>Nueva Cita
                            </button>
                            <button class="btn btn-outline-warning" onclick="alert('Próximamente')">
                                <i class="fas fa-user-plus me-2"></i>Registrar Paciente
                            </button>
                            <button class="btn btn-outline-info" onclick="alert('Próximamente')">
                                <i class="fas fa-chart-line me-2"></i>Mis Estadísticas
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