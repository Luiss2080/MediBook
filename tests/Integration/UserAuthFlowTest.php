<?php
namespace Tests\Integration;

use MediBook\Models\User;
use Tests\Support\DatabaseTestCase;

/**
 * Cubre la lógica de MediBook\Models\User contra una base de datos MySQL
 * real y desechable: autenticación, bloqueo por intentos fallidos, y el
 * ciclo de vida completo de un token de recuperación de contraseña.
 *
 * Esta es exactamente la lógica que security/fix-password-reset-account-takeover
 * y security/harden-session-handling conectaron a las vistas reales; antes
 * de esas ramas nada la ejercitaba nunca, ni siquiera manualmente (por eso
 * bindParam() sobre expresiones no-variable en User::logLoginAttempt() /
 * User::createUserSession() llevaba ahí sin detectarse: fatal en cuanto se
 * invocan).
 */
class UserAuthFlowTest extends DatabaseTestCase
{
    private User $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new User();
    }

    public function testAuthenticateSucceedsWithCorrectPassword(): void
    {
        $this->insertUser(['email' => 'patient@example.test', 'password' => password_hash('Password123', PASSWORD_DEFAULT)]);

        $user = $this->userModel->authenticate('patient@example.test', 'Password123');

        $this->assertIsArray($user);
        $this->assertSame('patient@example.test', $user['email']);
    }

    public function testAuthenticateFailsWithWrongPassword(): void
    {
        $this->insertUser(['email' => 'patient@example.test', 'password' => password_hash('Password123', PASSWORD_DEFAULT)]);

        $this->assertFalse($this->userModel->authenticate('patient@example.test', 'WrongPassword'));
    }

    public function testAuthenticateFailsForInactiveUser(): void
    {
        $this->insertUser([
            'email' => 'suspended@example.test',
            'password' => password_hash('Password123', PASSWORD_DEFAULT),
        ]);
        $this->pdo->prepare("UPDATE users SET status = 'suspended' WHERE email = ?")
            ->execute(['suspended@example.test']);

        $this->assertFalse($this->userModel->authenticate('suspended@example.test', 'Password123'));
    }

    public function testLoginLockoutAfterFiveFailedAttempts(): void
    {
        $email = 'lockout@example.test';

        $this->assertFalse(
            $this->userModel->checkLoginAttempts($email),
            'No debería haber bloqueo antes de ningún intento'
        );

        for ($i = 0; $i < 5; $i++) {
            $this->userModel->logLoginAttempt($email, false);
        }

        $this->assertTrue(
            $this->userModel->checkLoginAttempts($email),
            'Después de 5 intentos fallidos en 15 minutos, el email debe quedar bloqueado'
        );
    }

    public function testLoginLockoutDoesNotTriggerWithFewerFailures(): void
    {
        $email = 'almost-lockout@example.test';

        for ($i = 0; $i < 4; $i++) {
            $this->userModel->logLoginAttempt($email, false);
        }

        $this->assertFalse($this->userModel->checkLoginAttempts($email));
    }

    public function testSuccessfulAttemptsDoNotCountTowardsLockout(): void
    {
        $email = 'mixed-attempts@example.test';

        for ($i = 0; $i < 5; $i++) {
            $this->userModel->logLoginAttempt($email, true);
        }

        $this->assertFalse(
            $this->userModel->checkLoginAttempts($email),
            'Los intentos exitosos no deben contar para el bloqueo por fuerza bruta'
        );
    }

    public function testPasswordResetTokenLifecycle(): void
    {
        $this->insertUser(['email' => 'reset@example.test']);

        $result = $this->userModel->createPasswordResetToken('reset@example.test');

        $this->assertIsArray($result);
        $this->assertTrue($this->userModel->validatePasswordResetToken($result['token']));

        $this->assertTrue($this->userModel->resetPasswordWithToken($result['token'], 'BrandNewPass1'));

        $user = $this->userModel->authenticate('reset@example.test', 'BrandNewPass1');
        $this->assertIsArray($user, 'La contraseña nueva debe funcionar para autenticarse');
    }

    public function testPasswordResetTokenIsSingleUse(): void
    {
        $this->insertUser(['email' => 'reset-once@example.test']);
        $result = $this->userModel->createPasswordResetToken('reset-once@example.test');

        $this->assertTrue($this->userModel->resetPasswordWithToken($result['token'], 'FirstNewPass1'));

        // Reusar el mismo token para cambiarla otra vez debe fallar: ya fue
        // marcado como usado.
        $this->assertFalse($this->userModel->resetPasswordWithToken($result['token'], 'SecondNewPass1'));
        $this->assertFalse($this->userModel->validatePasswordResetToken($result['token']));
    }

    public function testExpiredPasswordResetTokenIsRejected(): void
    {
        $this->insertUser(['email' => 'expired@example.test']);

        // Se inserta directamente un token ya vencido para no depender de
        // sleeps reales en el test.
        $token = bin2hex(random_bytes(32));
        $this->pdo->prepare(
            'INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, DATE_SUB(NOW(), INTERVAL 1 HOUR))'
        )->execute(['expired@example.test', $token]);

        $this->assertFalse($this->userModel->validatePasswordResetToken($token));
        $this->assertFalse($this->userModel->resetPasswordWithToken($token, 'WontBeApplied1'));
    }

    public function testRequestingANewTokenInvalidatesThePreviousOne(): void
    {
        $this->insertUser(['email' => 'retoken@example.test']);

        $first = $this->userModel->createPasswordResetToken('retoken@example.test');
        $second = $this->userModel->createPasswordResetToken('retoken@example.test');

        $this->assertFalse(
            $this->userModel->validatePasswordResetToken($first['token']),
            'Pedir un segundo enlace de recuperación debe invalidar el primero'
        );
        $this->assertTrue($this->userModel->validatePasswordResetToken($second['token']));
    }

    public function testDuplicateEmailIsDetectedBeforeCreate(): void
    {
        $this->insertUser(['email' => 'duplicate@example.test']);

        $this->assertNotFalse($this->userModel->findByEmail('duplicate@example.test'));
        $this->assertFalse($this->userModel->findByEmail('does-not-exist@example.test'));
    }
}
