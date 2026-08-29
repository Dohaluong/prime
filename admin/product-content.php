<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json; charset=utf-8');
$pdo=db(); $id=(int)($_GET['id']??0);
if(!$pdo||!$id){echo '{"description":"","specifications":[]}';exit;}
$q=$pdo->prepare('SELECT detailed_description,specifications_json FROM products WHERE id=?');$q->execute([$id]);$row=$q->fetch(PDO::FETCH_ASSOC)?:[];
echo json_encode(['description'=>$row['detailed_description']??'','specifications'=>json_decode($row['specifications_json']??'[]',true)?:[]],JSON_UNESCAPED_UNICODE);
