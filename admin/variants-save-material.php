<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json');
$pdo=db(); $productId=(int)($_POST['product_id']??0); $materialId=(int)($_POST['material_id']??0);
$colors=json_decode($_POST['color_ids']??'[]',true);
if(!$pdo||!$productId||!$materialId||!is_array($colors)){http_response_code(422);exit;}
$colors=array_values(array_unique(array_map('intval',$colors)));
$pdo->prepare('UPDATE product_variant_materials SET color_ids_json=? WHERE product_id=? AND material_id=?')->execute([json_encode($colors),$productId,$materialId]); echo '{"ok":true}';
