<?php
$titulo = $titulo ?? 'La Dulce Esquina';
$pagina = basename($_SERVER['SCRIPT_NAME']);
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#9b5b4d" />
        <meta name="token" content="<?= escapar(token()) ?>" />
        <title><?= escapar($titulo) ?> | La Dulce Esquina</title>
        <link rel="icon" href="<?= ruta('assets/img/logo.svg') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/vendor/bootstrap.css') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/css/estilos.css') ?>" />
        <script src="<?= ruta('assets/vendor/bootstrap.js') ?>" defer></script>
        <script src="<?= ruta('assets/js/aplicacion.js') ?>" defer></script>
    </head>
    <body data-base="<?= escapar(rtrim(ruta(), '/')) ?>">
        <a class="saltar" href="#contenido">Saltar al contenido</a>
        <header class="encabezado">
            <nav class="navbar navbar-expand-lg container py-3" aria-label="Navegación principal">
                <a class="marca" href="<?= ruta('index.php') ?>">
                    <span>
                        La Dulce Esquina
                    </span>
                </a>
                <button
                    class="boton secundario navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navegacion"
                    aria-controls="navegacion"
                    aria-expanded="false"
                >
                    Menú ☰
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navegacion">
                    <div class="navegacion">
                        <a href="<?= ruta('index.php') ?>">Inicio</a>
                        <a href="<?= ruta('index.php#catalogo') ?>">Nuestro menú</a>
                        <?php if (usuario() && usuario()['rol'] === 'cliente'): ?>
                        <a href="<?= ruta('favoritos.php') ?>">Mis favoritos</a>
                        <form action="<?= ruta('salir.php') ?>" method="post">
                            <?php campo(); ?>
                            <button class="boton secundario compacto">Salir</button>
                        </form>
                        <?php elseif (!usuario()): ?>
                        <a href="<?= ruta('ingreso.php') ?>">Ingresar</a>
                        <a class="boton compacto" href="<?= ruta('registro.php') ?>">Crear cuenta</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>
        <main id="contenido">
            <?php if (isset($_SESSION['aviso'])): ?>
            <div class="container pt-3">
                <div class="alert alert-<?= escapar($_SESSION['aviso']['tipo']) ?>" role="status"><?= escapar($_SESSION['aviso']['mensaje']) ?></div>
            </div>
            <?php unset($_SESSION['aviso']); ?>
            <?php endif; ?>
            