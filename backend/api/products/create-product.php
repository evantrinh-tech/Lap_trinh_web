<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    jsonResponse(['success' => true, 'message' => 'Preflight OK']);
}

applyDDoSProtection(rateLimit: 30, rateMinutes: 1, endpoint: 'products_create');

// Chỉ Admin mới được thêm sản phẩm
$userId  = requireAuth();
$userOid = toObjectId($userId);
if (!$userOid) {
    jsonResponse(['success' => false, 'message' => 'Token không hợp lệ.'], 401);
}

$users = getCollection('users');
$user  = $users->findOne(['_id' => $userOid]);
if (!$user || ($user['role_id'] ?? null) != 1) {
    jsonResponse(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    methodNotAllowed('POST, OPTIONS');
}

/* =====================================================
   THƯ MỤC LƯU ẢNH
===================================================== */
$uploadDir = __DIR__ . '/../../../uploads/products/';
error_log('uploadDir = ' . $uploadDir);
$uploadUrl = '/uploads/products/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function uploadImage(array $file, string $dir, string $baseUrl): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null; // tối đa 5MB

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        default      => 'jpg',
    };

    $filename = uniqid('prod_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) return null;

    return $baseUrl . $filename;
}

/* =====================================================
   LẤY DATA JSON TỪ FIELD "data"
===================================================== */
$rawData = $_POST['data'] ?? null;
if (!$rawData) {
    jsonResponse(['success' => false, 'message' => 'Thiếu trường data (JSON).'], 400);
}

$input = json_decode($rawData, true);
if (!is_array($input)) {
    jsonResponse(['success' => false, 'message' => 'Dữ liệu JSON không hợp lệ.'], 400);
}

// Validate bắt buộc
foreach (['name', 'sku', 'slug', 'brand', 'price'] as $field) {
    if (empty($input[$field])) {
        jsonResponse(['success' => false, 'message' => "Thiếu trường bắt buộc: $field"], 422);
    }
}

/* =====================================================
   XỬ LÝ UPLOAD ẢNH TỪ files[]
===================================================== */
$uploadedImages = [];

if (!empty($_FILES['files'])) {
    $files = $_FILES['files'];

    // Chuẩn hóa về array nếu chỉ có 1 file
    if (!is_array($files['name'])) {
        $files = array_map(fn($v) => [$v], $files);
    }

    foreach (array_keys($files['name']) as $i) {
        $url = uploadImage([
            'name'     => $files['name'][$i],
            'type'     => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error'    => $files['error'][$i],
            'size'     => $files['size'][$i],
        ], $uploadDir, $uploadUrl);

        if ($url) $uploadedImages[] = $url;
    }
}

$images       = !empty($uploadedImages) ? $uploadedImages : (array) ($input['images'] ?? []);
$thumbnailUrl = $images[0] ?? '';

if (empty($thumbnailUrl)) {
    jsonResponse(['success' => false, 'message' => 'Cần ít nhất 1 hình ảnh.'], 422);
}

/* =====================================================
   LƯU VÀO MONGODB
===================================================== */
try {
    $products = getCollection('products');
    $variants = getCollection('product_variants');

    // Kiểm tra trùng SKU hoặc slug
    $existing = $products->findOne([
        '$or' => [
            ['sku'  => trim($input['sku'])],
            ['slug' => trim($input['slug'])],
        ],
    ]);
    if ($existing) {
        jsonResponse(['success' => false, 'message' => 'SKU hoặc slug đã tồn tại.'], 409);
    }

    $now = new MongoDB\BSON\UTCDateTime();

    // Insert product
    $result    = $products->insertOne([
        'sku'               => trim($input['sku']),
        'slug'              => trim($input['slug']),
        'name'              => trim($input['name']),
        'brand'             => trim($input['brand']             ?? ''),
        'category'          => trim($input['category']          ?? ''),
        'gender'            => trim($input['gender']            ?? 'unisex'),
        'frame_material'    => trim($input['frame_material']    ?? ''),
        'lens_type'         => trim($input['lens_type']         ?? ''),
        'price'             => (float) ($input['price']         ?? 0),
        'old_price'         => !empty($input['old_price'])
                                ? (float) $input['old_price'] : null,
        'discount_percent'  => (int)   ($input['discount_percent']  ?? 0),
        'badge'             => trim($input['badge']             ?? ''),
        'short_description' => trim($input['short_description'] ?? ''),
        'description'       => trim($input['description']       ?? ''),
        'thumbnail_url'     => $thumbnailUrl,
        'images'            => $images,
        'is_active'         => (bool) ($input['is_active']      ?? true),
        'is_featured'       => (bool) ($input['is_featured']    ?? false),
        'rating'            => 0.0,
        'review_count'      => 0,
        'sold_count'        => 0,
        'created_at'        => $now,
        'updated_at'        => $now,
    ]);

    $productId  = $result->getInsertedId();
    $variantIds = [];

    // Insert variants (màu sắc)
    if (!empty($input['variants']) && is_array($input['variants'])) {
        foreach ($input['variants'] as $v) {
            $vResult      = $variants->insertOne([
                'product_id' => $productId,
                'color'      => trim($v['color']     ?? ''),
                'color_hex'  => trim($v['color_hex'] ?? '#000000'),
                'size'       => trim($v['size']      ?? ''),
                'price'      => (float) ($input['price'] ?? 0),
                'stock'      => (int)   ($v['stock']     ?? 0),
                'created_at' => $now,
            ]);
            $variantIds[] = (string) $vResult->getInsertedId();
        }
    }

    jsonResponse([
        'success' => true,
        'message' => 'Thêm sản phẩm thành công.',
        'data'    => [
            'product_id'  => (string) $productId,
            'variant_ids' => $variantIds,
            'images'      => $images,
        ],
    ], 201);

} catch (\Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Lỗi server khi lưu sản phẩm.',
        'error'   => $e->getMessage(),
    ], 500);
}