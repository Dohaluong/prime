<?php
require_once __DIR__.'/config.php';

function products(): array {
    $fallback = [
      ['slug'=>'famigo','name'=>'Sofa băng FAMIGO','type'=>'Sofa giường mở điện','description'=>'Sofa giường mở điện · hộc đồ dưới đệm','price'=>24400000,'rating'=>'4.9','reviews'=>12,'image'=>'https://ima.vn/assets/uploads/media/Famigo_000275.jpg','status'=>'Giao nhanh 2-3 ngày','fast'=>1],
      ['slug'=>'casa','name'=>'Sofa băng CASA','type'=>'Sofa chỉnh điện','description'=>'Sofa chỉnh điện recliner · 2 ngả độc lập','price'=>19700000,'rating'=>'5.0','reviews'=>8,'image'=>'https://ima.vn/assets/uploads/media/6a151bce2644c_IMA04360.jpg','status'=>'Giao nhanh 2-3 ngày','fast'=>1],
      ['slug'=>'trio','name'=>'Sofa băng TRIO','type'=>'Sofa modular / góc đa năng','description'=>'Sofa modular · ghế nghỉ tháo rời được','price'=>20700000,'rating'=>'4.8','reviews'=>9,'image'=>'https://ima.vn/assets/uploads/media/Trio_000289.jpg','status'=>'Giao nhanh 2-3 ngày','fast'=>1],
      ['slug'=>'napoli','name'=>'Sofa băng NAPOLI','type'=>'Sofa chỉnh điện','description'=>'Thiết kế tối ưu không gian phòng khách · 2 phiên bản','price'=>16500000,'rating'=>'4.9','reviews'=>15,'image'=>'https://ima.vn/assets/uploads/media/Napoli_000153.jpg','status'=>'Giao nhanh 2-3 ngày','fast'=>1],
      ['slug'=>'casa-large','name'=>'Sofa băng CASA 2.6m','type'=>'Sofa góc chỉnh điện','description'=>'Bản góc lớn · chỉnh điện 3 vị trí','price'=>23400000,'rating'=>'4.9','reviews'=>6,'image'=>'https://ima.vn/assets/uploads/media/Casa_000048.jpg','status'=>'Đặt hàng — giao sau 14 ngày','fast'=>0],
      ['slug'=>'napoli-compact','name'=>'Sofa băng NAPOLI 1.8m','type'=>'Sofa giường mở điện','description'=>'Bản gọn cho căn hộ 1–2 phòng ngủ','price'=>13500000,'rating'=>'4.8','reviews'=>11,'image'=>'https://ima.vn/assets/uploads/media/Napoli_000326.jpg','status'=>'Giao nhanh 2-3 ngày','fast'=>1],
    ];
    $pdo = db(); if (!$pdo) return $fallback;
    try { $rows=$pdo->query('SELECT * FROM products WHERE active=1 ORDER BY featured DESC, id')->fetchAll(PDO::FETCH_ASSOC);$images=$pdo->prepare('SELECT image_url FROM product_images WHERE product_id=? ORDER BY is_featured DESC,sort_order,id LIMIT 2');foreach($rows as &$row){$images->execute([$row['id']]);$photos=$images->fetchAll(PDO::FETCH_COLUMN);$row['image']=prime_asset_url($photos[0]??$row['image']??'');$row['image_secondary']=prime_asset_url($photos[1]??'');}unset($row);return $rows ?: $fallback; } catch(Throwable $e) { return $fallback; }
}
function product(string $slug): array { foreach(products() as $p) if($p['slug']===$slug) return $p; return products()[0]; }
function money($n): string { return number_format((float)$n, 0, ',', '.').'đ'; }
function product_media(int $productId, string $fallback=''): array {
    $pdo=db(); if(!$pdo || !$productId) return $fallback ? [$fallback] : [];
    try {$q=$pdo->prepare('SELECT image_url FROM product_images WHERE product_id=? ORDER BY is_featured DESC,sort_order,id');$q->execute([$productId]);$rows=array_map('prime_asset_url',$q->fetchAll(PDO::FETCH_COLUMN));return $rows ?: ($fallback?[$fallback]:[]);}catch(Throwable $e){return $fallback?[$fallback]:[];}
}
function product_variant_data(int $productId): array {
    $fallback=['sizes'=>[['id'=>0,'name'=>'1.8m','price'=>14800000],['id'=>0,'name'=>'2.1m','price'=>24400000],['id'=>0,'name'=>'2.6m','price'=>29900000]],'materials'=>[['id'=>0,'name'=>'Vải bố kháng nước','coefficient'=>1]],'colors'=>[],'prices'=>[]];
    $pdo=db(); if(!$pdo || !$productId) return $fallback;
    try {
      $q=$pdo->prepare('SELECT id,name FROM product_size_options WHERE product_id=? ORDER BY sort_order,id');$q->execute([$productId]);$sizes=$q->fetchAll(PDO::FETCH_ASSOC);
      $q=$pdo->prepare('SELECT m.id,m.name,m.coefficient,r.color_ids_json FROM product_variant_materials r JOIN materials m ON m.id=r.material_id WHERE r.product_id=? ORDER BY m.name');$q->execute([$productId]);$materials=$q->fetchAll(PDO::FETCH_ASSOC);
      if(!$sizes || !$materials) return $fallback;
      $ids=implode(',',array_map('intval',array_column($materials,'id')));$colors=$pdo->query("SELECT id,material_id,code,image,hex_code FROM material_colors WHERE material_id IN ($ids) ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);foreach($colors as &$color)$color['image']=prime_asset_url($color['image']??'');unset($color);foreach($materials as &$material){$raw=$material['color_ids_json'];if($raw!==null){$selected=array_map('intval',json_decode($raw,true)?:[]);$colors=array_values(array_filter($colors,fn($color)=>$color['material_id']!=$material['id']||in_array((int)$color['id'],$selected,true)));}$material['has_color_rule']=$raw!==null;unset($material['color_ids_json']);}unset($material);
      $q=$pdo->prepare('SELECT material_id,size_option_id,price FROM product_variant_prices WHERE product_id=?');$q->execute([$productId]);$prices=[];foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row)$prices[$row['material_id'].'_'.$row['size_option_id']]=(float)$row['price'];
      return compact('sizes','materials','colors','prices');
    } catch(Throwable $e) { return $fallback; }
}
function product_reviews(int $productId): array {
    $pdo=db(); if(!$pdo||!$productId)return [];
    try{$q=$pdo->prepare('SELECT * FROM product_reviews WHERE product_id=? AND status="approved" ORDER BY created_at DESC');$q->execute([$productId]);$reviews=$q->fetchAll(PDO::FETCH_ASSOC);$images=$pdo->prepare('SELECT image_url FROM product_review_images WHERE review_id=? ORDER BY sort_order,id');foreach($reviews as &$review){$images->execute([$review['id']]);$review['images']=$images->fetchAll(PDO::FETCH_COLUMN);}unset($review);return $reviews;}catch(Throwable $e){return [];}
}
function about_content(): array {
    $fallback = [
      'hero_title'=>'Về IMA PRIME',
      'hero_image'=>'https://ima.vn/assets/uploads/Showroom_6901a3a4646e46.68674322.jpg',
      'lede_html'=>'<p>Trong hơn một thập kỷ, IMASOFA gắn với một hình ảnh quen thuộc: khách bước vào showroom 201 Trương Chinh, ngồi thử từng mẫu sofa da thật, chạm vào từng đường may trước khi quyết định. Đó là cách chúng tôi bán hàng — chậm rãi, trực tiếp, tin tưởng qua trải nghiệm tận tay.</p><p>Nhưng vài năm trở lại đây, chúng tôi nhận ra một điều: rất nhiều khách hàng không còn muốn — hoặc không còn kịp — ghé showroom trước khi mua một món đồ nội thất. Họ tìm hiểu qua điện thoại, xem video, nhắn tin hỏi vào lúc 9 giờ tối sau giờ làm, và mong nhận được câu trả lời ngay lúc đó chứ không phải hẹn cuối tuần ghé xem. Nhu cầu của họ cũng khác — căn hộ nhỏ hơn, cần một chiếc sofa vừa là chỗ ngồi, vừa là chỗ ngủ, vừa cất được chăn gối, mà không phải đợi hàng đặt riêng cả tháng trời.</p><p>IMA PRIME ra đời từ đó — không phải để thay thế IMASOFA, mà để trả lời đúng câu hỏi mà showroom truyền thống chưa trả lời được: làm sao mang một sản phẩm nội thất đáng tin cậy đến với khách hàng mà không bắt họ phải đến tận nơi mới yên tâm mua.</p>',
      'duo_image_1'=>'https://ima.vn/assets/uploads/Showroom_6901a3c423ce47.85717517.jpg',
      'duo_image_2'=>'https://ima.vn/assets/uploads/project_photo/a89-design-675ff2436f310.jpg',
      'section1_heading'=>'Chúng tôi giữ lại phần khó nhất, bỏ đi phần chậm nhất',
      'section1_html'=>'<p>Việc chuyển sang bán online không có nghĩa là làm hàng đại trà, kém chăm chút. Khung sofa và phần bọc vẫn được chúng tôi tự sản xuất tại xưởng riêng, đúng cách chúng tôi vẫn làm cho IMASOFA suốt nhiều năm qua. Phần thay đổi là cách chúng tôi tổ chức sản xuất: thay vì chờ đặt hàng rồi mới bắt đầu từ con số không, chúng tôi dựng sẵn phần khung theo một số kích thước cố định, để khi khách chốt màu vải, chỉ còn công đoạn cắt và bọc — thường xong trong 2–3 ngày. Đủ nhanh để phù hợp với một quyết định mua hàng được đưa ra qua tin nhắn, không phải sau một buổi hẹn xem hàng.</p><p>Phần cơ cấu điện — motor, bộ điều khiển cho các dòng sofa chỉnh điện và sofa giường mở điện — là phần duy nhất chúng tôi nhập khẩu, được kiểm tra kỹ trước khi lắp vào từng sản phẩm. Đây cũng là lý do chúng tôi minh bạch tách riêng thời hạn bảo hành cho khung và cho phần điện, thay vì gộp chung một con số nghe cho yên tâm nhưng không rõ ràng.</p>',
      'stats'=>[
        ['number'=>'10 năm','label'=>'bảo hành khung, phần chúng tôi tự làm'],
        ['number'=>'2 năm','label'=>'bảo hành cơ cấu điện, linh kiện nhập khẩu'],
        ['number'=>'2–3 ngày','label'=>'từ lúc chốt màu vải đến khi giao'],
        ['number'=>'1 xưởng','label'=>'khung và bọc vải làm tại Việt Nam'],
      ],
      'section2_heading'=>'Online không có nghĩa là xa cách',
      'section2_html'=>'<p>Cái chúng tôi lo nhất khi rời khỏi mô hình showroom là mất đi phần con người trong mỗi lần bán hàng. Nên với IMA PRIME, showroom Hà Nội vẫn ở đó — cho ai muốn ngồi thử trước khi quyết định — nhưng với khách hàng ở xa, chúng tôi cố gắng bù lại bằng cách khác: video quay thật cảnh cơ chế vận hành, trả lời tin nhắn nhanh và thật, gọi điện xác nhận trước mỗi lần giao hàng, và một đội ngũ sẵn sàng gửi linh kiện thay thế, hướng dẫn tận tình nếu chẳng may có trục trặc.</p><p>Chúng tôi không xem online là một kênh bán hàng phụ để mở rộng doanh số. Với IMA PRIME, đó là cách chúng tôi chọn để đến gần hơn với những gia đình mà một chiếc sofa không chỉ cần đẹp, mà còn cần thông minh, gọn gàng và đúng lúc — theo đúng nhịp sống hôm nay.</p>',
      'trio_image_1'=>'https://ima.vn/assets/uploads/Showroom_6901a3c4a99466.07953148.jpg',
      'trio_image_2'=>'https://ima.vn/assets/uploads/Showroom_6901a3c5518cb6.68403480.jpg',
      'trio_image_3'=>'https://ima.vn/assets/uploads/Showroom_6901a3c628a302.92196515.jpg',
      'cta_heading'=>'Ghé showroom, hoặc nhắn tin lúc 9 giờ tối.',
      'cta_text'=>'Cách nào cũng là chúng tôi. 201 Trường Chinh mở 9h–18h hàng ngày, Zalo trả lời đến 20h.',
    ];
    $pdo = db(); if (!$pdo) return $fallback;
    try {
      $row = $pdo->query('SELECT * FROM about_content WHERE id=1')->fetch(PDO::FETCH_ASSOC);
      if (!$row) return $fallback;
      $stats = json_decode($row['stats_json'] ?? '', true);
      $row['stats'] = $stats ?: $fallback['stats'];
      foreach ($fallback as $key => $value) {
        if ($key === 'stats') continue;
        if (!isset($row[$key]) || $row[$key] === '' || $row[$key] === null) $row[$key] = $value;
      }
      return $row;
    } catch (Throwable $e) { return $fallback; }
}
