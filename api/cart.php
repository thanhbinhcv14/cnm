<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy thông tin giỏ hàng của user
        $stmt = $conn->prepare("
            SELECT c.*, v.HangVe, v.GiaVe, s.TenSuKien, s.HinhAnh 
            FROM giohang c
            JOIN ve v ON c.ID_Ve = v.ID_Ve
            JOIN sukien s ON v.ID_SuKien = s.ID_SuKien
            WHERE c.ID_User = ?
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cartItems = [];
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = [
                'ID_GioHang' => $row['ID_GioHang'],
                'ID_Ve' => $row['ID_Ve'],
                'SoLuong' => $row['SoLuong'],
                'HangVe' => $row['HangVe'],
                'GiaVe' => $row['GiaVe'],
                'TenSuKien' => $row['TenSuKien'],
                'HinhAnh' => $row['HinhAnh'],
                'TongTien' => $row['SoLuong'] * $row['GiaVe']
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $cartItems]);
        break;

    case 'POST':
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!isset($data['ID_Ve']) || !isset($data['SoLuong'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin vé hoặc số lượng']);
            exit;
        }

        // Kiểm tra vé có tồn tại không
        $stmt = $conn->prepare("SELECT SoLuong FROM ve WHERE ID_Ve = ?");
        $stmt->bind_param("i", $data['ID_Ve']);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result->fetch_assoc();

        if (!$ticket) {
            echo json_encode(['success' => false, 'message' => 'Vé không tồn tại']);
            exit;
        }

        if ($data['SoLuong'] > $ticket['SoLuong']) {
            echo json_encode(['success' => false, 'message' => 'Số lượng vé không đủ']);
            exit;
        }

        // Kiểm tra vé đã có trong giỏ hàng chưa
        $stmt = $conn->prepare("SELECT ID_GioHang, SoLuong FROM giohang WHERE ID_User = ? AND ID_Ve = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $data['ID_Ve']);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingItem = $result->fetch_assoc();

        if ($existingItem) {
            // Cập nhật số lượng nếu đã có
            $newQuantity = $existingItem['SoLuong'] + $data['SoLuong'];
            if ($newQuantity > $ticket['SoLuong']) {
                echo json_encode(['success' => false, 'message' => 'Số lượng vé không đủ']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE giohang SET SoLuong = ? WHERE ID_GioHang = ?");
            $stmt->bind_param("ii", $newQuantity, $existingItem['ID_GioHang']);
        } else {
            // Thêm mới vào giỏ hàng
            $stmt = $conn->prepare("INSERT INTO giohang (ID_User, ID_Ve, SoLuong) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $_SESSION['user_id'], $data['ID_Ve'], $data['SoLuong']);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Thêm vào giỏ hàng thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        break;

    case 'DELETE':
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!isset($data['ID_GioHang'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID giỏ hàng']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM giohang WHERE ID_GioHang = ? AND ID_User = ?");
        $stmt->bind_param("ii", $data['ID_GioHang'], $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Xóa khỏi giỏ hàng thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        break;
}

$conn->close();
?> 