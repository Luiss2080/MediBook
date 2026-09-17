<?php
namespace Tests\Support;

use MediBook\Database\Connection;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Base para tests de integración que necesitan una base de datos real.
 *
 * Aplica las migraciones reales del proyecto (src/Database/migrations) una
 * vez por proceso y limpia las tablas relevantes antes de cada test, para
 * que los tests sean independientes entre sí sin necesitar un mock de PDO.
 */
abstract class DatabaseTestCase extends TestCase
{
    private static bool $migrated = false;

    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = Connection::getInstance()->getConnection();

        if (!self::$migrated) {
            $this->runMigrations();
            self::$migrated = true;
        }

        $this->cleanTables();
    }

    private function runMigrations(): void
    {
        $migrationsPath = dirname(__DIR__, 2) . '/src/Database/migrations/';
        $files = glob($migrationsPath . '*.sql');
        sort($files);

        foreach ($files as $file) {
            $this->pdo->exec((string) file_get_contents($file));
        }
    }

    private function cleanTables(): void
    {
        // Orden pensado para respetar dependencias lógicas entre tablas
        // (aunque el esquema actual no declara FKs explícitas).
        foreach (['user_sessions', 'login_attempts', 'password_reset_tokens', 'user_profiles', 'users'] as $table) {
            $this->pdo->exec("DELETE FROM {$table}");
        }
    }

    protected function insertUser(array $overrides = []): int
    {
        $data = array_merge([
            'username' => 'user_' . bin2hex(random_bytes(4)),
            'email' => bin2hex(random_bytes(4)) . '@example.test',
            'password' => password_hash('Password123', PASSWORD_DEFAULT),
            'first_name' => 'Test',
            'last_name' => 'User',
            'role' => 'patient',
        ], $overrides);

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password, first_name, last_name, role)
             VALUES (:username, :email, :password, :first_name, :last_name, :role)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }
}
