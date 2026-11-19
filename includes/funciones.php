<?php

function escapar($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function longitud(string $texto): int
{
    return (int) preg_match_all('/./us', $texto);
}

function dato(string $nombre, ?array $origen = null): string
{
    if ($origen === null) {
        $origen = $_POST;
    }

    return isset($origen[$nombre]) && is_string($origen[$nombre]) ? $origen[$nombre] : '';
}

function ruta(string $archivo = ''): string
{
    $directorio = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));

    if (in_array(basename($directorio), ['admin', 'api'], true)) {
        $directorio = dirname($directorio);
    }

    $partes = array_filter(explode('/', trim($directorio, '/')));
    $partes = array_map(fn($parte) => rawurlencode(rawurldecode($parte)), $partes);
    $base = $partes ? '/' . implode('/', $partes) : '';

    return $base . '/' . ltrim($archivo, '/');
}

function redirigir(string $archivo): void
{
    header('Location: ' . ruta($archivo));
    exit();
}

function consulta(string $sql, array $parametros = []): PDOStatement
{
    $sentencia = conexion()->prepare($sql);
    $sentencia->execute($parametros);

    return $sentencia;
}

function usuario(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigir(bool $administrador = false): void
{
    if (!usuario()) {
        redirigir($administrador ? 'admin/ingreso.php' : 'ingreso.php');
    }

    if ($administrador && usuario()['rol'] !== 'admin') {
        http_response_code(403);
        exit('Acceso reservado al administrador.');
    }
}

function publicar(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function token(): string
{
    return $_SESSION['token'] ??= bin2hex(random_bytes(32));
}

function campo(): void
{
    echo '<input type="hidden" name="token" value="' . escapar(token()) . '">';
}

function comprobar(): void
{
    $recibido = dato('token');

    if (!hash_equals(token(), $recibido)) {
        http_response_code(403);
        exit('Solicitud no válida. Recarga la página e inténtalo otra vez.');
    }
}

function aviso(string $mensaje, string $tipo = 'success'): void
{
    $_SESSION['aviso'] = ['mensaje' => $mensaje, 'tipo' => $tipo];
}

function dinero($cantidad): string
{
    return '$' . number_format((float) $cantidad, 2) . ' MXN';
}

function respuesta(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function clavevalida(string $clave): bool
{
    return longitud($clave) >= 8 &&
        strlen($clave) <= 72 &&
        preg_match('/[0-9]/', $clave) &&
        preg_match('/[^a-zA-Z0-9]/', $clave) &&
        !preg_match('/[\x00-\x1F\x7F]/', $clave);
}

function fotovalida(string $foto): bool
{
    return $foto === '' ||
        (bool) preg_match(
            '~^(assets/img/[a-z0-9]+\.(jpg|png|webp)|uploads/productos/[a-f0-9]{32}\.jpg)$~D',
            $foto
        );
}

function subirfoto(array $archivo): string
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('No se pudo subir la fotografía.');
    }

    if ($archivo['size'] > 3 * 1024 * 1024 || !is_uploaded_file($archivo['tmp_name'])) {
        throw new InvalidArgumentException('La fotografía debe pesar como máximo 3 MB.');
    }

    $informacion = new finfo(FILEINFO_MIME_TYPE);
    $tipo = $informacion->file($archivo['tmp_name']);
    $medidas = @getimagesize($archivo['tmp_name']);
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($tipo, $permitidos, true) || !$medidas || $medidas[0] * $medidas[1] > 12000000) {
        throw new InvalidArgumentException('Usa JPG, PNG o WebP de hasta 12 megapíxeles.');
    }

    if (!function_exists('imagecreatefromstring')) {
        throw new InvalidArgumentException('Activa la extensión GD en PHP para subir fotos.');
    }

    $imagen = @imagecreatefromstring(file_get_contents($archivo['tmp_name']));

    if (!$imagen) {
        throw new InvalidArgumentException('La imagen no es válida.');
    }

    $escala = min(1, 1600 / max($medidas[0], $medidas[1]));
    $ancho = max(1, (int) ($medidas[0] * $escala));
    $alto = max(1, (int) ($medidas[1] * $escala));
    $nueva = imagecreatetruecolor($ancho, $alto);
    imagefill($nueva, 0, 0, imagecolorallocate($nueva, 255, 255, 255));
    imagecopyresampled($nueva, $imagen, 0, 0, 0, 0, $ancho, $alto, $medidas[0], $medidas[1]);
    $nombre = bin2hex(random_bytes(16)) . '.jpg';
    $destino = __DIR__ . '/../uploads/productos/' . $nombre;
    $guardada = imagejpeg($nueva, $destino, 85);
    imagedestroy($imagen);
    imagedestroy($nueva);

    if (!$guardada) {
        throw new RuntimeException('No se pudo guardar la fotografía.');
    }

    return 'uploads/productos/' . $nombre;
}
