<?php
session_start();

// Verificar que el usuario esté autenticado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
    <title>Dashboard Administrador - MediBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
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
                <i class="fas fa-hospital-alt me-2"></i>
                <strong>MediBook</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-shield me-2"></i>
                        <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs me-2"></i>Configuración</a></li>
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
                        <h2 class="text-danger mb-2">
                            <i class="fas fa-user-shield stat-icon me-2"></i>
                            Panel de Administrador
                        </h2>
                        <p class="lead mb-0">Bienvenido, <?= htmlspecialchars($user['name']) ?></p>
                        <small class="text-muted">Última conexión: <?= date('d/m/Y H:i') ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">5</h4>
                                <p class="mb-0">Total Usuarios</p>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">2</h4>
                                <p class="mb-0">Doctores</p>
                            </div>
                            <i class="fas fa-user-md stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">2</h4>
                                <p class="mb-0">Pacientes</p>
                            </div>
                            <i class="fas fa-procedures stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card card-custom bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">0</h4>
                                <p class="mb-0">Citas Hoy</p>
                            </div>
                            <i class="fas fa-calendar-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Módulos -->
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5>Gestión de Usuarios</h5>
                        <p class="text-muted">Administrar doctores, pacientes y personal</p>
                        <button class="btn btn-primary" onclick="alert('Próximamente')">
                            <i class="fas fa-cog me-2"></i>Acceder
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt text-success mb-3" style="font-size: 3rem;"></i>
                        <h5>Sistema de Citas</h5>
                        <p class="text-muted">Supervisar y gestionar todas las citas</p>
                        <button class="btn btn-success" onclick="alert('Próximamente')">
                            <i class="fas fa-calendar me-2"></i>Ver Citas
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5>Reportes</h5>
                        <p class="text-muted">Análisis y estadísticas del sistema</p>
                        <button class="btn btn-warning" onclick="alert('Próximamente')">
                            <i class="fas fa-chart-line me-2"></i>Ver Reportes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Acceso rápido -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Acceso Rápido</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="/MediBook/" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-home me-2"></i>Inicio
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-success w-100" onclick="alert('Próximamente')">
                                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-warning w-100" onclick="alert('Próximamente')">
                                    <i class="fas fa-calendar-plus me-2"></i>Nueva Cita
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-info w-100" onclick="alert('Próximamente')">
                                    <i class="fas fa-cogs me-2"></i>Configuración
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <style>
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-success {
            background-color: var(--success-color);
            color: white;
        }
    </style>
</body>
</html>