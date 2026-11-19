USE `db_crud_pdo`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS dulceusuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(190) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    correostatus ENUM('pendiente', 'enviado', 'error', 'desactivado') NOT NULL DEFAULT 'pendiente',
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dulcecategorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(40) NOT NULL,
    slug VARCHAR(30) NOT NULL UNIQUE,
    orden TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dulceproductos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoriaid INT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    foto VARCHAR(255) NOT NULL DEFAULT '',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT dulcecategoria FOREIGN KEY (categoriaid) REFERENCES dulcecategorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dulcefavoritos (
    usuarioid INT UNSIGNED NOT NULL,
    productoid INT UNSIGNED NOT NULL,
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuarioid, productoid),
    CONSTRAINT dulcepersona FOREIGN KEY (usuarioid) REFERENCES dulceusuarios(id) ON DELETE CASCADE,
    CONSTRAINT dulceproducto FOREIGN KEY (productoid) REFERENCES dulceproductos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dulceintentos (
    llave CHAR(64) PRIMARY KEY,
    intentos INT UNSIGNED NOT NULL DEFAULT 0,
    inicio INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO dulcecategorias (nombre, slug, orden)
SELECT 'Donas', 'donas', 1
WHERE NOT EXISTS (SELECT id FROM dulcecategorias WHERE slug = 'donas');

INSERT INTO dulcecategorias (nombre, slug, orden)
SELECT 'Pays', 'pays', 2
WHERE NOT EXISTS (SELECT id FROM dulcecategorias WHERE slug = 'pays');

INSERT INTO dulcecategorias (nombre, slug, orden)
SELECT 'Pasteles', 'pasteles', 3
WHERE NOT EXISTS (SELECT id FROM dulcecategorias WHERE slug = 'pasteles');

INSERT INTO dulcecategorias (nombre, slug, orden)
SELECT 'Galletas', 'galletas', 4
WHERE NOT EXISTS (SELECT id FROM dulcecategorias WHERE slug = 'galletas');

INSERT INTO dulcecategorias (nombre, slug, orden)
SELECT 'Bebidas', 'bebidas', 5
WHERE NOT EXISTS (SELECT id FROM dulcecategorias WHERE slug = 'bebidas');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'donaglaseada', 'Dona glaseada', 'Una clásica y suave dona con glaseado de azúcar. Precio por pieza.', 28.00, 'assets/img/donas.jpg', 1
FROM dulcecategorias
WHERE slug = 'donas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'donaglaseada');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'donachocolate', 'Dona de chocolate', 'Cobertura de chocolate para una pausa llena de antojo. Precio por pieza.', 32.00, 'assets/img/donas.jpg', 0
FROM dulcecategorias
WHERE slug = 'donas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'donachocolate');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'donarellena', 'Dona rellena', 'Dona esponjosa con relleno de crema. Precio por pieza.', 38.00, 'assets/img/donas.jpg', 0
FROM dulcecategorias
WHERE slug = 'donas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'donarellena');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'paylimon', 'Pay de limón', 'Una porción cremosa de limón con base de galleta. Precio por rebanada.', 55.00, 'assets/img/pays.jpg', 1
FROM dulcecategorias
WHERE slug = 'pays' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'paylimon');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'payqueso', 'Pay de queso', 'Suave y cremoso, para acompañar un café. Precio por rebanada.', 60.00, 'assets/img/pays.jpg', 0
FROM dulcecategorias
WHERE slug = 'pays' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'payqueso');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'paymanzana', 'Pay de manzana', 'Manzana y un toque de canela. Precio por rebanada.', 58.00, 'assets/img/pays.jpg', 0
FROM dulcecategorias
WHERE slug = 'pays' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'paymanzana');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'pastelchocolate', 'Pastel de chocolate', 'Una rebanada para quienes siempre eligen chocolate. Precio por rebanada.', 75.00, 'assets/img/pasteles.jpg', 1
FROM dulcecategorias
WHERE slug = 'pasteles' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'pastelchocolate');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'pasteltresleches', 'Pastel tres leches', 'Esponjoso, húmedo y con un toque de vainilla. Precio por rebanada.', 70.00, 'assets/img/pasteles.jpg', 0
FROM dulcecategorias
WHERE slug = 'pasteles' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'pasteltresleches');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'pastelvainilla', 'Pastel de vainilla', 'Un clásico delicado para compartir un bonito momento. Precio por rebanada.', 65.00, 'assets/img/pasteles.jpg', 0
FROM dulcecategorias
WHERE slug = 'pasteles' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'pastelvainilla');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'galletachispas', 'Galleta de chispas', 'Galleta con chispas de chocolate y textura casera. Precio por pieza.', 25.00, 'assets/img/galletas.jpg', 1
FROM dulcecategorias
WHERE slug = 'galletas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'galletachispas');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'galletaavena', 'Galleta de avena', 'Una opción con avena para acompañar tu bebida. Precio por pieza.', 22.00, 'assets/img/galletas.jpg', 0
FROM dulcecategorias
WHERE slug = 'galletas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'galletaavena');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'galletamantequilla', 'Galleta de mantequilla', 'Delicada, crujiente y con un toque de mantequilla. Precio por pieza.', 20.00, 'assets/img/galletas.jpg', 0
FROM dulcecategorias
WHERE slug = 'galletas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'galletamantequilla');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'cafeamericano', 'Café americano', 'Café caliente para disfrutar con tu postre favorito. Vaso de demostración.', 35.00, 'assets/img/cafe.jpg', 0
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'cafeamericano');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'capuchino', 'Capuchino', 'Café con leche y una suave capa de espuma. Vaso de demostración.', 48.00, 'assets/img/cafe.jpg', 1
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'capuchino');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'latte', 'Latte', 'Una mezcla suave de café y leche. Vaso de demostración.', 50.00, 'assets/img/cafe.jpg', 0
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'latte');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'malteadachocolate', 'Malteada de chocolate', 'Una bebida cremosa y fría para consentirte. Vaso de demostración.', 65.00, 'assets/img/malteadas.jpg', 1
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'malteadachocolate');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'malteadavainilla', 'Malteada de vainilla', 'Suave, fresca y con sabor a vainilla. Vaso de demostración.', 60.00, 'assets/img/malteadas.jpg', 0
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'malteadavainilla');

INSERT INTO dulceproductos (categoriaid, codigo, nombre, descripcion, precio, foto, destacado)
SELECT id, 'malteadafresa', 'Malteada de fresa', 'Un antojo frío con sabor a fresa. Vaso de demostración.', 65.00, 'assets/img/malteadas.jpg', 0
FROM dulcecategorias
WHERE slug = 'bebidas' AND NOT EXISTS (SELECT id FROM dulceproductos WHERE codigo = 'malteadafresa');

