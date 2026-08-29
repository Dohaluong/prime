<?php
require_once __DIR__.'/auth.php'; require_admin();
header('Content-Type: application/json; charset=utf-8');

function ai_fail(string $message): never {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

$apiKey = prime_env('GPT_API_KEY');
if ($apiKey === '') ai_fail('Chưa cấu hình GPT_API_KEY trong design/.env');

$name = trim((string)($_POST['name'] ?? ''));
$type = trim((string)($_POST['type'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$price = trim((string)($_POST['price'] ?? ''));
if ($name === '') ai_fail('Cần nhập tên sản phẩm trước khi tạo nội dung bằng AI.');

$priceLabel = $price !== '' && is_numeric($price) ? number_format((float)$price, 0, ',', '.').'đ' : 'chưa rõ';

$system = 'Bạn là chuyên gia copywriting nội thất cho thương hiệu sofa điện cao cấp IMA PRIME tại Việt Nam. '
    .'Viết nội dung tiếng Việt tự nhiên, chuyên nghiệp, không sáo rỗng, không bịa số liệu kỹ thuật chính xác tuyệt đối '
    .'(dùng các giá trị hợp lý, mang tính minh hoạ cho loại sản phẩm này). '
    .'Luôn trả lời bằng JSON hợp lệ theo đúng schema được yêu cầu, không thêm text ngoài JSON.';

$user = "Sản phẩm: {$name}\nDanh mục: {$type}\nMô tả ngắn hiện có: {$description}\nGiá niêm yết: {$priceLabel}\n\n"
    ."Hãy tạo:\n"
    ."1) \"detailed_description\": đoạn mô tả chi tiết dạng HTML thuần (chỉ dùng các thẻ h3, p, ul, li, strong — KHÔNG dùng html/head/body, KHÔNG dùng style hay class), khoảng 3-5 đoạn/mục, giọng văn thuyết phục, nêu bật chất liệu, công năng, trải nghiệm sử dụng.\n"
    ."2) \"specifications\": mảng 8-12 object {\"label\":..,\"value\":..} là các thông số kỹ thuật tiếng Việt phù hợp với loại sản phẩm này (kích thước, chất liệu khung/đệm, motor điện nếu là sofa điện, trọng lượng, số chỗ ngồi, phụ kiện đi kèm, bảo hành, lắp đặt...).";

$payload = json_encode([
    'model' => prime_env('GPT_MODEL', 'gpt-4o-mini'),
    'messages' => [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user', 'content' => $user],
    ],
    'response_format' => ['type' => 'json_object'],
    'temperature' => 0.7,
], JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer '.$apiKey],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 60,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) ai_fail('Không kết nối được tới OpenAI: '.$curlError);

$body = json_decode($response, true);
if ($httpCode >= 400) {
    ai_fail('OpenAI báo lỗi: '.($body['error']['message'] ?? 'HTTP '.$httpCode));
}

$content = $body['choices'][0]['message']['content'] ?? '';
$parsed = json_decode($content, true);
if (!is_array($parsed) || !isset($parsed['detailed_description'])) {
    ai_fail('AI trả về nội dung không đúng định dạng, hãy thử lại.');
}

$specs = [];
foreach ((array)($parsed['specifications'] ?? []) as $row) {
    $label = trim((string)($row['label'] ?? ''));
    $value = trim((string)($row['value'] ?? ''));
    if ($label !== '' || $value !== '') $specs[] = ['label' => $label, 'value' => $value];
}

echo json_encode([
    'ok' => true,
    'detailed_description' => (string)$parsed['detailed_description'],
    'specifications' => $specs,
], JSON_UNESCAPED_UNICODE);
