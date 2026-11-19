<?php
require __DIR__ . '/../includes/inicio.php';
exigir(true);
$catalogo = new Catalogo(conexion());
$categorias = $catalogo->categorias();
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$producto = $id ? $catalogo->buscar($id) : null;

if ($id === false || ($id && !$producto)) {
    http_response_code(404);
    exit('No encontramos ese producto.');
}

$datos = $producto ?? ['nombre' => '', 'descripcion' => '', 'categoriaid' => '', 'precio' => '', 'activo' => 1, 'destacado' => 0];
$error = '';

if (publicar()) {
    comprobar();
    $datos = [
        'nombre' => trim(dato('nombre')),
        'descripcion' => trim(dato('descripcion')),
        'categoriaid' => (int) dato('categoriaid'),
        'precio' => str_replace(',', '.', trim(dato('precio'))),
        'activo' => dato('activo') === '1' ? 1 : 0,
        'destacado' => dato('destacado') === '1' ? 1 : 0,
    ];
    $nuevafoto = '';

    try {
        $permitidas = array_map('intval', array_column($categorias, 'id'));

        if (longitud($datos['nombre']) < 2 || longitud($datos['nombre']) > 100
            || longitud($datos['descripcion']) < 10 || longitud($datos['descripcion']) > 2000
            || !in_array($datos['categoriaid'], $permitidas, true)
            || !preg_match('/^\d{1,5}(\.\d{1,2})?$/D', $datos['precio'])
            || (float) $datos['precio'] <= 0) {
            throw new InvalidArgumentException('Revisa los campos. Usa un precio mayor a cero, hasta 99999.99.');
        }

        $categoria = array_values(array_filter($categorias, fn ($fila) => (int) $fila['id'] === $datos['categoriaid']))[0];
        $defecto = $categoria['slug'] === 'bebidas' ? 'cafe' : $categoria['slug'];
        $datos['foto'] = $producto['foto'] ?? 'assets/img/' . $defecto . '.jpg';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $nuevafoto = subirfoto($_FILES['foto']);
            $datos['foto'] = $nuevafoto;
        }

        $catalogo->guardar((int) $id, $datos);
        aviso($id ? 'Producto actualizado.' : 'Nuevo producto agregado.');
        redirigir('admin/productos.php');
    } catch (Throwable $fallo) {
        if ($nuevafoto) {
            @unlink(__DIR__ . '/../' . $nuevafoto);
        }
        if ($fallo instanceof InvalidArgumentException) {
            $error = $fallo->getMessage();
        } else {
            throw $fallo;
        }
    }
}

$titulo = $id ? 'Editar producto' : 'Agregar producto';
require __DIR__ . '/../includes/panel.php';
?>
<section class="card tarjetapanel editor">
    <?php if ($error): ?>
    <div class="alert alert-danger" role="alert"><?= escapar($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <?php campo(); ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <label for="nombre">Nombre del producto</label>
                <input
                    class="form-control"
                    id="nombre"
                    name="nombre"
                    required
                    minlength="2"
                    maxlength="100"
                    value="<?= escapar($datos['nombre']) ?>"
                />
                <label for="categoriaid">Categoría</label>
                <select class="form-select" id="categoriaid" name="categoriaid" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" <?= (int) $datos['categoriaid'] === (int) $categoria['id'] ? 'selected' : '' ?>><?= escapar($categoria['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="precio">Precio en pesos mexicanos</label>
                <input
                    class="form-control"
                    type="number"
                    id="precio"
                    name="precio"
                    min="0.01"
                    max="99999.99"
                    step="0.01"
                    required
                    value="<?= escapar($datos['precio']) ?>"
                />
                <label for="descripcion">Descripción e indicación de porción</label>
                <textarea
                    class="form-control"
                    id="descripcion"
                    name="descripcion"
                    rows="5"
                    minlength="10"
                    maxlength="2000"
                    required
                >
<?= escapar($datos['descripcion']) ?></textarea>
                <div class="form-check mt-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="activo"
                        name="activo"
                        value="1"
                        <?= $datos['activo'] ? 'checked' : '' ?>
                    />
                    <label class="form-check-label" for="activo">
                        Mostrar en el catálogo público
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="destacado"
                        name="destacado"
                        value="1"
                        <?= $datos['destacado'] ? 'checked' : '' ?>
                    />
                    <label class="form-check-label" for="destacado">
                        Destacar al inicio del menú
                    </label>
                </div>
            </div>
            <div class="col-lg-5">
                <label for="foto">Fotografía del producto</label>
                <input
                    class="form-control"
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                />
                <small>
                    JPG, PNG o WebP; máximo 3 MB y 12 megapíxeles. Si no subes una foto, se conserva
                    la actual o la imagen de la categoría.
                </small>
                <img
                    class="fotoprevia"
                    id="fotoprevia"
                    alt="Vista previa de la fotografía"
                    <?= $producto && fotovalida($producto['foto']) && $producto['foto'] ? 'src="' . escapar(ruta($producto['foto'])) . '"' : 'hidden' ?>
                />
            </div>
        </div>
        <div class="d-flex gap-3 align-items-center mt-4">
            <button class="boton">Guardar producto</button>
            <a class="enlace" href="<?= ruta('admin/productos.php') ?>">Cancelar</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../includes/final.php'; ?>
