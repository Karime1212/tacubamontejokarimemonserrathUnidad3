<?php
require __DIR__ . '/includes/inicio.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$producto = $id ? (new Catalogo(conexion()))->buscar($id) : null;

if (!$producto || !$producto['activo']) {
    http_response_code(404);
    $titulo = 'Producto no disponible';
    require __DIR__ . '/includes/cabecera.php';
    echo '<section class="container seccion"><h1>No encontramos este antojo.</h1><a class="boton" href="' . ruta('index.php#catalogo') . '">Volver al menú</a></section>';
    require __DIR__ . '/includes/pie.php';
    exit;
}

$titulo = $producto['nombre'];
require __DIR__ . '/includes/cabecera.php';
?>
<section class="detalle container">
    <a class="enlace" href="<?= ruta('index.php#catalogo') ?>">← Volver al menú</a>
    <div class="row g-5 align-items-center mt-1">
        <div class="col-lg-6">
            <img
                class="fotodetalle"
                src="<?= escapar(ruta(fotovalida($producto['foto']) && $producto['foto'] ? $producto['foto'] : 'assets/img/pasteles.jpg')) ?>"
                alt="<?= escapar($producto['nombre']) ?>"
                width="1200"
                height="1000"
            />
        </div>
        <div class="col-lg-6">
            <span class="etiqueta"><?= escapar($producto['categoria']) ?></span>
            <h1><?= escapar($producto['nombre']) ?></h1>
            <p><?= nl2br(escapar($producto['descripcion'])) ?></p>
            <div class="precio grande"><?= dinero($producto['precio']) ?></div>
            <p class="discreto">
                Precio de demostración. La disponibilidad y los ingredientes deben confirmarse con
                el establecimiento.
            </p>
            <button class="boton" type="button" data-favorito="<?= $producto['id'] ?>" aria-pressed="false">
                ♡ Guardar en favoritos
            </button>
            <div class="recordatorio">
                <strong>Una pausa para disfrutar.</strong>
                <p>Combina tu antojo con un café o una malteada de nuestro menú.</p>
                <a href="<?= ruta('index.php?categoria=bebidas#catalogo') ?>">Ver bebidas →</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
