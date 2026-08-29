<?php
require_once 'auth.php'; require_admin(); header('Content-Type: application/json; charset=utf-8');
$pdo=db();$productId=(int)($_POST['product_id']??0);$url=trim($_POST['image_url']??'');$action=$_POST['action']??'';
if(!$pdo||!$productId||$url===''||!in_array($action,['primary','secondary','delete'],true)){http_response_code(422);exit;}
try{$check=$pdo->prepare('SELECT id FROM product_images WHERE product_id=? AND image_url=?');$check->execute([$productId,$url]);if(!$check->fetchColumn())throw new RuntimeException();$pdo->beginTransaction();
if($action==='delete'){$pdo->prepare('DELETE FROM product_images WHERE product_id=? AND image_url=?')->execute([$productId,$url]);$q=$pdo->prepare('SELECT image_url FROM product_images WHERE product_id=? ORDER BY is_featured DESC,sort_order,id LIMIT 1');$q->execute([$productId]);$next=$q->fetchColumn()?:'';$pdo->prepare('UPDATE products SET image=? WHERE id=?')->execute([$next,$productId]);}
else if($action==='primary'){$pdo->prepare('UPDATE product_images SET is_featured=0 WHERE product_id=?')->execute([$productId]);$pdo->prepare('UPDATE product_images SET is_featured=1,sort_order=0 WHERE product_id=? AND image_url=?')->execute([$productId,$url]);$pdo->prepare('UPDATE products SET image=? WHERE id=?')->execute([$url,$productId]);}
else {
    // A secondary image must never replace the existing primary image. Move only
    // non-primary images out of slot 1, then reserve that slot for this image.
    $pdo->prepare('UPDATE product_images SET sort_order=sort_order+10 WHERE product_id=? AND image_url<>? AND is_featured=0')->execute([$productId,$url]);
    $pdo->prepare('UPDATE product_images SET is_featured=0,sort_order=1 WHERE product_id=? AND image_url=?')->execute([$productId,$url]);

    // If the user changed the current primary into a secondary image, promote
    // the next available image so every product still has exactly one primary.
    $primary=$pdo->prepare('SELECT image_url FROM product_images WHERE product_id=? AND is_featured=1 ORDER BY sort_order,id LIMIT 1');
    $primary->execute([$productId]);
    $primaryUrl=$primary->fetchColumn();
    if(!$primaryUrl){
        $next=$pdo->prepare('SELECT image_url FROM product_images WHERE product_id=? AND image_url<>? ORDER BY sort_order,id LIMIT 1');
        $next->execute([$productId,$url]);
        $primaryUrl=$next->fetchColumn()?:$url;
        $pdo->prepare('UPDATE product_images SET is_featured=1,sort_order=0 WHERE product_id=? AND image_url=?')->execute([$productId,$primaryUrl]);
    }
    $pdo->prepare('UPDATE products SET image=? WHERE id=?')->execute([$primaryUrl,$productId]);
}
$pdo->commit();echo '{"ok":true}';}catch(Throwable $e){if($pdo&&$pdo->inTransaction())$pdo->rollBack();http_response_code(422);echo '{"ok":false}';}
