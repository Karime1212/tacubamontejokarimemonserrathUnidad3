<?php
require __DIR__ . '/../includes/inicio.php';
exigir(true);

if (!publicar()) {
    http_response_code(405);
    header('Allow: POST');
    exit();
}

comprobar();
$id = filter_var(dato('id'), FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(422);
    exit('Producto no válido.');
}

$catalogo = new Catalogo(conexion());

if (!$catalogo->buscar($id)) {
    http_response_code(404);
    exit('El producto ya no existe.');
}

$catalogo->eliminar($id);
aviso('Producto eliminado. Sus favoritos también fueron retirados.');
redirigir('admin/productos.php');
