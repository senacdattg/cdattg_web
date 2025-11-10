<?php

declare(strict_types=1);

/**
 * Excepción dedicada cuando falta una variable de entorno requerida.
 */
final class MissingEnvironmentVariable extends RuntimeException
{

}

/**
 * Script de diagnóstico para validar la conectividad MySQL desde el contenedor.
 * Las credenciales se obtienen de variables de entorno para evitar secretos hardcodeados.
 */
function envOrFail(string $key): string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        throw new MissingEnvironmentVariable("Variable de entorno {$key} no definida.");
    }

    return $value;
}

try {
    $host = envOrFail('DB_HOST');
    $port = getenv('DB_PORT') ?: '3306';
    $database = envOrFail('DB_DATABASE');
    $username = envOrFail('DB_USERNAME');
    $password = envOrFail('DB_PASSWORD');

    echo 'host=' . gethostbyname($host) . PHP_EOL;

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $database);
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo 'pdo=ok' . PHP_EOL;
} catch (Throwable $e) {
    echo 'pdo=' . $e->getMessage() . PHP_EOL;
}
