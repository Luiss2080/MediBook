<?php
/**
 * Script de Migración y Seeding para MediBook
 * Ejecuta las migraciones y seeds en orden
 */

require_once __DIR__ . '/../config/Connection.php';

use MediBook\Database\Connection;

class DatabaseMigrator 
{
    private $db;
    private $migrationsPath;
    private $seedsPath;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->migrationsPath = __DIR__ . '/../src/Database/migrations/';
        $this->seedsPath = __DIR__ . '/../src/Database/Seeds/';
    }
    
    public function runMigrations()
    {
        echo "🚀 Ejecutando migraciones...\n\n";
        
        $migrationFiles = glob($this->migrationsPath . '*.sql');
        sort($migrationFiles);
        
        foreach ($migrationFiles as $file) {
            $fileName = basename($file);
            echo "📋 Ejecutando migración: {$fileName}\n";
            
            $sql = file_get_contents($file);
            
            try {
                $this->db->exec($sql);
                echo "✅ Migración {$fileName} ejecutada correctamente\n";
            } catch (PDOException $e) {
                echo "❌ Error en migración {$fileName}: " . $e->getMessage() . "\n";
            }
            
            echo "\n";
        }
    }
    
    public function runSeeds()
    {
        echo "🌱 Ejecutando seeds...\n\n";
        
        $seedFiles = glob($this->seedsPath . '*.sql');
        sort($seedFiles);
        
        foreach ($seedFiles as $file) {
            $fileName = basename($file);
            echo "🌱 Ejecutando seed: {$fileName}\n";
            
            $sql = file_get_contents($file);
            
            // Solo ejecutar si el archivo tiene contenido SQL real
            if (trim($sql) && !empty(preg_replace('/--.*/', '', $sql))) {
                try {
                    $this->db->exec($sql);
                    echo "✅ Seed {$fileName} ejecutado correctamente\n";
                } catch (PDOException $e) {
                    echo "❌ Error en seed {$fileName}: " . $e->getMessage() . "\n";
                }
            } else {
                echo "⏭️  Seed {$fileName} omitido (sin datos)\n";
            }
            
            echo "\n";
        }
    }
    
    public function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->db->exec($sql);
            echo "✅ Tabla de migraciones creada\n\n";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla de migraciones: " . $e->getMessage() . "\n\n";
        }
    }
    
    public function showTables()
    {
        echo "📊 Tablas en la base de datos:\n";
        
        $stmt = $this->db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            echo "   • {$table}\n";
        }
        
        echo "\n";
    }
    
    public function showUsers()
    {
        echo "👥 Usuarios en la base de datos:\n";
        
        try {
            $stmt = $this->db->query("SELECT id, username, email, role, status FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($users as $user) {
                echo "   • ID: {$user['id']}, Usuario: {$user['username']}, Email: {$user['email']}, Rol: {$user['role']}, Estado: {$user['status']}\n";
            }
        } catch (PDOException $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
}

// Ejecutar migraciones
try {
    echo "🏥 MediBook - Sistema de Migración de Base de Datos\n";
    echo "================================================\n\n";
    
    $migrator = new DatabaseMigrator();
    
    // Crear tabla de control de migraciones
    $migrator->createMigrationsTable();
    
    // Ejecutar migraciones
    $migrator->runMigrations();
    
    // Ejecutar seeds
    $migrator->runSeeds();
    
    // Mostrar resumen
    echo "📋 RESUMEN FINAL\n";
    echo "================\n";
    $migrator->showTables();
    $migrator->showUsers();
    
    echo "🎉 ¡Migración completada exitosamente!\n\n";
    echo "📝 Credenciales de prueba:\n";
    echo "   Admin: admin@medibook.com / password123\n";
    echo "   Doctor: doctor@medibook.com / password123\n";
    echo "   Paciente: patient@medibook.com / password123\n\n";
    
} catch (Exception $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
    exit(1);
}