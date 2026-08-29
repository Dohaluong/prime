<?php
require '_layout.php';
require_once __DIR__.'/../data.php';
$about = about_content();

function about_admin_image_field(string $name, string $label, string $url, string $hint = ''): void {
    echo '<div class="field about-image-field"><label>'.htmlspecialchars($label).'</label>';
    echo '<div class="about-image-preview">';
    if ($url !== '') echo '<img src="'.htmlspecialchars($url).'" alt="">';
    else echo '<span class="about-image-empty"><i class="bi bi-image"></i></span>';
    echo '</div>';
    echo '<label class="upload-zone" for="'.$name.'_file"><i class="bi bi-cloud-arrow-up"></i><b>Thay ảnh</b><input id="'.$name.'_file" name="'.$name.'_file" type="file" accept="image/jpeg,image/png,image/webp"></label>';
    if ($hint !== '') echo '<small>'.htmlspecialchars($hint).'</small>';
    echo '</div>';
}

admin_header('Trang giới thiệu', 'about'); admin_flash(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<section class="form-page">
<div class="form-heading"><div><h2>Trang giới thiệu</h2><p>Quản lý nội dung và ảnh hiển thị trên trang <code>/about.php</code>. Ảnh chưa tải lên sẽ hiển thị ảnh mẫu.</p></div></div>
<?php if (!db()): ?><div class="notice error">MySQL chưa kết nối. Không thể lưu dữ liệu.</div><?php endif; ?>
<form class="product-form about-content-form" method="post" action="about-save.php" enctype="multipart/form-data">
<div class="form-main">

<section class="form-card">
  <h3>Tiêu đề & ảnh banner</h3>
  <div class="field"><label>Tiêu đề trang (H1)</label><input name="hero_title" value="<?= htmlspecialchars($about['hero_title']) ?>"></div>
  <?php about_admin_image_field('hero_image', 'Ảnh banner ngang', $about['hero_image'], 'Tỉ lệ đề xuất 21:9, ảnh rộng ≥2400px.'); ?>
</section>

<section class="form-card content-card">
  <div class="content-card-head"><h3>Đoạn mở đầu</h3></div>
  <div class="field"><textarea class="rich-editor" name="lede_html"><?= $about['lede_html'] ?></textarea></div>
</section>

<section class="form-card">
  <h3>Cặp ảnh minh hoạ #1</h3>
  <div class="two-fields">
    <?php about_admin_image_field('duo_image_1', 'Ảnh trái', $about['duo_image_1'], 'Tỉ lệ đề xuất 4:3.'); ?>
    <?php about_admin_image_field('duo_image_2', 'Ảnh phải', $about['duo_image_2'], 'Tỉ lệ đề xuất 4:3.'); ?>
  </div>
</section>

<section class="form-card content-card">
  <div class="content-card-head"><h3>Nội dung mục 1</h3></div>
  <div class="field"><label>Tiêu đề mục</label><input name="section1_heading" value="<?= htmlspecialchars($about['section1_heading']) ?>"></div>
  <div class="field"><textarea class="rich-editor" name="section1_html"><?= $about['section1_html'] ?></textarea></div>
</section>

<section class="form-card">
  <div class="content-card-head"><h3>Số liệu nổi bật</h3><button type="button" class="admin-button outline-variant" data-stat-add><i class="bi bi-plus-lg"></i> Thêm số liệu</button></div>
  <table class="spec-table">
    <thead><tr><th>Số liệu</th><th>Mô tả</th><th></th></tr></thead>
    <tbody data-stat-rows>
      <?php foreach ($about['stats'] as $stat): ?>
      <tr><td><input type="text" name="stat_number[]" value="<?= htmlspecialchars($stat['number'] ?? '') ?>" placeholder="Ví dụ: 10 năm"></td><td><input type="text" name="stat_label[]" value="<?= htmlspecialchars($stat['label'] ?? '') ?>" placeholder="Ví dụ: bảo hành khung"></td><td><button type="button" class="spec-remove" aria-label="Xoá dòng"><i class="bi bi-trash"></i></button></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section class="form-card content-card">
  <div class="content-card-head"><h3>Nội dung mục 2</h3></div>
  <div class="field"><label>Tiêu đề mục</label><input name="section2_heading" value="<?= htmlspecialchars($about['section2_heading']) ?>"></div>
  <div class="field"><textarea class="rich-editor" name="section2_html"><?= $about['section2_html'] ?></textarea></div>
</section>

<section class="form-card">
  <h3>Bộ 3 ảnh minh hoạ</h3>
  <div class="about-trio-fields">
    <?php about_admin_image_field('trio_image_1', 'Ảnh 1', $about['trio_image_1'], 'Tỉ lệ đề xuất 1:1.'); ?>
    <?php about_admin_image_field('trio_image_2', 'Ảnh 2', $about['trio_image_2'], 'Tỉ lệ đề xuất 1:1.'); ?>
    <?php about_admin_image_field('trio_image_3', 'Ảnh 3', $about['trio_image_3'], 'Tỉ lệ đề xuất 1:1.'); ?>
  </div>
</section>

<section class="form-card">
  <h3>Khối kêu gọi hành động (cuối trang)</h3>
  <div class="field"><label>Tiêu đề CTA</label><input name="cta_heading" value="<?= htmlspecialchars($about['cta_heading']) ?>"></div>
  <div class="field"><label>Mô tả CTA</label><textarea name="cta_text" rows="3"><?= htmlspecialchars($about['cta_text']) ?></textarea></div>
</section>

</div>
<aside class="form-side">
  <section class="form-card">
    <button class="admin-button save" <?= db() ? '' : 'disabled' ?>>Lưu nội dung</button>
    <a class="cancel" href="../about.php" target="_blank">Xem trang →</a>
  </section>
</aside>
</form>
</section>
<?php admin_footer(); ?>
