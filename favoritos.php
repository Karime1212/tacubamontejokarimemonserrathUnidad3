<?php
require __DIR__ . '/includes/inicio.php';
exigir();

if (usuario()['rol'] === 'admin') {
    redirigir('admin/index.php');
}

$titulo = 'Mis favoritos';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="container seccion">
    <div class="titular">
        <div>
            <span class="etiqueta">GUARDADOS CON MUCHO ANTOJO</span>
            <h1>
                Tu pequeña
                <br />
                <em>lista de felicidad.</em>
            </h1>
            <p>Hola, <?= escapar(usuario()['nombre']) ?>. Aquí están tus favoritos, guardados en tu cuenta.</p>
        </div>
        <a class="boton secundario" href="<?= ruta('index.php#catalogo') ?>">Seguir explorando →</a>
    </div>
    <div class="productos" id="favoritos" aria-live="polite" aria-busy="true">
        <div class="esqueleto"></div>
        <div class="esqueleto"></div>
        <div class="esqueleto"></div>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
