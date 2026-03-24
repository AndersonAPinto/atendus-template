<?php
// Router para o servidor built-in do PHP
// Uso: php -S localhost:8080 router.php  (dentro da pasta lp/)

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rota da API: /api/lp/{slug}
if (preg_match('#^/api/lp/([a-z0-9\-_]+)$#i', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/api.php';
    exit;
}

// Arquivos estáticos e index.html
return false;
