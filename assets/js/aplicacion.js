'use strict';

const buscar = (selector, origen = document) => origen.querySelector(selector);
const todos = (selector, origen = document) => [...origen.querySelectorAll(selector)];
const base = document.body.dataset.base || '';
const ruta = (archivo) => `${base}/${archivo}`;
const movimiento = window.matchMedia('(prefers-reduced-motion: reduce)');

function elemento(etiqueta, texto = '', clase = '') {
    const nodo = document.createElement(etiqueta);
    nodo.textContent = texto;
    if (clase) nodo.className = clase;
    return nodo;
}

function avisar(mensaje) {
    const region = buscar('#avisos');
    if (!region) return;
    region.replaceChildren(elemento('div', mensaje, 'mensaje'));
    window.setTimeout(() => region.replaceChildren(), 4500);
}

function fotovalida(foto) {
    return (
        typeof foto === 'string' &&
        /^(assets\/img\/[a-z0-9]+\.(jpg|png|webp)|uploads\/productos\/[a-f0-9]{32}\.jpg)$/.test(
            foto,
        )
    );
}


function precio(valor) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(Number(valor));
}

async function obtener(url, opciones = {}) {
    const controlador = new AbortController();
    const tiempo = window.setTimeout(() => controlador.abort(), 12000);
    try {
        const respuesta = await fetch(url, {
            ...opciones,
            signal: opciones.signal || controlador.signal,
        });
        let datos;
        try {
            datos = await respuesta.json();
        } catch {
            throw new Error(
                'No pudimos completar la consulta. Revisa la conexión o vuelve a intentarlo.',
            );
        }
        if (!respuesta.ok) {
            const fallo = new Error(datos.error || 'No pudimos completar la operación.');
            fallo.codigo = respuesta.status;
            throw fallo;
        }
        return datos;
    } finally {
        window.clearTimeout(tiempo);
    }
}

function fotografia(producto) {
    const contenedor = elemento('div', '', 'fotoproducto');
    const imagen = new Image();
    imagen.src = ruta(fotovalida(producto.foto) ? producto.foto : 'assets/img/pasteles.jpg');
    imagen.alt = producto.nombre;
    imagen.loading = 'lazy';
    imagen.width = 1200;
    imagen.height = 1000;
    imagen.addEventListener(
        'error',
        () => {
            imagen.remove();
            contenedor.append(elemento('span', 'Fotografía no disponible', 'discreto'));
        },
        { once: true },
    );
    contenedor.append(imagen);
    return contenedor;
}

function botonfavorito(producto) {
    const boton = elemento('button', producto.favorito ? '♥' : '♡', 'favorito');
    boton.type = 'button';
    boton.dataset.favorito = producto.id;
    boton.setAttribute('aria-pressed', String(Boolean(producto.favorito)));
    boton.setAttribute(
        'aria-label',
        `${producto.favorito ? 'Quitar' : 'Guardar'} ${producto.nombre} de favoritos`,
    );
    return boton;
}

function abrirvista(producto) {
    const ventana = buscar('#vistaprevia');
    const contenido = buscar('#detalleprevio');
    if (!ventana || !contenido) return;
    contenido.replaceChildren(fotografia(producto));
    const cuerpo = elemento('div', '', 'cuerpovista');
    cuerpo.append(
        elemento('span', producto.categoria, 'etiqueta'),
        elemento('h2', producto.nombre),
        elemento('p', producto.descripcion),
        elemento('strong', `${precio(producto.precio)} MXN`, 'precio'),
    );
    const enlace = elemento('a', 'Ver producto →', 'boton');
    enlace.href = ruta(`producto.php?id=${encodeURIComponent(producto.id)}`);
    cuerpo.append(enlace);
    contenido.append(cuerpo);
    ventana.showModal();
}

function tarjeta(producto) {
    const articulo = elemento('article', '', 'card producto');
    const foto = fotografia(producto);
    foto.append(botonfavorito(producto));
    const cuerpo = elemento('div', '', 'cuerpoproducto');
    cuerpo.append(
        elemento('span', producto.categoria, 'etiqueta'),
        elemento('h3', producto.nombre),
        elemento('p', producto.descripcion, 'descripcion'),
    );
    const linea = elemento('div', '', 'precioproducto');
    const enlace = elemento('a', 'Ver detalle →', 'enlace');
    enlace.href = ruta(`producto.php?id=${encodeURIComponent(producto.id)}`);
    linea.append(elemento('span', precio(producto.precio), 'precio'), enlace);
    const rapido = elemento('button', 'Vista rápida', 'vistaboton');
    rapido.type = 'button';
    rapido.addEventListener('click', () => abrirvista(producto));
    cuerpo.append(linea, rapido);
    articulo.append(foto, cuerpo);
    return articulo;
}

function vacio(contenedor, titulo, mensaje) {
    const caja = elemento('div', '', 'vacio');
    caja.append(
        elemento('span', '♡', 'vacioicono'),
        elemento('h3', titulo),
        elemento('p', mensaje),
    );
    const enlace = elemento('a', 'Explorar el menú →', 'boton secundario');
    enlace.href = ruta('index.php#catalogo');
    caja.append(enlace);
    contenedor.replaceChildren(caja);
}

