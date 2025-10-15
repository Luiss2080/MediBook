<?php
/**
 * Script para actualizar las contraseñas de los usuarios de prueba
 */

// Configuración de base de datos directa para pruebas
$host = 'localhost';
$dbname = 'medibook';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "🟢 Conexión a la base de datos: EXITOSA\n\n";
    
    // Generar hash correcto para password123
    $correctPassword = 'password123';
    $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT);
    
    echo "🔐 Actualizando contraseñas para usuarios de prueba...\n";
    
    // Actualizar contraseñas
    $users = [
        ['admin@medibook.com', 'admin'],
        ['doctor@medibook.com', 'doctor1'],
        ['patient@medibook.com', 'patient1']
    ];
    
    foreach ($users as [$email, $username]) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt->execute([$hashedPassword, $email])) {
            echo "   ✅ Contraseña actualizada para: $email\n";
        } else {
            echo "   ❌ Error actualizando contraseña para: $email\n";
        }
    }
    
    echo "\n🧪 Probando autenticación después de la actualización:\n";
    
    // Probar autenticación nuevamente
    foreach ($users as [$email, $username]) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($correctPassword, $user['password'])) {
            echo "   ✅ Login exitoso para: $email (Rol: {$user['role']})\n";
        } else {
            echo "   ❌ Login fallido para: $email\n";
        }
    }
    
    echo "\n🎯 Credenciales finales para pruebas:\n";
    echo "   Admin:    admin@medibook.com / password123\n";
    echo "   Doctor:   doctor@medibook.com / password123\n";
    echo "   Paciente: patient@medibook.com / password123\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}