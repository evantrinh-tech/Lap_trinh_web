<?php
declare(strict_types=1);
header('Content-Type: application/json');

// =============================================
// CẤU HÌNH
// =============================================
$accessKey = "klm05TvNBzhg7h7j";
$secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";

// =============================================
// NHẬN DỮ LIỆU TỪ MOMO (JSON body)
// =============================================
$rawBody = file_get_contents('php://input');
$ipn     = json_decode($rawBody, true);

if (!$ipn) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid JSON"]);
    exit;
}

// =============================================
// XÁC THỰC CHỮ KÝ — BẮT BUỘC
// Tránh giả mạo callback từ bên ngoài
// =============================================
$receivedSignature = $ipn['signature'] ?? '';

$rawHash =
    "accessKey="     . ($ipn['accessKey']     ?? '') .
    "&amount="       . ($ipn['amount']        ?? '') .
    "&extraData="    . ($ipn['extraData']      ?? '') .
    "&message="      . ($ipn['message']        ?? '') .
    "&orderId="      . ($ipn['orderId']        ?? '') .
    "&orderInfo="    . ($ipn['orderInfo']      ?? '') .
    "&orderType="    . ($ipn['orderType']      ?? '') .
    "&partnerCode="  . ($ipn['partnerCode']    ?? '') .
    "&payType="      . ($ipn['payType']        ?? '') .
    "&requestId="    . ($ipn['requestId']      ?? '') .
    "&responseTime=" . ($ipn['responseTime']   ?? '') .
    "&resultCode="   . ($ipn['resultCode']     ?? '') .
    "&transId="      . ($ipn['transId']        ?? '');

$expectedSignature = hash_hmac("sha256", $rawHash, $secretKey);

if (!hash_equals($expectedSignature, $receivedSignature)) {
    http_response_code(400);
    echo json_encode(["message" => "Signature mismatch"]);
    exit;
}

// =============================================
// XỬ LÝ KẾT QUẢ
// =============================================
$resultCode = (int)($ipn['resultCode'] ?? -1);
$orderId    = $ipn['orderId']  ?? '';
$amount     = (int)($ipn['amount'] ?? 0);
$transId    = $ipn['transId']  ?? '';

if ($resultCode === 0) {
    // -----------------------------------------------
    // THANH TOÁN THÀNH CÔNG
    // TODO: Cập nhật trạng thái đơn hàng trong DB
    // Ví dụ:
    // updateOrderStatus($orderId, 'paid', $transId);
    // -----------------------------------------------
    error_log("[MoMo IPN] SUCCESS - orderId: $orderId | amount: $amount | transId: $transId");

    echo json_encode(["message" => "Success"]);
} else {
    // -----------------------------------------------
    // THANH TOÁN THẤT BẠI / HỦY
    // TODO: Cập nhật trạng thái đơn hàng → failed/cancelled
    // -----------------------------------------------
    $message = $ipn['message'] ?? 'Unknown error';
    error_log("[MoMo IPN] FAILED - orderId: $orderId | resultCode: $resultCode | message: $message");

    echo json_encode(["message" => "Failed"]);
}