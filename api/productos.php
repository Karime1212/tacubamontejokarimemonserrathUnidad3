<?php
require __DIR__ . '/../includes/inicio.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    respuesta(['error' => 'Método no permitido.'], 405);
}

$categoria = trim(dato('categoria', $_GET));
$busqueda = trim(dato('buscar', $_GET));
$permitidas = ['', 'donas', 'pays', 'pasteles', 'galletas', 'bebidas'];

if (!in_array($categoria, $permitidas, true) || longitud($busqueda) > 80) {
    respuesta(['error' => 'Filtro no válido.'], 422);
}

$catalogo = new Catalogo(conexion());
$productos = $catalogo->listar($categoria, $busqueda);
$guardados = [];

if (usuario() && usuario()['rol'] === 'cliente') {
    $guardados = consulta('SELECT productoid FROM dulcefavoritos WHERE usuarioid = ?', [
        usuario()['id']
    ])->fetchAll(PDO::FETCH_COLUMN);
    $guardados = array_map('intval', $guardados);
}

foreach ($productos as &$producto) {
    $producto['favorito'] = in_array((int) $producto['id'], $guardados, true);
    $producto['foto'] = fotovalida($producto['foto']) ? $producto['foto'] : '';
}
unset($producto);

respuesta(['datos' => $productos, 'total' => count($productos)]);
