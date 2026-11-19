<?php
require __DIR__ . '/includes/inicio.php';

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Comprobación disponible únicamente desde localhost.');
}

$revisiones = [
    'PHP 8.2 o superior' => version_compare(PHP_VERSION, '8.2', '>='),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'Fileinfo para validar imágenes' => extension_loaded('fileinfo'),
    'GD para procesar imágenes' => extension_loaded('gd'),
    'Esta página se abrió con HTTPS' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'Carpeta de fotos con permiso de escritura' => is_writable(__DIR__ . '/uploads/productos'),
];

try {
    $revisiones['Conexión a db_crud_pdo'] = conexion()->query('SELECT DATABASE()')->fetchColumn() === 'db_crud_pdo';

    foreach (['dulceusuarios', 'dulcecategorias', 'dulceproductos', 'dulcefavoritos', 'dulceintentos'] as $tabla) {
        $revisiones['Tabla ' . $tabla] = (bool) consulta('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?', [$tabla])->fetchColumn();
    }
} catch (Throwable $fallo) {
    $revisiones['Conexión a db_crud_pdo'] = false;
}

$titulo = 'Comprobar instalación';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="container seccion">
    <div class="card tarjetapanel instalacion">
        <h1>¿Todo listo?</h1>
        <p>Comprobación local de la instalación.</p>
        <ul class="revisiones">
            <?php foreach ($revisiones as $nombre => $listo): ?>
            <li>
                <span><?= escapar($nombre) ?></span>
                <strong><?= $listo ? '✓ Listo' : 'Revisar' ?></strong>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="discreto">
            Importa database/postres.sql en db_crud_pdo. Configura MySQL en config/conexion.php. Para subir fotografías, activa extension=gd y reinicia Apache si es necesario. Para comprobar HTTPS, abre esta misma página con https:// y verifica el certificado.
        </p>
        <a class="boton" href="<?= ruta('instalar.php') ?>">Crear primer administrador →</a>
    </div>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
