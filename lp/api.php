<?php
header('Content-Type: application/json; charset=utf-8');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3307');
define('DB_NAME', getenv('DB_NAME') ?: 'atendus');
define('DB_USER', getenv('DB_USER') ?: 'atendus');
define('DB_PASS', getenv('DB_PASS') ?: 'atendus123');

$slug = preg_replace('/[^a-z0-9\-_]/', '', strtolower($_GET['slug'] ?? ''));

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'slug é obrigatório']);
    exit;
}

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

    $stmt = $pdo->prepare('SELECT * FROM v_landing_page_full WHERE slug = :slug AND active = 1 LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => "slug \"$slug\" não encontrado"]);
        exit;
    }

    foreach (['steps', 'features_items', 'about_values', 'differentials', 'faq_items'] as $col) {
        if (isset($row[$col]) && is_string($row[$col])) {
            $row[$col] = json_decode($row[$col], true) ?: [];
        }
    }

    echo json_encode($row);

} catch (PDOException $e) {
    error_log('[API] DB error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
