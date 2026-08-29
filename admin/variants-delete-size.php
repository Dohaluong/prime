<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json');
$pdo=db(); $productId=(int)($_POST['product_id']??0); $id=(int)($_POST['id']??0);
if(!$pdo||!$productId||!$id){http_response_code(422);exit;}
$pdo->prepare('DELETE FROM product_size_options WHERE id=? AND product_id=?')->execute([$id,$productId]); echo '{"ok":true}';
