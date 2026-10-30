<?php

declare(strict_types=1);

$params = json_decode($argv[1] ?? '{}', true);

if (!is_array($params)) {
    fwrite(STDERR, "Parametros invalidos\n");
    exit(1);
}

$_GET = $params;

require dirname(__DIR__) . '/public/index.php';
