<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/../app/Catalogo.php';
require_once __DIR__ . '/../app/Usuarios.php';

date_default_timezone_set('America/Mexico_City');
ini_set('session.use_strict_mode', '1');
session_name('dulceesquina');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax'
]);
session_start();

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header(
    "Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; connect-src 'self'; style-src 'self'; script-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'"
);

set_exception_handler(function (Throwable $error): void {
    http_response_code(500);
    error_log('La Dulce Esquina: fallo interno (' . get_class($error) . ').');

    if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/')) {
        respuesta(['error' => 'No fue posible completar la operación.'], 500);
    }

    echo '<h1>No se pudo completar la operación</h1>';
    echo '<p>Comprueba que MySQL esté iniciado e importa database/postres.sql ';
    echo 'en db_crud_pdo. Revisa config/conexion.php.</p>';
});
