<?php

/**
 * Configuración de PHP-CS-Fixer.
 *
 * composer.json ya declara friendsofphp/php-cs-fixer como dependencia y el
 * README afirma que el proyecto sigue PSR-12, pero no existía ningún
 * archivo de configuración: `vendor/bin/php-cs-fixer fix` no tenía forma
 * de saber qué reglas aplicar ni qué carpetas tocar.
 *
 * El chequeo de estilo en CI corre en modo informativo (no bloquea el
 * pipeline) porque este audit se enfocó en seguridad/corrección, no en
 * reformatear archivos existentes sin revisión humana.
 */

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/config',
    ])
    ->exclude(['vendor']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(false);
