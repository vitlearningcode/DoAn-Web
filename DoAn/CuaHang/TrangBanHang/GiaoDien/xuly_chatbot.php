<?php
// 1. Tắt hiển thị lỗi mặc định của PHP để không hỏng JSON
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// 2. Nhúng file kết nối
include_once "../../../KetNoi/config/db.php"; 

if (!isset($pdo)) {
    echo json_encode(["error" => "Không tìm thấy kết nối PDO. Kiểm tra lại db.php"]);
    exit;
}

try {
    // 3. Lấy dữ liệu sách
    $sql = "SELECT tenSach, moTa, giaBan FROM Sach WHERE trangThai = 'DangKD' LIMIT 15";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $danhSachSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $context_sach = "Dưới đây là danh sách sách hiện có tại cửa hàng:\n";
    foreach ($danhSachSach as $sach) {
        // Xử lý chuỗi để đảm bảo không bị lỗi mã hóa UTF-8
        $tenSach = htmlspecialchars($sach['tenSach'], ENT_QUOTES, 'UTF-8');
        $moTa = $sach['moTa'] ? htmlspecialchars($sach['moTa'], ENT_QUOTES, 'UTF-8') : 'Đang cập nhật';
        $gia = number_format((float)$sach['giaBan']);
        $context_sach .= "- {$tenSach}: {$moTa} (Giá: {$gia}đ)\n";
    }

    // 4. Nhận dữ liệu từ JavaScript
    $inputData = json_decode(file_get_contents('php://input'), true);
    $userMsg = $inputData['message'] ?? '';

    if (empty($userMsg)) {
        echo json_encode(["error" => "Chưa có nội dung tin nhắn"]);
        exit;
    }

    // 5. Cấu hình Gemini API
    $apiKey = "AIzaSyDL6PeN6F9mid15oS5kOuYD9m4JIKZ8SuE"; // Mã API của bạn
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;

    $prompt = "Bạn là nhân viên tư vấn nhiệt tình của tiệm sách BookSM. \n\n" . 
              "Dữ liệu sách của cửa hàng: \n" . $context_sach . "\n\n" .
              "Yêu cầu của khách: " . $userMsg . "\n\n" .
              "Hãy tư vấn dựa trên danh sách sách trên. Nếu không thấy sách khách yêu cầu, hãy gợi ý sách khác gần giống.";

    $payload = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ]
    ];

    // KIỂM TRA LỖI ÉP KIỂU JSON (Rất quan trọng để tránh lỗi 400)
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($jsonPayload === false) {
        echo json_encode(["error" => "Lỗi gói dữ liệu: " . json_last_error_msg()]);
        exit;
    }

    // 6. Gọi API Gemini
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Dùng chuỗi JSON đã kiểm tra
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 7. Xử lý kết quả trả về
    if ($httpCode !== 200) {
        // Lấy chính xác câu báo lỗi của Google để in ra màn hình
        $googleError = json_decode($response, true);
        $errorMsg = $googleError['error']['message'] ?? "Lỗi không xác định từ Google";
        echo json_encode(["error" => "Google báo lỗi ($httpCode): " . $errorMsg]);
    } else {
        echo $response;
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Lỗi hệ thống: " . $e->getMessage()]);
}
?>