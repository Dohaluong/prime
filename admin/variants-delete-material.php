<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json');
$pdo=db(); $productId=(int)($_POST['product_id']??0); $materialId=(int)($_POST['material_id']??0);
if(!$pdo||!$productId||!$materialId){http_response_code(422);exit;}
$pdo->prepare('DELETE FROM product_variant_materials WHERE product_id=? AND material_id=?')->execute([$productId,$materialId]); echo '{"ok":true}';
