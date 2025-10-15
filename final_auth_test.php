<?php
/**
 * Prueba simplificada del sistema de autenticación
 */
session_start();

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'src/Models/User.php';

echo "🔄 Probando sistema de autenticación MediBook\n\n";

try {
    $userModel = new User();
    
    // Credenciales de prueba
    $testUsers = [
        ['admin@medibook.com', 'password123', 'admin'],
        ['doctor@medibook.com', 'password123', 'doctor'], 
        ['patient@medibook.com', 'password123', 'patient']
    ];
    
    foreach ($testUsers as [$email, $password, $expectedRole]) {
        echo "🧪 Probando: $email\n";
        
        // Autenticar usuario
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            echo "   ✅ Autenticación exitosa\n";
            echo "   👤 Usuario: {$user['first_name']} {$user['last_name']}\n";
            echo "   🏷️  Rol: {$user['role']}\n";
            echo "   📧 Email: {$user['email']}\n";
            
            // Determinar dashboard correcto
            $dashboard = match($user['role']) {
                'admin' => 'src/Views/admin/dashboard.php',
                'doctor' => 'src/Views/doctor/dashboard.php', 
                'patient' => 'src/Views/patient/dashboard.php',
                default => 'ERROR: Rol desconocido'
            };
            echo "   🎯 Dashboard: $dashboard\n";
            
            // Crear sesión de prueba
            $sessionId = $userModel->createUserSession($user['id']);
            if ($sessionId) {
                echo "   🔐 Sesión creada: $sessionId\n";
            }
            
        } else {
            echo "   ❌ Autenticación falló\n";
        }
        echo "   " . str_repeat("-", 40) . "\n\n";
    }
    
    echo "✅ PRUEBA COMPLETADA\n\n";
    echo "🌐 PARA PROBAR EN EL NAVEGADOR:\n";
    echo "   URL: http://localhost/MediBook/\n";
    echo "   \n";
    echo "   👑 ADMIN:\n";
    echo "      Email: admin@medibook.com\n";
    echo "      Password: password123\n";
    echo "      Dashboard: /src/Views/admin/dashboard.php\n";
    echo "   \n";
    echo "   👨‍⚕️ DOCTOR:\n";
    echo "      Email: doctor@medibook.com\n";
    echo "      Password: password123\n";
    echo "      Dashboard: /src/Views/doctor/dashboard.php\n";
    echo "   \n";
    echo "   🤒 PACIENTE:\n";
    echo "      Email: patient@medibook.com\n";
    echo "      Password: password123\n";
    echo "      Dashboard: /src/Views/patient/dashboard.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}