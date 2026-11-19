<?php
require __DIR__ . '/../includes/inicio.php';
exigir(true);
$catalogo = new Catalogo(conexion());
$productos = $catalogo->listar('', '', true);
$titulo = 'Productos';
require __DIR__ . '/../includes/panel.php';
?>
<div class="cabecerapanel">
    <p class="discreto">Administra las cinco categorías y sus precios.</p>
    <a class="boton" href="<?= ruta('admin/editar.php') ?>">Agregar producto +</a>
</div>
<section class="card tarjetapanel">
    <label class="visualmente" for="filtrarpanel">Buscar en productos</label>
    <input
        class="form-control mb-3"
        type="search"
        id="filtrarpanel"
        placeholder="Buscar por nombre o categoría…"
    />
    <div class="table-responsive">
        <table class="table align-middle tablados">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr data-fila>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img class="miniatura" src="<?= escapar(ruta($producto['foto'] && fotovalida($producto['foto']) ? $producto['foto'] : 'assets/img/pasteles.jpg')) ?>" alt="" />
                            <strong><?= escapar($producto['nombre']) ?></strong>
                        </div>
                    </td>
                    <td><?= escapar($producto['categoria']) ?></td>
                    <td><?= dinero($producto['precio']) ?></td>
                    <td><span class="insignia"><?= $producto['activo'] ? 'Disponible' : 'Oculto' ?></span></td>
                    <td>
                        <div class="acciones">
                            <a href="<?= ruta('admin/editar.php?id=' . $producto['id']) ?>">Editar</a>
                            <form method="post" action="<?= ruta('admin/eliminar.php') ?>">
                                <?php campo(); ?>
                                <input type="hidden" name="id" value="<?= $producto['id'] ?>" />
                                <button
                                    class="textopeligro"
                                    data-confirmar="¿Eliminar este producto y sus favoritos? Esta acción no se puede deshacer."
                                >
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="discreto" id="sinfilas" hidden>No hay coincidencias.</p>
</section>
<?php require __DIR__ . '/../includes/final.php'; ?>
