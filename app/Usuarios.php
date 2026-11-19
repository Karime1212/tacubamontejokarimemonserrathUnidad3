<?php

class Usuarios
{
    public function __construct(private PDO $conexion) {}

    public function registrar(
        string $nombre,
        string $correo,
        string $clave,
        string $rol = 'cliente'
    ): int {
        $sentencia = $this->conexion->prepare(
            'INSERT INTO dulceusuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)'
        );
        $sentencia->execute([$nombre, $correo, password_hash($clave, PASSWORD_DEFAULT), $rol]);

        return (int) $this->conexion->lastInsertId();
    }

    public function ingresar(string $correo, string $clave, string $rol): string
    {
        $llave = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'local') . '|' . $correo);
        $ahora = time();
        consulta('INSERT IGNORE INTO dulceintentos (llave, intentos, inicio) VALUES (?, 0, ?)', [
            $llave,
            $ahora
        ]);
        consulta(
            'UPDATE dulceintentos SET intentos = 0, inicio = ? WHERE llave = ? AND inicio < ?',
            [$ahora, $llave, $ahora - 900]
        );
        $intentos = consulta('SELECT intentos FROM dulceintentos WHERE llave = ?', [
            $llave
        ])->fetchColumn();

        if ($intentos >= 5) {
            return 'Demasiados intentos. Espera 15 minutos desde el primer intento.';
        }

        $sentencia = $this->conexion->prepare('SELECT * FROM dulceusuarios WHERE correo = ?');
        $sentencia->execute([$correo]);
        $persona = $sentencia->fetch();

        if (
            !$persona ||
            !password_verify($clave, $persona['contrasena']) ||
            $persona['rol'] !== $rol
        ) {
            consulta('UPDATE dulceintentos SET intentos = intentos + 1 WHERE llave = ?', [$llave]);
            return 'Correo o contraseña incorrectos para este acceso.';
        }

        consulta('DELETE FROM dulceintentos WHERE llave = ?', [$llave]);
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => (int) $persona['id'],
            'nombre' => $persona['nombre'],
            'correo' => $persona['correo'],
            'rol' => $persona['rol']
        ];
        $_SESSION['token'] = bin2hex(random_bytes(32));

        return '';
    }
}
