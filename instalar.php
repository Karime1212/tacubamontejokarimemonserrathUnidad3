<?php
require __DIR__ . '/includes/inicio.php';

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Instalación disponible únicamente desde localhost.');
}

if (consulta("SELECT id FROM dulceusuarios WHERE rol = 'admin' LIMIT 1")->fetch()) {
    http_response_code(403);
    exit('Ya existe un administrador. Elimina instalar.php y abre admin/ingreso.php.');
}

$error = '';

if (publicar()) {
    comprobar();
    $nombre = trim(dato('nombre'));
    $correo = strtolower(trim(dato('correo')));
    $clave = dato('clave');

    if (longitud($nombre) < 3 || longitud($nombre) > 100
        || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 190
        || !clavevalida($clave)) {
        $error = 'Revisa nombre, correo y contraseña (8 caracteres, número y símbolo).';
    } else {
        $bloqueo = consulta("SELECT GET_LOCK('dulceinstalacion', 5)")->fetchColumn();

        if ((int) $bloqueo !== 1) {
            throw new RuntimeException('No fue posible bloquear la instalación.');
        }

        try {
            if (consulta("SELECT id FROM dulceusuarios WHERE rol = 'admin'")->fetch()) {
                throw new InvalidArgumentException('Ya se creó un administrador.');
            }

            $id = (new Usuarios(conexion()))->registrar($nombre, $correo, $clave, 'admin');
            consulta("UPDATE dulceusuarios SET correostatus = 'desactivado' WHERE id = ?", [$id]);
            aviso('Administrador creado. Elimina instalar.php e ingresa al panel.');
        } catch (PDOException $fallo) {
            if ($fallo->getCode() === '23000') {
                $error = 'Ese correo ya está registrado. Utiliza otro.';
            } else {
                throw $fallo;
            }
        } catch (InvalidArgumentException $fallo) {
            $error = $fallo->getMessage();
        } finally {
            consulta("SELECT RELEASE_LOCK('dulceinstalacion')");
        }

        if ($error === '') {
            redirigir('admin/ingreso.php');
        }
    }
}

$titulo = 'Primer administrador';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="container seccion">
    <div class="formulario instalacion">
        <span class="etiqueta">CONFIGURACIÓN LOCAL · UNA SOLA VEZ</span>
        <h1>Primer administrador</h1>
        <p>Elige tus datos. No existe una contraseña predeterminada.</p>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= escapar($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?php campo(); ?>
            <label for="nombre">Nombre</label>
            <input
                class="form-control"
                id="nombre"
                name="nombre"
                required
                minlength="3"
                maxlength="100"
            />
            <label for="correo">Correo</label>
            <input
                class="form-control"
                type="email"
                id="correo"
                name="correo"
                required
                maxlength="190"
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
            <small>Incluye un número y un símbolo.</small>
            <button class="boton w-100">Crear administrador</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
