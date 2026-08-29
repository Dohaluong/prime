<?php
require_once __DIR__.'/config.php';
header('Content-Type: application/json; charset=utf-8');
try {
    $payload=json_decode(file_get_contents('php://input'),true,512,JSON_THROW_ON_ERROR);
    $items=$payload['items']??[]; $customer=$payload['customer']??[];
    if (!$items || empty($customer['name']) || empty($customer['phone']) || empty($customer['address'])) throw new RuntimeException('Thiếu thông tin đơn hàng.');
    $pdo=db(); if(!$pdo) throw new RuntimeException('Không kết nối được cơ sở dữ liệu.');
    $code='PRM-'.date('y').'-'.str_pad((string)random_int(1,9999),4,'0',STR_PAD_LEFT);
    $subtotal=array_sum(array_map(fn($item)=>(float)($item['price']??0)*(int)($item['quantity']??1),$items));
    $pdo->beginTransaction();
    $q=$pdo->prepare('INSERT INTO orders(order_code,customer_name,phone,email,city,district,address,delivery_date,delivery_time,payment_method,notes,subtotal) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
    $q->execute([$code,$customer['name'],$customer['phone'],$customer['email']??null,$customer['city']??null,$customer['district']??null,$customer['address'],$customer['date']??null,$customer['time']??null,$customer['payment']??null,$customer['notes']??null,$subtotal]);
    $orderId=$pdo->lastInsertId(); $q=$pdo->prepare('INSERT INTO order_items(order_id,product_name,product_image,option_text,unit_price,quantity) VALUES(?,?,?,?,?,?)');
    foreach($items as $item)$q->execute([$orderId,$item['name']??'Sản phẩm IMA PRIME',$item['image']??null,$item['option']??null,$item['price']??0,max(1,(int)($item['quantity']??1))]);
    $pdo->commit(); echo json_encode(['ok'=>true,'code'=>$code,'id'=>$orderId]);
} catch(Throwable $e) { if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack(); http_response_code(422); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
