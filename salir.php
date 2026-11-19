<?php
require __DIR__ . '/includes/inicio.php';

if (!publicar()) {
    http_response_code(405);
    header('Allow: POST');
    exit();
}

comprobar();
$administrador = usuario() && usuario()['rol'] === 'admin';
$_SESSION = [];
$configuracion = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 42000,
    'path' => $configuracion['path'],
    'secure' => $configuracion['secure'],
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_destroy();
redirigir($administrador ? 'admin/ingreso.php' : 'ingreso.php');
