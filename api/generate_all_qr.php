<?php
require_once '../config/database.php';
require_once '../qr/qr_generator.php';

$conn = getDBConnection();

$sql = "SELECT ID_Sukien FROM sukien";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['ID_Sukien'];
    $qrData = "http://localhost/SuKien/cnm/index.php?id=" . $id;
    $qrFilename = "sukien_" . $id . ".png";
    $qrPath = QRGenerator::generateQRCode($qrData, $qrFilename);
    $qrRelativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $qrPath);
    $update = "UPDATE sukien SET qrcode='" . $qrRelativePath . "' WHERE ID_Sukien=" . $id;
    $conn->query($update);
}

echo "Đã tạo QR cho tất cả sự kiện!";
$conn->close();
?> 