<?php


class Catalogo
{
    public function __construct(private PDO $conexion) {}

    public function categorias(): array
    {
        return $this->conexion->query('SELECT * FROM dulcecategorias ORDER BY orden')->fetchAll();
    }

    public function listar(
        string $categoria = '',
        string $busqueda = '',
        bool $todos = false
    ): array {
        $sql = 'SELECT p.*, c.nombre AS categoria, c.slug
                FROM dulceproductos p
                JOIN dulcecategorias c ON c.id = p.categoriaid
                WHERE 1 = 1';
        $parametros = [];

        if (!$todos) {
            $sql .= ' AND p.activo = 1';
        }

        if ($categoria !== '') {
            $sql .= ' AND c.slug = ?';
            $parametros[] = $categoria;
        }

        if ($busqueda !== '') {
            $sql .= ' AND (p.nombre LIKE ? OR p.descripcion LIKE ?)';
            $parametros[] = '%' . $busqueda . '%';
            $parametros[] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY p.destacado DESC, p.id';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return $sentencia->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $sentencia = $this->conexion->prepare(
            'SELECT p.*, c.nombre AS categoria, c.slug
             FROM dulceproductos p
             JOIN dulcecategorias c ON c.id = p.categoriaid
             WHERE p.id = ?'
        );
        $sentencia->execute([$id]);

        return $sentencia->fetch() ?: null;
    }

    public function guardar(int $id, array $producto): int
    {
        $valores = [
            $producto['categoriaid'],
            $producto['nombre'],
            $producto['descripcion'],
            $producto['precio'],
            $producto['foto'],
            $producto['activo'],
            $producto['destacado']
        ];

        if ($id > 0) {
            $sql = 'UPDATE dulceproductos
                    SET categoriaid = ?, nombre = ?, descripcion = ?, precio = ?,
                        foto = ?, activo = ?, destacado = ?
                    WHERE id = ?';
            $valores[] = $id;
        } else {
            $valores[] = bin2hex(random_bytes(12));
            $sql = 'INSERT INTO dulceproductos
                    (categoriaid, nombre, descripcion, precio, foto, activo, destacado, codigo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($valores);

        return $id ?: (int) $this->conexion->lastInsertId();
    }

    public function eliminar(int $id): void
    {
        $sentencia = $this->conexion->prepare('DELETE FROM dulceproductos WHERE id = ?');
        $sentencia->execute([$id]);
    }
}
