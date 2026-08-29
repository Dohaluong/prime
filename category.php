<?php
require_once __DIR__.'/data.php'; require_once __DIR__.'/partials.php';
$all=products(); $types=array_values(array_unique(array_column($all,'type')));
$priceBands=[
  ['key'=>'all','label'=>'Tất cả mức giá','min'=>0,'max'=>PHP_INT_MAX],
  ['key'=>'a','label'=>'Dưới 20 triệu','min'=>0,'max'=>20000000],
  ['key'=>'b','label'=>'20 – 30 triệu','min'=>20000000,'max'=>30000000],
  ['key'=>'c','label'=>'Trên 30 triệu','min'=>30000000,'max'=>PHP_INT_MAX],
];
header_page('Sản phẩm | IMA PRIME');
?>
<main class="catalog-page">
<div class="container crumb"><a href="index.php">Trang chủ</a> / <span>Sản phẩm</span></div>
<section class="container catalog-hero"><div><div class="eyebrow">Danh mục</div><h1>Sản phẩm</h1></div><p>Toàn bộ sofa giường mở điện, sofa chỉnh điện và sofa modular đang bán. Mẫu có nhãn xanh là bán thành phẩm dựng sẵn tại xưởng, giao trong 2-3 ngày.</p></section>
<div class="container catalog-layout">
<aside class="catalog-filter">
<div class="filter-head"><span class="eyebrow">Bộ lọc</span><button type="button" data-catalog-reset hidden>Xoá bộ lọc</button></div>
<div class="filter-group"><span>Danh mục</span><button class="active" data-catalog-filter="all">Tất cả <i><?=count($all)?></i></button><?php foreach($types as $type): ?><button data-catalog-filter="<?=htmlspecialchars($type)?>"><?=htmlspecialchars($type)?> <i><?=count(array_filter($all,fn($p)=>$p['type']===$type))?></i></button><?php endforeach; ?></div>
<div class="filter-group"><span>Tình trạng hàng</span><label><input type="checkbox" data-catalog-stock><span class="stock-status"><span class="stock-dot"></span>Có sẵn, giao 2–3 ngày</span></label></div>
<div class="filter-group"><span>Mức giá</span><?php foreach($priceBands as $band): ?><button class="<?=$band['key']==='all'?'active':''?>" data-catalog-price="<?=$band['key']?>" data-price-min="<?=$band['min']?>" data-price-max="<?=$band['max']===PHP_INT_MAX?'':$band['max']?>"><?=$band['label']?> <i><?=count(array_filter($all,fn($p)=>$p['price']>=$band['min']&&$p['price']<$band['max']))?></i></button><?php endforeach; ?></div>
<div class="filter-group"><span>Màu vải</span><div class="catalog-color-list" aria-label="Màu vải cơ bản"><button type="button" class="catalog-color-swatch" style="--swatch:#829a92" title="Xanh sage"></button><button type="button" class="catalog-color-swatch" style="--swatch:#b77973" title="Đỏ đất"></button><button type="button" class="catalog-color-swatch" style="--swatch:#8b829b" title="Tím khói"></button><button type="button" class="catalog-color-swatch" style="--swatch:#c6ae71" title="Vàng mù tạt"></button><button type="button" class="catalog-color-swatch" style="--swatch:#a79585" title="Be nâu"></button><button type="button" class="catalog-color-swatch" style="--swatch:#75848a" title="Xanh xám"></button></div><a href="fabric-library.php">Xem thư viện vải →</a></div>
<div class="filter-group"><span>Sắp xếp</span><select data-catalog-sort><option value="featured">Phổ biến nhất</option><option value="low">Giá thấp đến cao</option><option value="high">Giá cao đến thấp</option><option value="rating">Đánh giá cao nhất</option></select></div>
</aside>
<section>
<button class="catalog-mobile-filter" type="button" data-catalog-filter-open><i class="bi bi-sliders"></i> Bộ lọc <span data-catalog-mobile-count></span></button>
<div class="catalog-count"><span data-catalog-count><?=count($all)?> mẫu</span><div class="catalog-tags" data-catalog-tags hidden></div></div>
<div class="catalog-grid" data-catalog-grid><?php foreach($all as $p): ?><a class="catalog-card" data-catalog-type="<?=htmlspecialchars($p['type'])?>" data-catalog-price="<?=$p['price']?>" data-catalog-fast="<?=$p['fast']?>" data-catalog-rating="<?=$p['rating']?>" href="product.php?slug=<?=urlencode($p['slug'])?>"><div class="catalog-image"><img src="<?=htmlspecialchars($p['image'])?>" alt="<?=htmlspecialchars($p['name'])?>"><span class="badge <?=$p['fast']?'':'order'?>"><?=htmlspecialchars($p['status'])?></span></div><div><div class="catalog-title"><h3><?=htmlspecialchars($p['name'])?></h3><small>★ <?=htmlspecialchars($p['rating'])?></small></div><p><?=htmlspecialchars($p['description'])?></p><b><?=money($p['price'])?></b></div></a><?php endforeach; ?></div>
<div class="catalog-empty" data-catalog-empty hidden><h3>Không có mẫu nào khớp bộ lọc</h3><p>Thử nới mức giá hoặc bỏ lọc "có sẵn" để xem cả mẫu đặt theo đơn. Hoặc nhắn Zalo — chúng tôi kiểm tra tồn kho theo đúng kích thước và màu bạn cần.</p><button type="button" data-catalog-reset-empty>Xoá bộ lọc</button></div>
<div class="catalog-pagination" data-catalog-pagination hidden><span data-catalog-range></span><div class="pagination-controls"><button type="button" class="page-btn page-nav" data-catalog-prev>← Trước</button><div data-catalog-pages></div><button type="button" class="page-btn page-nav" data-catalog-next>Sau →</button></div></div>
</section>
</div>
<section class="container catalog-cta"><div><h2>Không chắc mẫu nào vừa phòng khách?</h2><p>Gửi ảnh phòng và ba số đo, chúng tôi lọc sẵn danh sách mẫu vừa lối đi, vừa thang máy và đang có hàng.</p></div><a class="button" href="https://zalo.me/0934430111">Nhắn Zalo tư vấn</a></section>
</main>
<?php footer_page(); ?>
