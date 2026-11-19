<?php

function conexion(): PDO
{
    static $conexion = null;

    if ($conexion === null) {
        $datos = require __DIR__ . '/config/conexion.php';
        $cadena = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $datos['servidor'],
            $datos['puerto'],
            $datos['base']
        );

        $conexion = new PDO($cadena, $datos['usuario'], $datos['clave'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    return $conexion;
}
