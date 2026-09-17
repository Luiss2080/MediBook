<?php
namespace MediBook\Helpers;

/**
 * Arranque de sesión centralizado.
 *
 * Antes de este cambio este archivo era un stub vacío. Cada vista llamaba a
 * session_start() directamente con las opciones por defecto de PHP, así que
 * los valores ya definidos en config/app.php ('session' => [...]) nunca se
 * aplicaban: ninguna cookie de sesión llevaba realmente el nombre, el
 * lifetime o el flag `secure` configurados.
 */
class SessionHelper
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $appConfigPath = __DIR__ . '/../../config/app.php';
        $config = file_exists($appConfigPath) ? (require $appConfigPath) : [];
        $session = $config['session'] ?? [];

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        session_name($session['name'] ?? 'medibook_session');

        session_set_cookie_params([
            'lifetime' => $session['lifetime'] ?? 7200,
            'path' => $session['path'] ?? '/',
            'domain' => $session['domain'] ?? '',
            // Solo forzamos "secure" cuando la petición ya llega por HTTPS:
            // exigirlo siempre rompería el desarrollo local por HTTP, pero
            // el valor fijo `false` del config original lo desactivaba
            // incluso en producción bajo HTTPS.
            'secure' => $isHttps || !empty($session['secure']),
            'httponly' => $session['httponly'] ?? true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;
    }

    /**
     * Regenera el ID de sesión preservando los datos, para usar
     * inmediatamente después de una autenticación exitosa y así evitar
     * fijación de sesión (session fixation).
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
}
