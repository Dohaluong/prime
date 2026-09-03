<?php
$weeklyFallbackColors = [
  ['code'=>'Be cát','hex_code'=>'#C9B9A0'], ['code'=>'Xám ghi','hex_code'=>'#A8A29A'],
  ['code'=>'Olive','hex_code'=>'#5F6B52'], ['code'=>'Nâu đất','hex_code'=>'#6E5544'], ['code'=>'Than chì','hex_code'=>'#3E3B34'],
];
?>
<div class="pdp weekly-product-card">
  <div class="gallery">
    <div class="main-photo"><img src="<?= htmlspecialchars($weeklyMedia[0] ?? $spotlight['image']) ?>" alt="<?= htmlspecialchars($spotlight['name']) ?>"></div>
    <div class="demo video-embed"><iframe src="https://www.youtube-nocookie.com/embed/vO7mYgL7XvY?controls=1&amp;modestbranding=1&amp;rel=0&amp;iv_load_policy=3&amp;playsinline=1&amp;disablekb=1&amp;fs=0" title="Video <?= htmlspecialchars($spotlight['name']) ?>" allow="autoplay; encrypted-media; picture-in-picture"></iframe></div>
    <div class="thumbs"><?php foreach ($weeklyMedia as $photo): ?><img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($spotlight['name']) ?>"><?php endforeach; ?></div>
  </div>
  <div class="product-info">
    <h1><?= htmlspecialchars($spotlight['name']) ?></h1>
    <div class="stars">★★★★★ <span style="color:#6c665c;letter-spacing:0"> <?= htmlspecialchars((string)$spotlight['rating']) ?> · <?= (int)$spotlight['reviews'] ?> đánh giá</span></div>
    <p class="hero-price" data-client-price><?= money($spotlight['price']) ?></p>
    <div class="divide"></div>
    <div class="choice-label">Kích thước <span>Chọn option phù hợp với không gian</span></div>
    <div class="options" data-client-sizes><?php foreach ($weeklyVariants['sizes'] as $i => $size): ?><button class="size-option <?= $i === 0 ? 'active' : '' ?>" type="button" data-client-size="<?= htmlspecialchars((string)$size['id']) ?>"><b><?= htmlspecialchars($size['name']) ?></b></button><?php endforeach; ?></div>
    <div class="choice-label" style="margin-top:24px">Chất liệu <span data-client-material-name></span></div>
    <div class="options"><select class="client-material-select" data-client-material><?php foreach ($weeklyVariants['materials'] as $material): ?><option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?></option><?php endforeach; ?></select></div>
    <div class="choice-label" style="margin-top:24px">Màu chất liệu <span data-client-color-name></span></div>
    <div class="options" data-client-colors></div>
    <div class="actions"><a class="button" href="https://zalo.me/0934430111">Nhắn Zalo tư vấn mẫu này</a><button class="button outline-dark" type="button" data-add-cart>Cho vào giỏ hàng</button></div>
    <div class="availability <?= $spotlight['fast'] ? '' : 'order' ?>"><div class="policy-rows">
      <button type="button" class="policy-row"><span class="policy-row-label"><i class="bi bi-box-seam status-icon"></i> <?= htmlspecialchars($spotlight['status']) ?></span><i class="chevron">›</i></button>
      <button type="button" class="policy-row"><span class="policy-row-label"><i class="bi bi-shield-check"></i> Bảo hành khung 10 năm · motor 2 năm · vải 1 năm</span><i class="chevron">›</i></button>
      <button type="button" class="policy-row"><span class="policy-row-label"><i class="bi bi-truck"></i> Miễn phí vận chuyển & lắp đặt nội thành Hà Nội</span><i class="chevron">›</i></button>
      <button type="button" class="policy-row"><span class="policy-row-label"><i class="bi bi-arrow-repeat"></i> Đổi trả trong 7 ngày nếu chưa qua sử dụng</span><i class="chevron">›</i></button>
    </div></div>
  </div>
</div>
<script>window.primeVariants=<?=json_encode($weeklyVariants, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;window.primeFallbackColors=<?=json_encode($weeklyFallbackColors, JSON_UNESCAPED_UNICODE)?>;window.primeBasePrice=<?=json_encode((float)$spotlight['price'])?>;</script>
