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
            return;
        }

        $envFile = $root . '/.env';

        if (!file_exists($envFile)) {
            return;
        }

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

medibook_bootstrap_env();
