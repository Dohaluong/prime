<?php
require_once __DIR__.'/auth.php';
require_admin();

function admin_header(string $title, string $active = 'dashboard'): void {
    $user = current_admin();
    $items = [
        'dashboard' => ['index.php', 'bi-grid-1x2', 'Tổng quan'],
        'products' => ['products.php', 'bi-box-seam', 'Sản phẩm'],
        'orders' => ['orders.php', 'bi-receipt', 'Đơn hàng'],
        'categories' => ['categories.php', 'bi-tags', 'Category'],
        'collections' => ['collections.php', 'bi-collection', 'Collection'],
        'materials' => ['materials.php', 'bi-layers', 'Chất liệu'],
        'reviews' => ['reviews.php', 'bi-star', 'Đánh giá'],
        'about' => ['about-form.php', 'bi-info-circle', 'Trang giới thiệu'],
        'users' => ['users.php', 'bi-people', 'Người dùng'],
    ];
    ?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars($title)?> · IMA PRIME Admin</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+SC:wght@300;400;500;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="assets/admin.css"></head><body><div class="admin-shell"><aside class="sidebar"><a class="admin-brand" href="index.php">IMA <em>PRIME</em><small>ADMIN</small></a><nav class="side-nav"><?php foreach ($items as $key => [$url, $icon, $label]): ?><a class="<?=$active === $key ? 'active' : ''?>" href="<?=$url?>"><i class="bi <?=$icon?>"></i><?=$label?></a><?php endforeach; ?></nav><div class="sidebar-bottom"><a href="../index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Xem website</a><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a><span>IMA PRIME · 2026</span></div></aside><main class="admin-main"><header class="admin-top"><button class="sidebar-toggle" aria-label="Mở menu"><i class="bi bi-list"></i></button><div><p>Quản trị nội dung</p><h1><?=htmlspecialchars($title)?></h1></div><div class="admin-user"><span><?=strtoupper(substr($user['name'] ?? 'A', 0, 1))?></span><div><b><?=htmlspecialchars($user['name'] ?? 'Quản trị viên')?></b><small><?=htmlspecialchars($user['role'] ?? 'admin')?></small></div></div></header>
<?php
}

function admin_footer(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = in_array($ip, ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)
        || preg_match('/^(localhost|127\.0\.0\.1|\[::1\])(?::\d+)?$/i', $host)
        || isset($_GET['annotate']);
    $assetVersion = (string) filemtime(__DIR__ . '/assets/admin.js');
    echo '</main></div><script src="assets/admin.js?v='.$assetVersion.'"></script>';
    if ($isLocal) {
        echo <<<'HTML'
<script type="module">
(async () => {
  try {
    const reactUrl = 'https://esm.sh/react@18';
    const reactDomUrl = 'https://esm.sh/react-dom@18/client';
    const agentationUrl = 'https://esm.sh/agentation@3?alias=react:' + reactUrl;
    const React = await import(reactUrl);
    const ReactDOM = await import(reactDomUrl);
    const agentation = await import(agentationUrl);
    const mount = document.createElement('div');
    mount.id = 'agentation-root';
    document.body.appendChild(mount);
    ReactDOM.createRoot(mount).render(React.createElement(agentation.Agentation, {reactComponents: false}));
  } catch (error) {
    console.error('Không thể khởi tạo Agentation:', error);
  }
})();
</script>
HTML;
    }
    echo '</body></html>';
}

function admin_flash(): void {
    if (!empty($_GET['ok'])) echo '<div class="notice success"><i class="bi bi-check-circle"></i> '.htmlspecialchars($_GET['ok']).'</div>';
    if (!empty($_GET['error'])) echo '<div class="notice error"><i class="bi bi-exclamation-circle"></i> '.htmlspecialchars($_GET['error']).'</div>';
}
