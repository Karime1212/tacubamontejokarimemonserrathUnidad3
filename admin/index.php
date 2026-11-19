<?php
require __DIR__ . '/../includes/inicio.php';
exigir(true);
$productos = consulta('SELECT COUNT(*) FROM dulceproductos')->fetchColumn();
$activos = consulta('SELECT COUNT(*) FROM dulceproductos WHERE activo = 1')->fetchColumn();
$clientes = consulta("SELECT COUNT(*) FROM dulceusuarios WHERE rol = 'cliente'")->fetchColumn();
$favoritos = consulta('SELECT COUNT(*) FROM dulcefavoritos')->fetchColumn();
$populares = consulta(
    'SELECT p.nombre, p.foto, COUNT(f.usuarioid) AS total
     FROM dulceproductos p
     LEFT JOIN dulcefavoritos f ON f.productoid = p.id
     GROUP BY p.id, p.nombre, p.foto
     ORDER BY total DESC, p.id LIMIT 5'
)->fetchAll();
$titulo = 'Un vistazo a tu esquina';
require __DIR__ . '/../includes/panel.php';
?>
<p class="discreto">
    Hola, <?= escapar(usuario()['nombre']) ?>. Organiza el catálogo y conoce los antojos más guardados.
</p>
<div class="row g-3 mb-4">
    <?php foreach ([['Productos', $productos], ['Disponibles', $activos], ['Clientes', $clientes], ['Favoritos', $favoritos]] as [$nombre, $total]): ?>
    <div class="col-6 col-xl-3">
        <div class="card resumen">
            <span><?= escapar($nombre) ?></span>
            <strong><?= escapar($total) ?></strong>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="row g-4">
    <div class="col-xl-8">
        <section class="card tarjetapanel">
            <h2>Antojos más guardados</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Favoritos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($populares as $producto): ?>
                        <tr>
                            <td><?= escapar($producto['nombre']) ?></td>
                            <td><?= escapar($producto['total']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="card tarjetapanel">
            <span class="etiqueta">DALE UN TOQUE NUEVO</span>
            <h2>¿Algo rico para agregar?</h2>
            <p>Registra un producto, ajusta su precio o sube una nueva fotografía.</p>
            <a class="boton" href="<?= ruta('admin/editar.php') ?>">Agregar producto →</a>
            <a class="enlace mt-3" href="<?= ruta('admin/productos.php') ?>">Ver todo el catálogo →</a>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../includes/final.php'; ?>
