<?php
require __DIR__ . '/../includes/inicio.php';

if (usuario()) {
    if (usuario()['rol'] === 'admin') {
        redirigir('admin/index.php');
    }
    http_response_code(403);
    exit('Ya tienes una sesión de cliente. Ciérrala o utiliza una ventana de incógnito para entrar como administrador.');
}

$error = '';

if (publicar()) {
    comprobar();
    $correo = strtolower(trim(dato('correo')));
    $clave = dato('clave');
    $error = (new Usuarios(conexion()))->ingresar($correo, $clave, 'admin');

    if ($error === '') {
        redirigir('admin/index.php');
    }
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="token" content="<?= escapar(token()) ?>" />
        <title>Administración | La Dulce Esquina</title>
        <link rel="icon" href="<?= ruta('assets/img/logo.svg') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/vendor/bootstrap.css') ?>" />
        <link rel="stylesheet" href="<?= ruta('assets/css/estilos.css') ?>" />
        <script src="<?= ruta('assets/js/aplicacion.js') ?>" defer></script>
    </head>
    <body class="ingresopanel" data-base="<?= escapar(rtrim(ruta(), '/')) ?>">
        <main class="container accesopanel">
            <section>
                <a class="marca" href="<?= ruta('index.php') ?>">
                    <img src="<?= ruta('assets/img/logo.svg') ?>" alt="" width="48" height="48" />
                    <span>La Dulce Esquina</span>
                </a>
                <span class="etiqueta">DETRÁS DE CADA DULCE MOMENTO</span>
                <h1>
                    Hay alguien
                    <br />
                    que lo hace posible.
                </h1>
                <p>Cuida tu catálogo y organiza los antojos de tu comunidad.</p>
                <a class="enlace" href="<?= ruta('index.php') ?>">← Volver al sitio público</a>
            </section>
            <section class="formulario">
                <span class="etiqueta">ACCESO PRIVADO</span>
                <h2>Administración</h2>
                <p>Ingresa con tu cuenta de administrador.</p>
                <?php if (isset($_SESSION['aviso'])): ?>
                <div class="alert alert-success"><?= escapar($_SESSION['aviso']['mensaje']) ?></div>
                <?php unset($_SESSION['aviso']); endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= escapar($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <?php campo(); ?>
                    <label for="correo">Correo del administrador</label>
                    <input
                        class="form-control"
                        type="email"
                        id="correo"
                        name="correo"
                        required
                        maxlength="190"
                        autocomplete="username"
                        value="<?= escapar(dato('correo')) ?>"
                    />
                    <label for="clave">Contraseña</label>
                    <input
                        class="form-control"
                        type="password"
                        id="clave"
                        name="clave"
                        required
                        maxlength="72"
                        autocomplete="current-password"
                    />
                    <button class="boton w-100">Entrar al panel →</button>
                </form>
                <small>No existe registro público de administradores.</small>
            </section>
        </main>
    </body>
</html>
