<?php
namespace Tests\Unit;

use MediBook\Helpers\SecurityHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Cada test arranca con una sesión limpia para no arrastrar
        // tokens CSRF de un test anterior.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testCsrfTokenIsStableUntilConsumed(): void
    {
        $first = SecurityHelper::csrfToken('form-a');
        $second = SecurityHelper::csrfToken('form-a');

        $this->assertSame($first, $second, 'El mismo formulario debe reutilizar el token mientras no se consuma');
        $this->assertGreaterThanOrEqual(64, strlen($first), 'El token debe tener suficiente entropía (32 bytes en hex)');
    }

    public function testDifferentFormsGetDifferentTokens(): void
    {
        $tokenA = SecurityHelper::csrfToken('form-a');
        $tokenB = SecurityHelper::csrfToken('form-b');

        $this->assertNotSame($tokenA, $tokenB);
    }

    public function testValidTokenPassesVerification(): void
    {
        $token = SecurityHelper::csrfToken('login');

        $this->assertTrue(SecurityHelper::verifyCsrfToken($token, 'login'));
    }

    public function testTokenIsSingleUse(): void
    {
        $token = SecurityHelper::csrfToken('login');

        $this->assertTrue(SecurityHelper::verifyCsrfToken($token, 'login'), 'El primer uso debe ser válido');
        $this->assertFalse(SecurityHelper::verifyCsrfToken($token, 'login'), 'Reutilizar el mismo token debe fallar (protege contra replay)');
    }

    public function testWrongTokenIsRejected(): void
    {
        SecurityHelper::csrfToken('login');

        $this->assertFalse(SecurityHelper::verifyCsrfToken('token-inventado-por-un-atacante', 'login'));
    }

    public function testMissingTokenIsRejected(): void
    {
        SecurityHelper::csrfToken('login');

        $this->assertFalse(SecurityHelper::verifyCsrfToken(null, 'login'));
        $this->assertFalse(SecurityHelper::verifyCsrfToken('', 'login'));
    }

    public function testTokenForOneFormDoesNotValidateAnotherForm(): void
    {
        $loginToken = SecurityHelper::csrfToken('login');

        $this->assertFalse(SecurityHelper::verifyCsrfToken($loginToken, 'register'));
    }

    #[DataProvider('strongPasswordProvider')]
    public function testIsStrongPassword(string $password, bool $expected): void
    {
        $this->assertSame($expected, SecurityHelper::isStrongPassword($password));
    }

    public static function strongPasswordProvider(): array
    {
        return [
            'too short' => ['Ab1', false],
            'no uppercase' => ['lowercase123', false],
            'no lowercase' => ['UPPERCASE123', false],
            'no digit' => ['NoDigitsHere', false],
            'valid strong password' => ['ValidPass123', true],
            'valid with symbols too' => ['V4lid!Pass#', true],
        ];
    }
}
