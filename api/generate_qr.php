<?php
require_once '../config/config.php';
require_once '../qr/qr_generator.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['phuongthuc']) || !isset($data['tongtien']) || !isset($data['noidung'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

$userId = $_SESSION['user_id'];
$phuongThuc = $data['phuongthuc'];
$tongTien = $data['tongtien'];
$noidung = $data['noidung'];

$qrContent = "Thanh toán user $userId, số tiền $tongTien, phương thức $phuongThuc, nội dung: $noidung";
$qrFilename = 'pay_' . $userId . '_' . time() . '.png';
$qrPath = QRGenerator::generateQRCode($qrContent, $qrFilename);
$qrRelativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $qrPath);

echo json_encode(['success' => true, 'qr_image' => $qrRelativePath]); 