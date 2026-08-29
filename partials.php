<?php
function prime_asset_version(): string {
    return substr(md5_file(__DIR__ . '/assets/style.css')
        . md5_file(__DIR__ . '/assets/app.js')
        . md5_file(__DIR__ . '/assets/product-swiper.js'), 0, 16);
}

function header_page(string $title = 'IMA PRIME'): void {
    $assetVersion = prime_asset_version(); ?>
<!doctype html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars($title)?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600&family=Newsreader:opsz,wght@6..72,200;6..72,300;6..72,400;6..72,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/vendor/swiper-bundle.min.css?v=<?=$assetVersion?>"><link rel="stylesheet" href="assets/style.css?v=<?=$assetVersion?>"></head><body>
<div class="topbar">Hàng dựng sẵn tại xưởng — giao Hà Nội trong 2–3 ngày · Miễn phí vận chuyển & lắp đặt nội thành</div>
<header class="header"><a class="brand" href="index.php">IMA <em>PRIME</em></a><button class="menu-toggle" aria-label="Mở menu">☰</button><nav><a href="category.php">Sản phẩm</a><a href="fabric-library.php">Thư viện vải</a><a href="about.php">Về chúng tôi</a></nav><a class="zalo small" href="https://zalo.me/0934430111">Nhắn Zalo</a><button class="header-cart" type="button" data-cart-open aria-label="Giỏ hàng"><i class="bi bi-bag"></i><span data-cart-count>0</span></button></header><div class="cart-modal" data-cart-modal hidden><div class="cart-modal-backdrop" data-cart-close></div><section class="cart-modal-panel" role="dialog" aria-modal="true" aria-label="Giỏ hàng"><button class="cart-modal-close" data-cart-close aria-label="Đóng">×</button><div class="eyebrow">Giỏ hàng</div><h2>Sản phẩm bạn đang chọn</h2><div data-cart-preview></div><a class="button" href="cart.php">Đi đến giỏ hàng</a></section></div>
<?php }

function prime_dev_mode(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, ['127.0.0.1', '::1'], true) || isset($_GET['annotate']);
}

function footer_page(): void {
    $assetVersion = prime_asset_version(); ?>
<footer><div class="footer-grid"><div><a class="brand" href="index.php">IMA <em>PRIME</em></a><p>Sofa điện và sofa thông minh, dựng sẵn tại xưởng Việt Nam.</p><a class="under" href="https://ima.vn">Xem dòng da thật IMASOFA →</a></div><div><b>Sản phẩm</b><a href="category.php">Sofa điện & thông minh</a><a href="category.php">Sofa chỉnh điện</a><a href="category.php">Sofa modular / góc</a><a href="category.php?type=bed">Giường nâng điện</a></div><div><b>Hỗ trợ</b><a href="policies.php#warranty">Chính sách bảo hành</a><a href="policies.php#delivery">Vận chuyển & giao hàng</a><a href="fabric-library.php">Thư viện vải/màu</a><a href="showroom.php">Showroom & liên hệ</a></div><div><b>Liên hệ</b><a href="tel:0934430111">0934 430 111</a><span>201 Trường Chinh, Hà Nội</span><span>9h00 – 18h00, hàng ngày</span><a href="https://zalo.me/0934430111">Zalo OA</a></div></div><div class="copyright">© 2026 IMA PRIME. Giá niêm yết áp dụng chung cho online và đại lý.<span>Sản xuất tại Việt Nam · Linh kiện điện nhập khẩu kiểm định</span></div></footer>
<script src="assets/vendor/swiper-bundle.min.js?v=<?=$assetVersion?>"></script><script src="assets/vendor/fslightbox.js?v=<?=$assetVersion?>"></script><script src="assets/app.js?v=<?=$assetVersion?>"></script><script src="assets/product-swiper.js?v=<?=$assetVersion?>"></script>
<?php if (prime_dev_mode()): ?>
<script type="importmap">
{"imports":{"react":"https://esm.sh/react@18.3.1","react/jsx-runtime":"https://esm.sh/react@18.3.1/jsx-runtime","react-dom":"https://esm.sh/react-dom@18.3.1","react-dom/client":"https://esm.sh/react-dom@18.3.1/client","agentation":"https://esm.sh/agentation@3.0.2?external=react,react-dom"}}
</script>
<script type="module">
import React from 'react';
import { createRoot } from 'react-dom/client';
import { Agentation } from 'agentation';
const mount = document.createElement('div');
document.body.appendChild(mount);
createRoot(mount).render(React.createElement(Agentation));
</script>
<?php endif; ?>
</body></html>
<?php }