let categoria = buscar('#productos')?.dataset.categoria || '';
let controlador;
let serie = 0;

async function cargarmenu() {
    const contenedor = buscar('#productos');
    if (!contenedor) return;
    controlador?.abort();
    controlador = new AbortController();
    const actual = ++serie;
    contenedor.setAttribute('aria-busy', 'true');
    buscar('#resultados').textContent = 'Buscando tu antojo…';
    const parametros = new URLSearchParams({ categoria, buscar: buscar('#buscar').value.trim() });
    try {
        const datos = await obtener(ruta(`api/productos.php?${parametros}`), {
            signal: controlador.signal,
        });
        if (actual !== serie) return;
        contenedor.replaceChildren(...datos.datos.map(tarjeta));
        buscar('#resultados').textContent =
            `${datos.total} ${datos.total === 1 ? 'antojo disponible' : 'antojos disponibles'}`;
        if (!datos.total)
            vacio(
                contenedor,
                'Este antojo aún no aparece.',
                'Prueba otra palabra o limpia los filtros.',
            );
    } catch (fallo) {
        if (fallo.name === 'AbortError') return;
        if (actual !== serie) return;
        buscar('#resultados').textContent = 'No disponible';
        vacio(
            contenedor,
            'No pudimos cargar el menú.',
            'Comprueba tu conexión y que MySQL esté iniciado.',
        );
        const reintento = elemento('button', 'Volver a intentar', 'boton mt-3');
        reintento.addEventListener('click', cargarmenu);
        buscar('.vacio', contenedor).append(reintento);
    } finally {
        if (actual === serie) contenedor.setAttribute('aria-busy', 'false');
    }
}

if (buscar('#productos')) {
    cargarmenu();
    todos('[data-categoria]')
        .filter((nodo) => nodo.tagName === 'BUTTON')
        .forEach((boton) => {
            boton.addEventListener('click', () => {
                categoria = boton.dataset.categoria;
                todos('#categorias button').forEach((otro) => {
                    const activo = otro === boton;
                    otro.classList.toggle('activa', activo);
                    otro.setAttribute('aria-pressed', String(activo));
                });
                cargarmenu();
            });
        });
    let demora;
    buscar('#buscar').addEventListener('input', () => {
        clearTimeout(demora);
        demora = setTimeout(cargarmenu, 220);
    });
    buscar('#buscador').addEventListener('submit', (evento) => {
        evento.preventDefault();
        cargarmenu();
    });
    buscar('#buscador').addEventListener('reset', () => {
        categoria = '';
        todos('#categorias button').forEach((boton) => {
            const activo = boton.dataset.categoria === '';
            boton.classList.toggle('activa', activo);
            boton.setAttribute('aria-pressed', String(activo));
        });
        setTimeout(cargarmenu, 0);
    });
}

async function cargarfavoritos() {
    const contenedor = buscar('#favoritos');
    if (!contenedor) return;
    try {
        const datos = await obtener(ruta('api/favoritos.php'));
        contenedor.replaceChildren(...datos.datos.map(tarjeta));
        if (!datos.total)
            vacio(
                contenedor,
                'Aquí empieza tu lista de antojos.',
                'Toca el corazón de cualquier producto para guardarlo.',
            );
    } catch (fallo) {
        if (fallo.codigo === 401) {
            window.location.href = ruta('ingreso.php');
            return;
        }
        vacio(contenedor, 'No pudimos consultar tus favoritos.', 'Intenta de nuevo en un momento.');
    } finally {
        contenedor.setAttribute('aria-busy', 'false');
    }
}

if (buscar('#favoritos')) cargarfavoritos();

async function guardarfavorito(boton) {
    boton.disabled = true;
    const guardado = boton.getAttribute('aria-pressed') === 'true';
    try {
        const resultado = await obtener(ruta('api/favoritos.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': buscar('meta[name="token"]').content,
            },
            body: JSON.stringify({
                id: Number(boton.dataset.favorito),
                accion: guardado ? 'quitar' : 'agregar',
            }),
        });
        todos('[data-favorito]')
            .filter((otro) => otro.dataset.favorito === boton.dataset.favorito)
            .forEach((otro) => {
                otro.setAttribute('aria-pressed', String(resultado.guardado));
                otro.textContent = otro.classList.contains('favorito')
                    ? resultado.guardado
                        ? '♥'
                        : '♡'
                    : resultado.guardado
                      ? '♥ Guardado en favoritos'
                      : '♡ Guardar en favoritos';
            });
        avisar(
            resultado.guardado
                ? 'Un dulce antojo guardado en tus favoritos ♡'
                : 'Producto retirado de tus favoritos.',
        );
        if (buscar('#favoritos')) await cargarfavoritos();
    } catch (fallo) {
        if (fallo.codigo === 401) {
            window.location.href = ruta('ingreso.php');
            return;
        }
        avisar(fallo.message);
    } finally {
        boton.disabled = false;
    }
}

