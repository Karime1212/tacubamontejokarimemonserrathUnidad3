<?php
$titulo = $titulo ?? 'Administración';
$pagina = basename($_SERVER['SCRIPT_NAME']);
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="token" content="<?= escapar(token()) ?>" />
        <title><?= escapar($titulo) ?> | Administración</title>
        <link rel="icon" href="<?= ruta('assets/img/logo.svg') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/vendor/bootstrap.css') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/css/estilos.css') ?>" />
        <script src="<?= ruta('assets/vendor/bootstrap.js') ?>" defer></script>
        <script src="<?= ruta('assets/js/aplicacion.js') ?>" defer></script>
    </head>
    <body class="administracion" data-base="<?= escapar(rtrim(ruta(), '/')) ?>">
        <a class="saltar" href="#contenido">Saltar al contenido</a>
        <header class="barrapanel">
            <a class="marca" href="<?= ruta('admin/index.php') ?>">
                <img src="<?= ruta('assets/img/logo.svg') ?>" alt="" width="44" height="44" />
                <span>
                    La Dulce Esquina
                    <small>ADMINISTRACIÓN</small>
                </span>
            </a>
            <div class="d-flex gap-2">
                <a class="boton secundario compacto" href="<?= ruta('index.php') ?>">Ver sitio público →</a>
                <button
                    class="boton secundario compacto d-lg-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menupanel"
                    aria-controls="menupanel"
                    aria-expanded="false"
                >
                    Menú ☰
                </button>
            </div>
        </header>
        <aside class="lateral collapse d-lg-block" id="menupanel">
            <div class="persona">
                <strong><?= escapar(usuario()['nombre']) ?></strong>
                <small>Administrador</small>
            </div>
            <nav aria-label="Administración">
                <a class="<?= $pagina === 'index.php' ? 'activo' : '' ?>" href="<?= ruta('admin/index.php') ?>">◫ Resumen</a>
                <a class="<?= in_array($pagina, ['productos.php', 'editar.php']) ? 'activo' : '' ?>" href="<?= ruta('admin/productos.php') ?>">♡ Productos</a>
                <a class="<?= $pagina === 'usuarios.php' ? 'activo' : '' ?>" href="<?= ruta('admin/usuarios.php') ?>">♙ Clientes</a>
            </nav>
            <p>
                Un catálogo bonito.
                <br />
                Una esquina llena de antojos.
            </p>
            <form action="<?= ruta('salir.php') ?>" method="post">
                <?php campo(); ?>
                <button class="boton secundario compacto w-100">Cerrar sesión</button>
            </form>
        </aside>
        <main class="contenidopanel" id="contenido">
            <span class="etiqueta">LA DULCE ESQUINA / ADMINISTRACIÓN</span>
            <h1><?= escapar($titulo) ?></h1>
            <?php if (isset($_SESSION['aviso'])): ?>
            <div class="alert alert-<?= escapar($_SESSION['aviso']['tipo']) ?>" role="status"><?= escapar($_SESSION['aviso']['mensaje']) ?></div>
            <?php unset($_SESSION['aviso']); ?>
            <?php endif; ?>
            