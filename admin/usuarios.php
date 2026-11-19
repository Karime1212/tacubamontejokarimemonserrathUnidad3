<?php
require __DIR__ . '/../includes/inicio.php';
exigir(true);
$clientes = consulta("SELECT nombre, correo, creado FROM dulceusuarios WHERE rol = 'cliente' ORDER BY id DESC")->fetchAll();
$titulo = 'Clientes';
require __DIR__ . '/../includes/panel.php';
?>
<p class="discreto">
    Consulta el nombre, el correo y la fecha de registro de tus clientes.
</p>
<section class="card tarjetapanel">
    <div class="table-responsive">
        <table class="table align-middle tablados">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= escapar($cliente['nombre']) ?></td>
                    <td><?= escapar($cliente['correo']) ?></td>
                    <td><?= escapar($cliente['creado']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$clientes): ?>
    <p class="discreto">Todavía no hay clientes registrados.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/final.php'; ?>
