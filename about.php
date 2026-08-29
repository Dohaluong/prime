<?php
require_once __DIR__.'/data.php';
require_once __DIR__.'/partials.php';
$about = about_content();

function about_image_block(string $url, string $alt, string $ratio, string $extraStyle = ''): void {
    if ($url !== '') {
        echo '<div class="ph-image" style="aspect-ratio:'.$ratio.';'.$extraStyle.'"><img src="'.htmlspecialchars($url).'" alt="'.htmlspecialchars($alt).'"></div>';
    } else {
        echo '<div class="ph-image" style="aspect-ratio:'.$ratio.';background:#EBE5D9;'.$extraStyle.'"><div class="ph-caption"><i class="bi bi-image"></i><span>'.htmlspecialchars($alt).'</span></div></div>';
    }
}

header_page('Về chúng tôi | IMA PRIME');
?>
<main class="about-page">
  <section class="page-hero container"><h1><?= htmlspecialchars($about['hero_title']) ?></h1></section>

  <section class="container about-hero-image">
    <?php about_image_block($about['hero_image'], 'Ảnh xưởng hoặc showroom 201 Trường Chinh', '21/9'); ?>
  </section>

  <section class="about-narrow about-lede"><?= $about['lede_html'] ?></section>

  <section class="container about-duo">
    <div class="about-duo-grid">
      <?php about_image_block($about['duo_image_1'], 'Ảnh xưởng: dựng khung sofa', '4/3'); ?>
      <?php about_image_block($about['duo_image_2'], 'Ảnh cận: công đoạn bọc vải', '4/3'); ?>
    </div>
  </section>

  <section class="about-narrow about-section">
    <h2><?= htmlspecialchars($about['section1_heading']) ?></h2>
    <?= $about['section1_html'] ?>
  </section>

  <section class="container about-stats">
    <div class="about-stats-grid">
      <?php foreach ($about['stats'] as $stat): ?>
      <div class="about-stat"><b><?= htmlspecialchars($stat['number'] ?? '') ?></b><span><?= htmlspecialchars($stat['label'] ?? '') ?></span></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="about-narrow about-section">
    <h2><?= htmlspecialchars($about['section2_heading']) ?></h2>
    <?= $about['section2_html'] ?>
  </section>

  <section class="container about-trio">
    <div class="about-trio-grid">
      <?php about_image_block($about['trio_image_1'], 'Showroom 201 Trường Chinh', '1'); ?>
      <?php about_image_block($about['trio_image_2'], 'Đội giao hàng lắp đặt tại nhà khách', '1'); ?>
      <?php about_image_block($about['trio_image_3'], 'Quay video demo cơ chế điện', '1'); ?>
    </div>
  </section>

  <section class="container about-cta">
    <div class="about-cta-inner">
      <div><h2><?= htmlspecialchars($about['cta_heading']) ?></h2><p><?= htmlspecialchars($about['cta_text']) ?></p></div>
      <div class="about-cta-actions">
        <a class="button light" href="https://zalo.me/0934430111">Nhắn Zalo tư vấn</a>
        <a class="button outline" href="showroom.php">Xem showroom</a>
      </div>
    </div>
  </section>
</main>
<?php footer_page(); ?>
