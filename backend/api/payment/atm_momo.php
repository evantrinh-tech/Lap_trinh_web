<?php
declare(strict_types=1);
header('Content-Type: application/json');
 
// =============================================
// CẤU HÌNH
// =============================================
$endpoint    = "https://test-payment.momo.vn/v2/gateway/api/create";
// Production: "https://payment.momo.vn/v2/gateway/api/create"
 
$partnerCode = "MOMOBKUN20180529";
$accessKey   = "klm05TvNBzhg7h7j";
$secretKey   = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
 
$redirectUrl = "http://localhost:8000/order-success.html";
// Production: "https://yourdomain.com/checkout-success.php"
 
$ipnUrl      = "http://localhost/backend/payment/momo_ipn.php";
// Production: "https://yourdomain.com/backend/payment/momo_ipn.php"
 
// =============================================
// NHẬN DỮ LIỆU ĐẦU VÀO
// =============================================
$orderId   = $_POST['orderId']   ?? (string)time();
$amount    = (int)($_POST['amount']  ?? 0);
$orderInfo = $_POST['orderInfo'] ?? "Thanh toán Kính Xanh";
 
// =============================================
// KIỂM TRA ĐẦU VÀO
// =============================================
if ($amount < 1000) {
    echo json_encode(["success" => false, "message" => "Số tiền tối thiểu 1.000 VNĐ"]);
    exit;
}
if (empty(trim($orderId))) {
    echo json_encode(["success" => false, "message" => "Thiếu orderId"]);
    exit;
}
 
// =============================================
// TẠO CHỮ KÝ
// =============================================
$requestId   = (string)time();
$requestType = "payWithATM";   // ATM nội địa
$extraData   = "";
 
$rawHash =
    "accessKey="    . $accessKey   .
    "&amount="      . $amount      .
    "&extraData="   . $extraData   .
    "&ipnUrl="      . $ipnUrl      .
    "&orderId="     . $orderId     .
    "&orderInfo="   . $orderInfo   .
    "&partnerCode=" . $partnerCode .
    "&redirectUrl=" . $redirectUrl .
    "&requestId="   . $requestId   .
    "&requestType=" . $requestType;
 
$signature = hash_hmac("sha256", $rawHash, $secretKey);
 
// =============================================
// PAYLOAD
// =============================================
$payload = [
    "partnerCode" => $partnerCode,
    "partnerName" => "KinhXanh Store",
    "storeId"     => "KX_STORE_01",
    "requestId"   => $requestId,
    "amount"      => $amount,
    "orderId"     => $orderId,
    "orderInfo"   => $orderInfo,
    "redirectUrl" => $redirectUrl,
    "ipnUrl"      => $ipnUrl,
    "lang"        => "vi",
    "extraData"   => $extraData,
    "requestType" => $requestType,
    "signature"   => $signature,
];
 
// =============================================
// GỌI API MOMO
// =============================================
function execPostRequest(string $url, string $data): string {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        return json_encode(["error" => true, "message" => $err]);
    }
    curl_close($ch);
    return $result;
}
 
$result     = execPostRequest($endpoint, json_encode($payload));
$jsonResult = json_decode($result, true);
 
// =============================================
// TRẢ VỀ KẾT QUẢ
// =============================================
if (!empty($jsonResult['payUrl'])) {
    echo json_encode([
        "success" => true,
        "payUrl"  => $jsonResult['payUrl'],
        "orderId" => $orderId,
        "amount"  => $amount,
    ]);
} else {
    $resultCode = $jsonResult['resultCode'] ?? 'N/A';
    $message    = $jsonResult['message']    ?? 'Lỗi không xác định';
    echo json_encode([
        "success"    => false,
        "resultCode" => $resultCode,
        "message"    => "MoMo lỗi [$resultCode]: $message",
        "raw"        => $jsonResult,
    ]);
}