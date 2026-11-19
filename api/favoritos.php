<?php
require __DIR__ . '/../includes/inicio.php';

if (!usuario()) {
    respuesta(['error' => 'Inicia sesión para guardar tus favoritos.'], 401);
}

if (usuario()['rol'] !== 'cliente') {
    respuesta(['error' => 'Utiliza una cuenta de cliente.'], 403);
}

$idusuario = (int) usuario()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productos = consulta(
        'SELECT p.*, c.nombre AS categoria, c.slug
         FROM dulcefavoritos f
         JOIN dulceproductos p ON p.id = f.productoid
         JOIN dulcecategorias c ON c.id = p.categoriaid
         WHERE f.usuarioid = ? AND p.activo = 1
         ORDER BY f.creado DESC, p.id',
        [$idusuario]
    )->fetchAll();

    foreach ($productos as &$producto) {
        $producto['favorito'] = true;
        $producto['foto'] = fotovalida($producto['foto']) ? $producto['foto'] : '';
    }
    unset($producto);
    respuesta(['datos' => $productos, 'total' => count($productos)]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: GET, POST');
    respuesta(['error' => 'Método no permitido.'], 405);
}

$recibido = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if (!hash_equals(token(), $recibido)) {
    respuesta(['error' => 'Solicitud no válida. Recarga la página.'], 403);
}

try {
    $datos = json_decode(file_get_contents('php://input'), true, 32, JSON_THROW_ON_ERROR);

    if (!is_array($datos)) {
        throw new InvalidArgumentException('Datos no válidos.');
    }

    $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
    $accion = $datos['accion'] ?? '';

    if (!$id || $id < 1 || !in_array($accion, ['agregar', 'quitar'], true)) {
        throw new InvalidArgumentException('Producto o acción no válidos.');
    }

    $conexion = conexion();
    $conexion->beginTransaction();

    try {
        $producto = consulta('SELECT id, activo FROM dulceproductos WHERE id = ? FOR UPDATE', [
            $id
        ])->fetch();

        if (!$producto || ($accion === 'agregar' && !$producto['activo'])) {
            throw new InvalidArgumentException('Este producto ya no está disponible.');
        }

        if ($accion === 'agregar') {
            consulta('INSERT IGNORE INTO dulcefavoritos (usuarioid, productoid) VALUES (?, ?)', [
                $idusuario,
                $id
            ]);
        } else {
            consulta('DELETE FROM dulcefavoritos WHERE usuarioid = ? AND productoid = ?', [
                $idusuario,
                $id
            ]);
        }

        $conexion->commit();
    } catch (Throwable $fallo) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        throw $fallo;
    }

    respuesta(['guardado' => $accion === 'agregar']);
} catch (JsonException | InvalidArgumentException $fallo) {
    respuesta(['error' => $fallo->getMessage()], 422);
}
