<?php
require_once __DIR__.'/auth.php'; require_admin(); $pdo=db();
if($_SERVER['REQUEST_METHOD']==='POST'&&$pdo&&!empty($_POST['id']))try{$pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]);header('Location: products.php?ok=Đã xoá sản phẩm');exit;}catch(Throwable $e){}
header('Location: products.php?error=Không thể xoá sản phẩm');
