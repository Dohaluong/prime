<?php
require_once __DIR__.'/partials.php';
require_once __DIR__.'/config.php';
$colors=[]; $materials=[];
try { $pdo=db(); $colors=$pdo ? $pdo->query('SELECT c.*,m.name material_name FROM material_colors c JOIN materials m ON m.id=c.material_id ORDER BY m.name,c.code')->fetchAll(PDO::FETCH_ASSOC) : []; $materials=array_values(array_unique(array_column($colors,'material_name'))); } catch(Throwable $e) {}
header_page('Thư viện vải | IMA PRIME');
?>
<main class="content-page"><section class="page-hero container"><div class="eyebrow">Thư viện vật liệu</div><h1>Chọn chất liệu trước, chọn mẫu sau.</h1><p>Mỗi ảnh là một mã vải/da đang có tại xưởng. Màu hiển thị có thể chênh lệch nhẹ theo màn hình — nhắn Zalo để nhận ảnh, video thực tế trước khi đặt.</p></section><section class="container"><div class="fabric-filters" data-fabric-filters><button class="active" type="button" data-fabric-filter="all">Tất cả</button><?php foreach($materials as $material): ?><button type="button" data-fabric-filter="<?=htmlspecialchars($material)?>"><?=htmlspecialchars($material)?></button><?php endforeach; ?></div><div class="fabric-library-grid" data-fabric-grid><?php foreach($colors as $c): ?><figure data-fabric-material="<?=htmlspecialchars($c['material_name'])?>"><img src="<?=htmlspecialchars($c['image'])?>" alt="<?=htmlspecialchars($c['code'])?>"><figcaption><b><?=htmlspecialchars($c['code'])?></b><span><?=htmlspecialchars($c['material_name'])?></span></figcaption></figure><?php endforeach; ?></div><div class="policy-callout"><h3>Muốn xem màu dưới ánh sáng nhà bạn?</h3><p>Gửi chúng tôi mẫu sofa và mã màu bạn đang cân nhắc.</p><a class="button" href="https://zalo.me/0934430111">Nhắn Zalo xem ảnh thật</a></div></section></main>
<?php footer_page(); ?>
