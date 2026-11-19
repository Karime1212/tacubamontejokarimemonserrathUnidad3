<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

session_save_path(sys_get_temp_dir());
require __DIR__ . '/../includes/inicio.php';

$pruebas = [
    'Imagen local permitida' => fn() => fotovalida('assets/img/donas.jpg'),
    'Imagen subida permitida' => fn() => fotovalida('uploads/productos/' . str_repeat('a', 32) . '.jpg'),
    'Rechazar scripts' => fn() => !fotovalida('uploads/productos/prueba.php'),
    'Rechazar rutas arbitrarias' => fn() => !fotovalida('../../config/correo.php'),
    'Rechazar JavaScript como imagen' => fn() => !fotovalida('javascript:alert(1)'),
    'Rechazar contraseña sin símbolo' => fn() => !clavevalida('Clave2026'),
    'Rechazar contraseña sin número' => fn() => !clavevalida('UnaClave#'),
    'Rechazar caracteres de control' => fn() => !clavevalida("Clave#2026\n"),
    'Aceptar contraseña válida' => fn() => clavevalida('UnaClave#2026'),
    'Contar caracteres UTF-8' => fn() => longitud('café') === 4,
    'Escapar contenido HTML' => fn() => escapar('<script>') === '&lt;script&gt;',
    'PHPMailer disponible' => fn() => class_exists(PHPMailer\PHPMailer\PHPMailer::class),
];

$fallos = 0;

foreach ($pruebas as $nombre => $prueba) {
    $correcta = (bool) $prueba();
    echo ($correcta ? 'PASS ' : 'FAIL ') . $nombre . PHP_EOL;
    if (!$correcta) {
        $fallos++;
    }
}

exit($fallos ? 1 : 0);
