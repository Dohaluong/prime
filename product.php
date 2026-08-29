<?php
require 'data.php';
require 'partials.php';

$p = product($_GET['slug'] ?? 'famigo');
$media = product_media((int)($p['id'] ?? 0), $p['image']);
$variants = product_variant_data((int)($p['id'] ?? 0));
$approvedReviews = product_reviews((int)($p['id'] ?? 0));
$detailedDescription = $p['detailed_description'] ?? '';
$specifications = json_decode($p['specifications_json'] ?? '[]', true) ?: [];
$defaultColors = [
  ['code'=>'Be cát','hex_code'=>'#C9B9A0'], ['code'=>'Xám ghi','hex_code'=>'#A8A29A'],
  ['code'=>'Olive','hex_code'=>'#5F6B52'], ['code'=>'Nâu đất','hex_code'=>'#6E5544'],
  ['code'=>'Than chì','hex_code'=>'#3E3B34'],
];
$accessories = array_slice(array_values(array_filter(products(), fn($item) => ($item['slug'] ?? '') !== ($p['slug'] ?? ''))), 0, 3);

/** Use YouTube's privacy-enhanced embed with the smallest practical player UI. */
function prime_video_embed_url(string $url): string {
  $cleanUrl = html_entity_decode(trim($url));
  if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:embed/|shorts/|watch\?(?:[^#]*&)?v=))([A-Za-z0-9_-]{6,})~i', $cleanUrl, $match)) {
    return 'https://www.youtube-nocookie.com/embed/'.$match[1].'?controls=1&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&disablekb=1&fs=0';
  }
  return $cleanUrl;
}
$videoEmbedUrl = prime_video_embed_url($p['video_url'] ?? 'https://www.youtube.com/watch?v=vO7mYgL7XvY');
header_page($p['name'].' | IMA PRIME');
?>
<main>
  <div class="container crumb"><a href="index.php">Trang chủ</a> / <a href="category.php">Sản phẩm</a> / <?= htmlspecialchars($p['name']) ?></div>
  <section class="container pdp">
    <div class="gallery">
      <div class="main-photo"><img src="<?= htmlspecialchars($media[0]) ?>" alt="<?= htmlspecialchars($p['name']) ?>"></div>
      <?php if (!empty($p['video_url'])): ?>
        <div class="demo video-embed"><iframe src="<?= htmlspecialchars($videoEmbedUrl) ?>" title="Video <?= htmlspecialchars($p['name']) ?>" allow="autoplay; encrypted-media; picture-in-picture"></iframe></div>
      <?php else: ?>
        <div class="demo video-embed"><iframe src="<?= htmlspecialchars($videoEmbedUrl) ?>" title="Video demo cơ chế sofa điện" allow="autoplay; encrypted-media; picture-in-picture"></iframe></div>
      <?php endif; ?>
      <div class="thumbs"><?php foreach ($media as $photo): ?><img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($p['name']) ?>"><?php endforeach; ?></div>
      <article class="gallery-detail"><div class="eyebrow">Về sản phẩm</div><h2><?= htmlspecialchars($p['name']) ?></h2><?php if ($detailedDescription): ?><div class="detailed-description"><?= $detailedDescription ?></div><?php else: ?><p><?= nl2br(htmlspecialchars($p['description'])) ?></p><?php endif; ?></article>
      <section class="product-specification"><div class="eyebrow">Product specification</div><h2>Thông số sản phẩm</h2><?php if ($specifications): ?><dl><?php foreach ($specifications as $spec): ?><div><dt><?= htmlspecialchars($spec['label'] ?? '') ?></dt><dd><?= htmlspecialchars($spec['value'] ?? '') ?></dd></div><?php endforeach; ?></dl><?php else: ?><dl><div><dt>Kích thước</dt><dd>Tuỳ option lựa chọn</dd></div><div><dt>Bảo hành</dt><dd>Khung 10 năm · motor 2 năm</dd></div></dl><?php endif; ?></section>
    </div>
    <div class="product-info">
      <h1><?= htmlspecialchars($p['name']) ?></h1>
      <div class="stars">★★★★★ <span style="color:#6c665c;letter-spacing:0"> <?= $p['rating'] ?> · <?= $p['reviews'] ?> đánh giá đã xác thực đơn</span></div>
      <p class="hero-price" data-client-price><?= money($p['price']) ?></p>
      <div class="divide"></div>
      <div class="choice-label">Kích thước <span>Chọn option phù hợp với không gian</span></div>
      <div class="options" data-client-sizes><?php foreach ($variants['sizes'] as $i => $size): ?><button class="size-option <?= $i === 0 ? 'active' : '' ?>" type="button" data-client-size="<?= htmlspecialchars((string)$size['id']) ?>"><b><?= htmlspecialchars($size['name']) ?></b></button><?php endforeach; ?></div>
      <div class="choice-label" style="margin-top:24px">Chất liệu <span data-client-material-name></span></div>
      <div class="options"><select class="client-material-select" data-client-material><?php foreach ($variants['materials'] as $material): ?><option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?></option><?php endforeach; ?></select></div>
      <div class="choice-label" style="margin-top:24px">Màu chất liệu <span data-client-color-name></span></div>
      <div class="options" data-client-colors></div>
      <div class="actions"><a class="button" href="https://zalo.me/0934430111">Nhắn Zalo tư vấn mẫu này</a><button class="button outline-dark" type="button" data-add-cart>Cho vào giỏ hàng</button></div>
      <div class="availability <?= $p['fast'] ? '' : 'order' ?>">
        <div class="policy-rows">
          <button type="button" class="policy-row" data-policy-open="stock"><span class="policy-row-label"><i class="bi bi-box-seam status-icon"></i> <span data-client-stock><?= htmlspecialchars($p['status']) ?></span></span><i class="chevron">›</i></button>
          <button type="button" class="policy-row" data-policy-open="warranty"><span class="policy-row-label"><i class="bi bi-shield-check"></i> Bảo hành khung 10 năm · motor 2 năm · vải 1 năm</span><i class="chevron">›</i></button>
          <button type="button" class="policy-row" data-policy-open="delivery"><span class="policy-row-label"><i class="bi bi-truck"></i> Miễn phí vận chuyển & lắp đặt nội thành Hà Nội</span><i class="chevron">›</i></button>
          <button type="button" class="policy-row" data-policy-open="returns"><span class="policy-row-label"><i class="bi bi-arrow-repeat"></i> Đổi trả trong 7 ngày nếu chưa qua sử dụng</span><i class="chevron">›</i></button>
        </div>
      </div>
      <div class="policy-content" hidden>
        <div data-policy-content="stock">
          <h3>Tình trạng hàng</h3>
          <?php if ($p['fast']): ?>
          <p>Mẫu này thuộc nhóm bán thành phẩm dựng sẵn tại xưởng — <?= htmlspecialchars($p['status']) ?>. Đơn được kiểm tra và đóng gói trong ngày, giao nội thành Hà Nội trong 2–3 ngày làm việc.</p>
          <?php else: ?>
          <p>Mẫu này được sản xuất theo đơn đặt hàng — <?= htmlspecialchars($p['status']) ?>. Thời gian hoàn thiện khoảng 14 ngày kể từ khi xác nhận màu và kích thước.</p>
          <?php endif; ?>
          <h4>Giá đã gồm VAT</h4>
          <p>Giá niêm yết đã bao gồm thuế VAT, không phát sinh thêm khi thanh toán.</p>
          <a class="under" href="policies.php#delivery">Xem đầy đủ chính sách vận chuyển →</a>
        </div>
        <div data-policy-content="warranty">
          <h3>Bảo hành</h3>
          <p>Ba mốc thời gian theo ba bộ phận, không gộp chung một con số.</p>
          <h4>Khung sofa / giường — 10 năm</h4><p>Áp dụng cho nứt, gãy, biến dạng khung trong điều kiện sử dụng bình thường.</p>
          <h4>Cơ cấu điện — 2 năm</h4><p>Gồm motor, bộ điều khiển, nguồn và dây; bảo hành theo cam kết linh kiện nhập khẩu.</p>
          <h4>Vải bọc / da — 1 năm</h4><p>Áp dụng cho lỗi sản xuất như bung chỉ, lệch đường may, bong lớp phủ.</p>
          <a class="under" href="policies.php#warranty">Xem đầy đủ chính sách bảo hành →</a>
        </div>
        <div data-policy-content="delivery">
          <h3>Vận chuyển & lắp đặt</h3>
          <h4>Nội thành Hà Nội — miễn phí</h4><p>Miễn phí vận chuyển và lắp đặt, giao theo khung giờ hẹn trước. Mẫu có nhãn Giao nhanh được giao trong 2–3 ngày.</p>
          <h4>Ngoài khu vực Hà Nội</h4><p>Phí tính theo khoảng cách thực tế, thông báo rõ trước khi xác nhận. Thời gian giao dự kiến 5–10 ngày tuỳ tỉnh.</p>
          <a class="under" href="policies.php#delivery">Xem đầy đủ chính sách vận chuyển →</a>
        </div>
        <div data-policy-content="returns">
          <h3>Đổi trả</h3>
          <p>Đổi hoặc trả trong 7 ngày nếu sản phẩm chưa qua sử dụng, không hư hại và còn nguyên điều kiện bàn giao. Miễn phí hoàn toàn nếu lỗi do sản xuất hoặc vận chuyển.</p>
          <p>Đơn cắt vải theo màu riêng không thể huỷ sau khi đã xác nhận cắt vải.</p>
          <a class="under" href="policies.php#returns">Xem đầy đủ chính sách đổi trả →</a>
        </div>
      </div>
    </div>
  </section>

  <section class="product-reviews"><div class="container"><div class="section-head"><div><div class="eyebrow">Khách hàng đánh giá</div><h2>Những phòng khách đã chọn <?= htmlspecialchars($p['name']) ?></h2></div><div><div class="review-score"><b><?= htmlspecialchars($p['rating']) ?>/5</b><span>★★★★★</span><small><?= count($approvedReviews) ?> đánh giá đã duyệt</small></div><button class="under review-open" type="button">Viết review</button></div></div><div class="review-list"><?php foreach ($approvedReviews as $review): ?><article class="customer-review"><div><b><?= htmlspecialchars($review['customer_name']) ?></b><span class="stars"><?= str_repeat('★', (int)$review['rating']) ?></span></div><p><?= nl2br(htmlspecialchars($review['content'])) ?></p><?php if ($review['images']): ?><div class="review-images"><?php foreach ($review['images'] as $image): ?><img src="<?= htmlspecialchars($image) ?>" alt="Ảnh khách hàng"><?php endforeach; ?></div><?php endif; ?></article><?php endforeach; ?><?php if (!$approvedReviews): ?><p class="review-empty">Chưa có đánh giá được duyệt. Hãy là người đầu tiên chia sẻ trải nghiệm.</p><?php endif; ?></div></div></section>

  <section class="container section"><div class="details"><div><div class="eyebrow">Đo trước khi đặt</div><h2>Ba số đo cần kiểm tra trước khi bấm đặt</h2><p class="description">Sofa được giao nguyên khối phần khung, chân tháo rời. Hãy đo cửa, hành lang và cabin thang máy trước khi đặt.</p></div><div class="measurements"><div><span><b>Kích thước tổng thể</b><small>Tuỳ theo option đã chọn</small></span><b>Liên hệ tư vấn</b></div><div><span><b>Khi mở thành giường</b><small>Cần chừa khoảng trống phía trước</small></span><b>210 × 145 cm</b></div><div><span><b>Lối vào tối thiểu</b><small>Cửa, hành lang, cửa thang máy</small></span><b>rộng 90 cm</b></div></div></div></section>
  <div class="policy-drawer" data-policy-drawer hidden>
    <div class="policy-drawer-backdrop" data-policy-close></div>
    <section class="policy-drawer-panel" role="dialog" aria-modal="true" aria-label="Chi tiết chính sách">
      <button class="policy-drawer-close" type="button" data-policy-close aria-label="Đóng">×</button>
      <div class="eyebrow">Chính sách</div>
      <div data-policy-drawer-body></div>
    </section>
  </div>
  <dialog class="review-dialog"><form method="post" action="review-submit.php" enctype="multipart/form-data"><button class="review-close" type="button">×</button><div class="eyebrow">Chia sẻ trải nghiệm</div><h2>Viết review</h2><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><label>Tên của bạn *<input required name="customer_name"></label><label>Đánh giá *<select name="rating"><option value="5">5 sao</option><option value="4">4 sao</option><option value="3">3 sao</option><option value="2">2 sao</option><option value="1">1 sao</option></select></label><label>Nội dung review *<textarea required name="content" rows="5"></textarea></label><label>Ảnh thực tế <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple></label><p>Review sẽ hiển thị sau khi được IMA PRIME duyệt.</p><button class="button">Gửi review</button></form></dialog>

  <section class="container section product-accessories"><div class="section-head"><div><div class="eyebrow">Hoàn thiện không gian</div><h2>Sản phẩm mua kèm</h2></div><a class="under" href="category.php">Xem tất cả sản phẩm →</a></div><div class="products"><?php foreach ($accessories as $item): ?><a class="product-card" href="product.php?slug=<?= urlencode($item['slug']) ?>"><div class="product-image"><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"><span class="badge <?= $item['fast'] ? '' : 'order' ?>"><?= htmlspecialchars($item['status']) ?></span></div><div class="product-text"><div class="product-title"><h3><?= htmlspecialchars($item['name']) ?></h3><span class="rating">★ <?= htmlspecialchars($item['rating']) ?></span></div><p><?= htmlspecialchars($item['description']) ?></p><b class="price"><?= money($item['price']) ?></b></div></a><?php endforeach; ?></div></section>
</main>

<div class="sticky-cta"><div class="sticky-content"><div><b><?= htmlspecialchars($p['name']) ?> · <span data-client-size-name></span></b><br><small data-client-color-sticky></small></div><strong class="price" data-client-price><?= money($p['price']) ?></strong><div class="actions"><a class="button" href="https://zalo.me/0934430111">Nhắn Zalo tư vấn</a><button class="button outline-dark" type="button" data-add-cart>Cho vào giỏ hàng</button></div></div></div>
<script>window.primeVariants=<?= json_encode($variants, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;window.primeFallbackColors=<?= json_encode($defaultColors, JSON_UNESCAPED_UNICODE) ?>;window.primeBasePrice=<?= json_encode((float)$p['price']) ?>;</script>
<?php footer_page(); ?>
