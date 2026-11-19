<?php
require __DIR__ . '/includes/inicio.php';
$catalogo = new Catalogo(conexion());
$categorias = $catalogo->categorias();
$seleccion = trim(dato('categoria', $_GET));
$titulo = 'Catálogo';
require __DIR__ . '/includes/cabecera.php';
?>
<section class="portada container">
    <div class="row align-items-center g-5">
        <div class="col-lg-6 revelar">
            <h1>La Dulce Esquina</h1>
            <p>Donas, pays, pasteles, galletas y bebidas.</p>
            <a class="boton" href="#catalogo">Ver productos</a>
        </div>
        <div class="col-lg-6 revelar">
            <div class="escaparate">
                <div
                    id="carrusel"
                    class="carousel slide carousel-fade"
                    data-bs-ride="carousel"
                    data-bs-interval="5000"
                    aria-label="Nuestros antojos"
                >
                    <div class="carousel-indicators">
                        <button
                            type="button"
                            data-bs-target="#carrusel"
                            data-bs-slide-to="0"
                            class="active"
                            aria-current="true"
                            aria-label="Pasteles"
                        ></button>
                        <button
                            type="button"
                            data-bs-target="#carrusel"
                            data-bs-slide-to="1"
                            aria-label="Donas"
                        ></button>
                        <button
                            type="button"
                            data-bs-target="#carrusel"
                            data-bs-slide-to="2"
                            aria-label="Café"
                        ></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img
                                src="<?= ruta('assets/img/pasteles.jpg') ?>"
                                alt="Pastel decorado, fotografía de demostración"
                                width="1200"
                                height="1000"
                            />
                        </div>
                        <div class="carousel-item">
                            <img
                                src="<?= ruta('assets/img/donas.jpg') ?>"
                                alt="Donas, fotografía de demostración"
                                width="1200"
                                height="1000"
                            />
                        </div>
                        <div class="carousel-item">
                            <img
                                src="<?= ruta('assets/img/cafe.jpg') ?>"
                                alt="Una pausa con café"
                                width="1200"
                                height="1000"
                            />
                        </div>
                    </div>
                    <button
                        class="carousel-control-prev"
                        type="button"
                        data-bs-target="#carrusel"
                        data-bs-slide="prev"
                        aria-label="Imagen anterior"
                    >
                        <span aria-hidden="true">←</span>
                    </button>
                    <button
                        class="carousel-control-next"
                        type="button"
                        data-bs-target="#carrusel"
                        data-bs-slide="next"
                        aria-label="Imagen siguiente"
                    >
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
                <button class="pausa" type="button" id="pausa" aria-pressed="false">
                    Pausar carrusel
                </button>
            </div>
        </div>
    </div>
</section>
<section class="seccion container" id="catalogo">
    <div class="titular revelar">
        <div>
            <h2>Productos</h2>
            <p>Busca un producto o selecciona una categoría.</p>
        </div>
        <span class="contador" id="resultados" aria-live="polite">Cargando menú…</span>
    </div>
    <div class="categorias" id="categorias" aria-label="Categorías del menú">
        <button
            class="categoria <?= $seleccion === '' ? 'activa' : '' ?>"
            type="button"
            data-categoria=""
            aria-pressed="<?= $seleccion === '' ? 'true' : 'false' ?>"
        >
            Todo el menú
        </button>
        <?php foreach ($categorias as $categoria): ?>
        <button
            class="categoria <?= $seleccion === $categoria['slug'] ? 'activa' : '' ?>"
            type="button"
            data-categoria="<?= escapar($categoria['slug']) ?>"
            aria-pressed="<?= $seleccion === $categoria['slug'] ? 'true' : 'false' ?>"
        >
            <?= escapar($categoria['nombre']) ?>
        </button>
        <?php endforeach; ?>
    </div>
    <form class="buscador" id="buscador" role="search">
        <label class="visualmente" for="buscar">Buscar un producto</label>
        <input
            type="search"
            id="buscar"
            placeholder="Buscar producto…"
            maxlength="80"
            autocomplete="off"
        />
        <button class="boton secundario" type="reset">Limpiar filtros</button>
    </form>
    <div
        class="productos"
        id="productos"
        data-categoria="<?= escapar($seleccion) ?>"
        aria-live="polite"
        aria-busy="true"
    >
        <div class="esqueleto"></div>
        <div class="esqueleto"></div>
        <div class="esqueleto"></div>
    </div>
    <noscript><p>Activa JavaScript para explorar el menú interactivo.</p></noscript>
</section>
<?php require __DIR__ . '/includes/pie.php'; ?>
