<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json');
$pdo=db(); $productId=(int)($_POST['product_id']??0); $id=(int)($_POST['id']??0); $name=trim($_POST['name']??'');
$details=json_decode($_POST['details']??'{}',true);
if(!$pdo||!$productId||!$id||$name===''||!is_array($details)){http_response_code(422);exit;}
try{$pdo->prepare('UPDATE product_size_options SET name=?,details_json=? WHERE id=? AND product_id=?')->execute([$name,json_encode($details,JSON_UNESCAPED_UNICODE),$id,$productId]);echo '{"ok":true}';}catch(Throwable $e){http_response_code(422);echo '{"error":"Không thể lưu kích thước"}';}
