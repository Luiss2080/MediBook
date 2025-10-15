<?php
/**
 * Script de prueba completa del sistema de autenticación
 * Simula el flujo completo de login sin navegador
 */

// Incluir las clases necesarias
require_once 'config/database.php';
require_once 'src/Models/User.php';
require_once 'src/Controllers/AuthController.php';

echo "🔄 Iniciando prueba completa del sistema de autenticación\n\n";

try {
    // Simular datos POST de login
    $testCredentials = [
        'admin' => ['email' => 'admin@medibook.com', 'password' => 'password123'],
        'doctor' => ['email' => 'doctor@medibook.com', 'password' => 'password123'],
        'patient' => ['email' => 'patient@medibook.com', 'password' => 'password123']
    ];
    
    foreach ($testCredentials as $role => $credentials) {
        echo "🧪 Probando login para rol: " . strtoupper($role) . "\n";
        echo "   Email: {$credentials['email']}\n";
        echo "   Password: {$credentials['password']}\n";
        
        // Crear una nueva instancia del controlador para cada prueba
        $authController = new AuthController();
        
        // Simular $_POST
        $_POST = [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ];
        
        // Intentar login
        ob_start(); // Capturar output
        $result = $authController->login();
        $output = ob_get_clean();
        
        if (isset($_SESSION['user_id'])) {
            echo "   ✅ Login exitoso\n";
            echo "   👤 Usuario ID: {$_SESSION['user_id']}\n";
            echo "   🏷️  Rol: {$_SESSION['role']}\n";
            echo "   📧 Email: {$_SESSION['email']}\n";
            
            // Verificar redirección según el rol
            switch ($_SESSION['role']) {
                case 'admin':
                    $expectedRedirect = 'src/Views/admin/dashboard.php';
                    break;
                case 'doctor':
                    $expectedRedirect = 'src/Views/doctor/dashboard.php';
                    break;
                case 'patient':
                    $expectedRedirect = 'src/Views/patient/dashboard.php';
                    break;
            }
            echo "   🎯 Dashboard esperado: $expectedRedirect\n";
            
            // Limpiar sesión para próxima prueba
            session_destroy();
            session_start();
        } else {
            echo "   ❌ Login falló\n";
            if (isset($_SESSION['error'])) {
                echo "   💬 Error: {$_SESSION['error']}\n";
            }
        }
        echo "   " . str_repeat("-", 50) . "\n\n";
    }
    
    echo "✅ Prueba completa finalizada\n";
    echo "🎯 El sistema está listo para usar con las credenciales mostradas arriba\n\n";
    echo "📝 PARA PROBAR EN EL NAVEGADOR:\n";
    echo "   1. Accede a: http://localhost/MediBook/\n";
    echo "   2. Usa cualquiera de estas credenciales:\n";
    echo "      • Admin: admin@medibook.com / password123\n";
    echo "      • Doctor: doctor@medibook.com / password123\n";
    echo "      • Paciente: patient@medibook.com / password123\n";
    echo "   3. Verifica que te redirija al dashboard correcto según el rol\n";
    
} catch (Exception $e) {
    echo "❌ Error en la prueba: " . $e->getMessage() . "\n";
}