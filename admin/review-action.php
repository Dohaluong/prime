<?php
require_once __DIR__.'/auth.php';
require_admin();
$pdo = db(); $id = (int)($_POST['id'] ?? 0); $action = $_POST['action'] ?? '';
if (!$pdo || !$id || !in_array($action, ['approve','hide','delete'], true)) { header('Location: reviews.php?error='.urlencode('Yêu cầu không hợp lệ')); exit; }
try {
    if ($action === 'delete') { $pdo->prepare('DELETE FROM product_reviews WHERE id=?')->execute([$id]); $message='Đã xoá đánh giá'; }
    else { $status=$action==='approve'?'approved':'rejected'; $pdo->prepare('UPDATE product_reviews SET status=? WHERE id=?')->execute([$status,$id]); $message=$status==='approved'?'Đánh giá đã được hiển thị':'Đánh giá đã được ẩn'; }
    header('Location: reviews.php?ok='.urlencode($message));
} catch (Throwable $e) { header('Location: reviews.php?error='.urlencode('Không thể cập nhật đánh giá')); }
