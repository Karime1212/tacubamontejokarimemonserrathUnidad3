<?php
require __DIR__ . '/includes/inicio.php';

if (usuario()) {
    redirigir(usuario()['rol'] === 'admin' ? 'admin/index.php' : 'favoritos.php');
}

$error = '';

if (publicar()) {
    comprobar();
    $nombre = trim(dato('nombre'));
    $correo = strtolower(trim(dato('correo')));
    $clave = dato('clave');
    $confirmacion = dato('confirmacion');

    if (longitud($nombre) < 3 || longitud($nombre) > 100) {
        $error = 'Escribe tu nombre, entre 3 y 100 caracteres.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 190) {
        $error = 'Escribe un correo electrónico válido.';
    } elseif (!clavevalida($clave)) {
        $error = 'Tu contraseña necesita al menos 8 caracteres, un número y un símbolo; máximo 72 bytes.';
    } elseif ($clave !== $confirmacion) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        try {
            $usuarios = new Usuarios(conexion());
            $usuarios->registrar($nombre, $correo, $clave);
            unset($clave, $confirmacion);
            aviso('¡Cuenta creada! Ya puedes iniciar sesión.');

            redirigir('ingreso.php');
        } catch (PDOException $fallo) {
            if ($fallo->getCode() === '23000') {
                $error = 'Ese correo ya tiene una cuenta. Puedes iniciar sesión.';
            } else {
                throw $fallo;
            }
        }
    }
}

$titulo = 'Crea tu cuenta';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="acceso container">
    <div class="accesoimagen">
        <img src="<?= ruta('assets/img/donas.jpg') ?>" alt="Donas para una nueva historia" width="1200" height="1000" />
        <div>
            <span class="etiqueta">UN POQUITO DE FELICIDAD</span>
            <h2>
                Tu antojo favorito
                <br />
                empieza aquí.
            </h2>
        </div>
    </div>
    <div class="formulario">
        <span class="etiqueta">HOLA, QUÉ GUSTO CONOCERTE</span>
        <h1>
            Hagamos una
            <br />
            dulce conexión.
        </h1>
        <p>Crea tu cuenta y guarda lo que más se te antoje.</p>
        <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><?= escapar($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?php campo(); ?>
            <label for="nombre">Tu nombre</label>
            <input
                class="form-control"
                id="nombre"
                name="nombre"
                required
                minlength="3"
                maxlength="100"
                autocomplete="name"
                value="<?= escapar(dato('nombre')) ?>"
            />
            <label for="correo">Correo electrónico</label>
            <input
                class="form-control"
                type="email"
                id="correo"
                name="correo"
                required
                maxlength="190"
                autocomplete="email"
                value="<?= escapar(dato('correo')) ?>"
            />
            <label for="clave">Contraseña</label>
            <input
                class="form-control"
                type="password"
                id="clave"
                name="clave"
                required
                minlength="8"
                maxlength="72"
                autocomplete="new-password"
            />
            <small>Al menos 8 caracteres, un número y un símbolo.</small>
            <label for="confirmacion">Confirma tu contraseña</label>
            <input
                class="form-control"
                type="password"
                id="confirmacion"
                name="confirmacion"
                required
                minlength="8"
                maxlength="72"
                autocomplete="new-password"
            />
            <small class="d-block mt-3">
                Usa tu correo para iniciar sesión y guardar tus favoritos.
            </small>
            <button class="boton w-100" type="submit">Crear mi cuenta →</button>
        </form>
        <p class="alternativa">
            ¿Ya nos conocemos?
            <a href="<?= ruta('ingreso.php') ?>">Ingresa aquí</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
