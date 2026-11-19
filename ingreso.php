<?php
require __DIR__ . '/includes/inicio.php';

if (usuario()) {
    redirigir(usuario()['rol'] === 'admin' ? 'admin/index.php' : 'favoritos.php');
}

$error = '';

if (publicar()) {
    comprobar();
    $correo = strtolower(trim(dato('correo')));
    $clave = dato('clave');
    $usuarios = new Usuarios(conexion());
    $error = $usuarios->ingresar($correo, $clave, 'cliente');

    if ($error === '') {
        redirigir('favoritos.php');
    }
}

$titulo = 'Bienvenida de nuevo';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="acceso container">
    <div class="accesoimagen">
        <img src="<?= ruta('assets/img/galletas.jpg') ?>" alt="Un momento dulce" width="1200" height="1000" />
        <div>
            <span class="etiqueta">TU RINCÓN FAVORITO</span>
            <h2>
                Un gusto
                <br />
                volver a verte.
            </h2>
        </div>
    </div>
    <div class="formulario">
        <span class="etiqueta">BIENVENIDA A TU ESQUINA</span>
        <h1>Qué bueno que volviste.</h1>
        <p>Ingresa para guardar tus antojos favoritos.</p>
        <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= escapar($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?php campo(); ?>
            <label for="correo">Correo electrónico</label>
            <input
                class="form-control"
                type="email"
                id="correo"
                name="correo"
                maxlength="190"
                required
                autocomplete="email"
                value="<?= escapar(dato('correo')) ?>"
            />
            <label for="clave">Contraseña</label>
            <input
                class="form-control"
                type="password"
                id="clave"
                name="clave"
                maxlength="72"
                required
                autocomplete="current-password"
            />
            <button class="boton w-100" type="submit">Ingresar →</button>
        </form>
        <p class="alternativa">
            ¿Primera visita?
            <a href="<?= ruta('registro.php') ?>">Crea tu cuenta</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