document.addEventListener('click', (evento) => {
    const boton = evento.target.closest('[data-favorito]');
    if (boton && !boton.disabled) guardarfavorito(boton);
});

const favoritodetalle = buscar('.detalle [data-favorito]');
if (favoritodetalle) {
    obtener(ruta('api/favoritos.php'))
        .then((datos) => {
            if (
                datos.datos.some(
                    (producto) => Number(producto.id) === Number(favoritodetalle.dataset.favorito),
                )
            ) {
                favoritodetalle.setAttribute('aria-pressed', 'true');
                favoritodetalle.textContent = '♥ Guardado en favoritos';
            }
        })
        .catch(() => {});
}

buscar('#cerrar')?.addEventListener('click', () => buscar('#vistaprevia').close());
buscar('#vistaprevia')?.addEventListener('click', (evento) => {
    if (evento.target !== evento.currentTarget) return;
    const limites = evento.currentTarget.getBoundingClientRect();
    if (
        evento.clientX < limites.left ||
        evento.clientX > limites.right ||
        evento.clientY < limites.top ||
        evento.clientY > limites.bottom
    ) {
        evento.currentTarget.close();
    }
});

todos('input[type="password"]').forEach((entrada) => {
    const grupo = elemento('div', '', 'campoclave');
    entrada.parentNode.insertBefore(grupo, entrada);
    grupo.append(entrada);
    const boton = elemento('button', 'Ver', 'verclave');
    boton.type = 'button';
    boton.setAttribute('aria-label', 'Mostrar contraseña');
    boton.setAttribute('aria-pressed', 'false');
    boton.addEventListener('click', () => {
        const visible = entrada.type === 'password';
        entrada.type = visible ? 'text' : 'password';
        boton.textContent = visible ? 'Ocultar' : 'Ver';
        boton.setAttribute('aria-pressed', String(visible));
        boton.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
    grupo.append(boton);
});

const foto = buscar('#foto');
let fototemporal;
foto?.addEventListener('change', () => {
    const archivo = foto.files[0];
    const previa = buscar('#fotoprevia');
    if (fototemporal) URL.revokeObjectURL(fototemporal);
    if (!archivo) return;
    if (
        !['image/jpeg', 'image/png', 'image/webp'].includes(archivo.type) ||
        archivo.size > 3 * 1024 * 1024
    ) {
        avisar('Elige una fotografía JPG, PNG o WebP de hasta 3 MB.');
        foto.value = '';
        return;
    }
    fototemporal = URL.createObjectURL(archivo);
    previa.src = fototemporal;
    previa.hidden = false;
});

todos('[data-confirmar]').forEach((boton) =>
    boton.addEventListener('click', (evento) => {
        if (!window.confirm(boton.dataset.confirmar)) evento.preventDefault();
    }),
);

buscar('#filtrarpanel')?.addEventListener('input', (evento) => {
    const texto = evento.target.value.trim().toLocaleLowerCase('es');
    let visibles = 0;
    todos('[data-fila]').forEach((fila) => {
        fila.hidden = !fila.textContent.toLocaleLowerCase('es').includes(texto);
        if (!fila.hidden) visibles++;
    });
    buscar('#sinfilas').hidden = visibles > 0;
});

const carrusel = buscar('#carrusel');
if (carrusel && window.bootstrap) {
    const instancia = bootstrap.Carousel.getOrCreateInstance(carrusel);
    const boton = buscar('#pausa');
    let pausado = movimiento.matches;
    const actualizar = () => {
        if (pausado || document.hidden) instancia.pause();
        else instancia.cycle();
        boton.textContent = pausado ? 'Reanudar carrusel' : 'Pausar carrusel';
        boton.setAttribute('aria-pressed', String(pausado));
    };
    boton.addEventListener('click', () => {
        pausado = !pausado;
        actualizar();
    });
    movimiento.addEventListener('change', () => {
        pausado = movimiento.matches;
        actualizar();
    });
    document.addEventListener('visibilitychange', actualizar);
    actualizar();
}

if (!movimiento.matches && 'IntersectionObserver' in window) {
    const observador = new IntersectionObserver(
        (entradas) => {
            entradas.forEach((entrada) => {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('visible');
                    observador.unobserve(entrada.target);
                }
            });
        },
        { threshold: 0.1 },
    );
    todos('.revelar').forEach((nodo) => {
        nodo.classList.add('espera');
        observador.observe(nodo);
    });
}

const arriba = buscar('#arriba');
window.addEventListener(
    'scroll',
    () => {
        if (arriba) arriba.hidden = window.scrollY < 600;
        buscar('.encabezado')?.classList.toggle('sombra', window.scrollY > 20);
    },
    { passive: true },
);
arriba?.addEventListener('click', () =>
    window.scrollTo({ top: 0, behavior: movimiento.matches ? 'instant' : 'smooth' }),
);

const navegacion = buscar('#navegacion');
navegacion?.addEventListener('click', (evento) => {
    if (evento.target.closest('a') && navegacion.classList.contains('show') && window.bootstrap) {
        bootstrap.Collapse.getOrCreateInstance(navegacion, { toggle: false }).hide();
    }
});
