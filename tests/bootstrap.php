<?php
/**
 * Bootstrap de PHPUnit.
 *
 * Los tests de integración necesitan una base de datos MySQL real y
 * desechable (no tocan nunca la base de desarrollo). Por defecto apuntan
 * a `medibook_test` en localhost con las credenciales típicas de un
 * entorno local (root sin contraseña, como Laragon/XAMPP); en CI, estas
 * variables se sobreescriben con las del servicio de MySQL del workflow
 * (ver .github/workflows/ci.yml).
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$testDbDefaults = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'medibook_test',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
    'DB_CHARSET' => 'utf8mb4',
];

foreach ($testDbDefaults as $key => $default) {
    $value = getenv($key);
    $value = ($value === false || $value === '') ? $default : $value;

    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
