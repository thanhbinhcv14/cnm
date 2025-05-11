<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../qr/qr_generator.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['items']) || !isset($data['tongtien']) || !isset($data['phuongthuc'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin thanh toán']);
    exit;
}

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$tongTien = $data['tongtien'];
$phuongThuc = $data['phuongthuc'];
$noidung = '';
foreach ($data['items'] as $item) {
    $noidung .= $item['TenSuKien'] . ' (' . $item['HangVe'] . ') x ' . $item['SoLuong'] . '; ';
}
// Sinh mã QR động
$qrContent = "Thanh toán user $userId, số tiền $tongTien, phương thức $phuongThuc, nội dung: $noidung";
$qrFilename = 'pay_' . $userId . '_' . time() . '.png';
$qrPath = QRGenerator::generateQRCode($qrContent, $qrFilename);
$qrRelativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $qrPath); // Đường dẫn tương đối cho frontend

$stmt = $conn->prepare("INSERT INTO thanhtoan (ID_User, TongTien, NoiDungThanhToan, PhuongThucThanhToan, Qrcode) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idsss", $userId, $tongTien, $noidung, $phuongThuc, $qrRelativePath);
if ($stmt->execute()) {
    // (Tùy chọn) Xóa giỏ hàng sau khi thanh toán
    $conn->query("DELETE FROM giohang WHERE ID_User = $userId");
    echo json_encode([
        'success' => true,
        'message' => 'Thanh toán thành công',
        'qr_image' => $qrRelativePath
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lưu thanh toán thất bại']);
}
$conn->close(); 