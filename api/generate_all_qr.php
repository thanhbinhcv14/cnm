<?php
require_once '../config/database.php';
require_once '../qr/qr_generator.php';

$conn = getDBConnection();

$sql = "SELECT ID_Sukien FROM sukien";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['ID_Sukien'];
    // $ip = getHostByName(getHostName());
    //$qrData = "http://172.16.3.184/SuKien/cnm/views/product/productdetails.php?id=" . $id; sài chung mạng lan
    $qrData = " https://a0f8-2001-ee0-4f97-3930-ac2c-69c0-36cd-7af3.ngrok-free.app/SuKien/cnm/views/product/productdetails.php?id=" . $id;
    $qrFilename = "sukien_" . $id . ".png";
    $qrPath = QRGenerator::generateQRCode($qrData, $qrFilename);
    $qrRelativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $qrPath);
    $update = "UPDATE sukien SET qrcode='" . $qrRelativePath . "' WHERE ID_Sukien=" . $id;
    $conn->query($update);
}

echo "Đã tạo QR cho tất cả sự kiện!";
$conn->close();
?> 