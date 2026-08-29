<?php
require_once __DIR__.'/data.php';
header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_map(fn($p)=>['slug'=>$p['slug'],'name'=>$p['name'],'description'=>$p['description'],'price'=>money($p['price']),'rating'=>$p['rating'],'image'=>$p['image'],'status'=>$p['status']],products()),JSON_UNESCAPED_UNICODE);
