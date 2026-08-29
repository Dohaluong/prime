<?php
require_once 'auth.php';
require_admin();
header('Content-Type: application/json; charset=utf-8');

$pdo = db();
$productId = (int)($_POST['product_id'] ?? 0);
$order = json_decode($_POST['order'] ?? '[]', true);
if (!$pdo || !$productId || !is_array($order)) { http_response_code(422); exit; }
$order = array_values(array_unique(array_filter(array_map(fn($url) => trim((string)$url), $order))));
try {
    $owned = $pdo->prepare('SELECT image_url FROM product_images WHERE product_id=?');
    $owned->execute([$productId]);
    $valid = $owned->fetchAll(PDO::FETCH_COLUMN);
    if (count($order) !== count($valid) || array_diff($order, $valid)) throw new RuntimeException('Danh sách ảnh không hợp lệ.');
    $pdo->beginTransaction();
    $save = $pdo->prepare('UPDATE product_images SET sort_order=? WHERE image_url=? AND product_id=?');
    foreach ($order as $position => $imageUrl) $save->execute([$position + 1, $imageUrl, $productId]);
    $pdo->commit();
    echo '{"ok":true}';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(422);
    echo '{"ok":false}';
}
