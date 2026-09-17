<?php
/**
 * Carga las variables de entorno (.env) en $_ENV/getenv() antes de que
 * cualquier archivo de configuración (database.php, mail.php, app.php) las
 * lea.
 *
 * Ningún punto de entrada de la aplicación llamaba nunca a
 * Dotenv::createImmutable(...)->load(), a pesar de que composer.json
 * declara vlucas/phpdotenv como dependencia y de que config/*.php están
 * escritos como si $_ENV ya viniera poblado. En la práctica esto significa
 * que editar .env no tenía ningún efecto: la app siempre usaba los valores
 * por defecto hardcodeados en config/database.php (localhost/root/"").
 *
 * Este bootstrap usa vlucas/phpdotenv cuando está instalado (composer
 * install) y, si no lo está, hace un parseo mínimo de .env como red de
 * seguridad para no dejar la aplicación completamente rota en un entorno
 * donde `composer install` todavía no se ejecutó.
 */

if (!function_exists('medibook_bootstrap_env')) {
    function medibook_bootstrap_env(): void
    {
        static $bootstrapped = false;

        if ($bootstrapped) {
            return;
        }

        $bootstrapped = true;
        $root = dirname(__DIR__);

        if (file_exists($root . '/vendor/autoload.php')) {
            require_once $root . '/vendor/autoload.php';
        }

        if (class_exists(\Dotenv\Dotenv::class)) {
            \Dotenv\Dotenv::createImmutable($root)->safeLoad();
        } else {
            $envFile = $root . '/.env';

            if (file_exists($envFile)) {
                foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);

                    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                        continue;
                    }

                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, " \t\n\r\0\x0B\"'");

                    if ($key !== '' && getenv($key) === false) {
                        putenv("{$key}={$value}");
                        $_ENV[$key] = $value;
                        $_SERVER[$key] = $value;
                    }
                }
            }
        }

        // Red de seguridad: en muchas instalaciones PHP (Laragon/XAMPP
        // incluidos) `variables_order` en php.ini no incluye "E", así que
        // $_ENV nunca se rellena automáticamente desde variables de entorno
        // reales del sistema operativo (export en shell, Docker ENV,
        // systemd Environment=, Apache SetEnv, etc.). Dotenv en modo
        // "immutable" además se niega a escribir en $_ENV una clave que ya
        // exista según getenv(), así que esas dos cosas combinadas dejaban
        // $_ENV completamente vacío para cualquier variable definida solo a
        // nivel de sistema operativo, y config/*.php únicamente lee
        // $_ENV[...] (nunca getenv()). Este bucle final asegura que toda
        // variable visible vía getenv() también quede en $_ENV.
        foreach (getenv() as $key => $value) {
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

medibook_bootstrap_env();
