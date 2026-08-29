<?php
require_once __DIR__.'/auth.php'; require_admin();
$pdo = db(); if (!$pdo) { header('Location: about-form.php?error='.urlencode('Chưa kết nối được MySQL')); exit; }

function about_upload(string $field): ?string {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($_FILES[$field]['error'] ?? 0) !== UPLOAD_ERR_OK || ($_FILES[$field]['size'] ?? 0) > 10485760) return null;
    $tmp = $_FILES[$field]['tmp_name'];
    if (!is_uploaded_file($tmp)) return null;
    $ok = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    if (!isset($ok[$mime])) return null;
    $dir = __DIR__.'/../uploads/about';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) return null;
    $name = bin2hex(random_bytes(16)).'.'.$ok[$mime];
    if (!move_uploaded_file($tmp, $dir.'/'.$name)) return null;
    return '/Prime-2/uploads/about/'.$name;
}

try {
    $current = $pdo->query('SELECT * FROM about_content WHERE id=1')->fetch(PDO::FETCH_ASSOC) ?: [];

    $imageFields = ['hero_image', 'duo_image_1', 'duo_image_2', 'trio_image_1', 'trio_image_2', 'trio_image_3'];
    $data = [];
    foreach ($imageFields as $f) {
        $uploaded = about_upload($f.'_file');
        $data[$f] = $uploaded ?? ($current[$f] ?? '');
    }

    $textFields = ['hero_title', 'lede_html', 'section1_heading', 'section1_html', 'section2_heading', 'section2_html', 'cta_heading', 'cta_text'];
    foreach ($textFields as $f) $data[$f] = (string)($_POST[$f] ?? '');

    $numbers = $_POST['stat_number'] ?? [];
    $labels = $_POST['stat_label'] ?? [];
    $stats = [];
    foreach ($numbers as $i => $number) {
        $number = trim((string)$number);
        $label = trim((string)($labels[$i] ?? ''));
        if ($number !== '' || $label !== '') $stats[] = ['number' => $number, 'label' => $label];
    }
    $data['stats_json'] = json_encode($stats, JSON_UNESCAPED_UNICODE);

    $exists = (int)$pdo->query('SELECT COUNT(*) FROM about_content WHERE id=1')->fetchColumn();
    $cols = 'hero_title=:hero_title,hero_image=:hero_image,lede_html=:lede_html,duo_image_1=:duo_image_1,duo_image_2=:duo_image_2,section1_heading=:section1_heading,section1_html=:section1_html,stats_json=:stats_json,section2_heading=:section2_heading,section2_html=:section2_html,trio_image_1=:trio_image_1,trio_image_2=:trio_image_2,trio_image_3=:trio_image_3,cta_heading=:cta_heading,cta_text=:cta_text';
    if ($exists) {
        $pdo->prepare('UPDATE about_content SET '.$cols.' WHERE id=1')->execute($data);
    } else {
        $data['id'] = 1;
        $pdo->prepare('INSERT INTO about_content (id,hero_title,hero_image,lede_html,duo_image_1,duo_image_2,section1_heading,section1_html,stats_json,section2_heading,section2_html,trio_image_1,trio_image_2,trio_image_3,cta_heading,cta_text) VALUES (:id,:hero_title,:hero_image,:lede_html,:duo_image_1,:duo_image_2,:section1_heading,:section1_html,:stats_json,:section2_heading,:section2_html,:trio_image_1,:trio_image_2,:trio_image_3,:cta_heading,:cta_text)')->execute($data);
    }
    header('Location: about-form.php?ok='.urlencode('Đã lưu nội dung trang giới thiệu'));
} catch (Throwable $e) {
    header('Location: about-form.php?error='.urlencode('Không thể lưu nội dung'));
}
