<?php
namespace MediBook\Helpers;

/**
 * Utilidades de seguridad compartidas por toda la aplicación.
 *
 * Antes de este cambio este archivo existía en el repositorio pero estaba
 * completamente vacío (0 bytes): la clase nunca se implementó, así que
 * ningún formulario tenía protección CSRF real a pesar de que el README
 * y config/app.php ('csrf_token_name') asumían que sí.
 */
class SecurityHelper
{
    private const CSRF_SESSION_KEY = '_csrf_tokens';
    private const CSRF_MAX_TOKENS = 20;

    /**
     * Genera (o reutiliza) un token CSRF para el formulario indicado y lo
     * guarda en sesión para poder validarlo después.
     */
    public static function csrfToken(string $formName = 'default'): string
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION[self::CSRF_SESSION_KEY]) || !is_array($_SESSION[self::CSRF_SESSION_KEY])) {
            $_SESSION[self::CSRF_SESSION_KEY] = [];
        }

        if (empty($_SESSION[self::CSRF_SESSION_KEY][$formName])) {
            // Si hay demasiados tokens acumulados (formularios abiertos en
            // muchas pestañas), se descartan los más antiguos.
            if (count($_SESSION[self::CSRF_SESSION_KEY]) >= self::CSRF_MAX_TOKENS) {
                array_shift($_SESSION[self::CSRF_SESSION_KEY]);
            }

            $_SESSION[self::CSRF_SESSION_KEY][$formName] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_SESSION_KEY][$formName];
    }

    /**
     * Devuelve el campo <input type="hidden"> listo para insertar en un
     * formulario POST.
     */
    public static function csrfField(string $formName = 'default'): string
    {
        $token = htmlspecialchars(self::csrfToken($formName), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    /**
     * Valida el token CSRF recibido en el POST contra el guardado en sesión.
     * El token se consume (de un solo uso) tanto si es válido como si no,
     * para impedir reintentos por fuerza bruta contra el mismo valor.
     */
    public static function verifyCsrfToken(?string $submittedToken, string $formName = 'default'): bool
    {
        self::ensureSessionStarted();

        $expected = $_SESSION[self::CSRF_SESSION_KEY][$formName] ?? null;
        unset($_SESSION[self::CSRF_SESSION_KEY][$formName]);

        if (!is_string($submittedToken) || !is_string($expected) || $submittedToken === '') {
            return false;
        }

        return hash_equals($expected, $submittedToken);
    }

    /**
     * Valida $_POST['_token'] para el formulario dado y, si falla, corta la
     * petición con un 419 (convención usada por varios frameworks PHP para
     * "token de página expirado").
     */
    public static function requireValidCsrfToken(string $formName = 'default'): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }

        if (!self::verifyCsrfToken($_POST['_token'] ?? null, $formName)) {
            http_response_code(419);
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
                . '<title>Sesión expirada - MediBook</title></head><body>'
                . '<h1>419 - Token de seguridad inválido o expirado</h1>'
                . '<p>Por seguridad, recarga la página e intenta nuevamente.</p>'
                . '<a href="javascript:history.back()">Volver</a>'
                . '</body></html>';
            exit;
        }
    }

    /**
     * Valida la fortaleza de una contraseña: mínimo 8 caracteres, con al
     * menos una mayúscula, una minúscula y un número. Centraliza una regla
     * que antes estaba duplicada (y a veces olvidada) en varios formularios.
     */
    public static function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/\d/', $password) === 1;
    }

    private static function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            require_once __DIR__ . '/SessionHelper.php';
            SessionHelper::start();
        }
    }
}
