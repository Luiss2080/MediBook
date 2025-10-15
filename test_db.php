<?php
/**
 * Script de prueba para el sistema de autenticación
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
    
    // Verificar tablas
    $tables = ['users', 'user_sessions', 'user_profiles', 'login_attempts', 'password_reset_tokens'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla '$table' existe\n";
        } else {
            echo "❌ Tabla '$table' NO existe\n";
        }
    }
    
    echo "\n";
    
    // Verificar usuarios
    $stmt = $pdo->query("SELECT id, username, email, role, status FROM users");
    $users = $stmt->fetchAll();
    
    echo "👥 Usuarios en la base de datos:\n";
    foreach ($users as $user) {
        echo "   • ID: {$user['id']}, Usuario: {$user['username']}, Email: {$user['email']}, Rol: {$user['role']}, Estado: {$user['status']}\n";
    }
    
    echo "\n";
    
    // Probar autenticación
    echo "🔐 Probando autenticación:\n";
    
    $testCredentials = [
        ['admin@medibook.com', 'password123'],
        ['doctor@medibook.com', 'password123'],
        ['patient@medibook.com', 'password123']
    ];
    
    foreach ($testCredentials as [$email, $pass]) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($pass, $user['password'])) {
            echo "   ✅ Login exitoso para: $email (Rol: {$user['role']})\n";
        } else {
            echo "   ❌ Login fallido para: $email\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}