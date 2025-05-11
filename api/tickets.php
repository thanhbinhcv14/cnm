<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../config/database.php';
require_once '../config/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy danh sách vé của một sự kiện
        if (isset($_GET['event_id'])) {
            $event_id = intval($_GET['event_id']);
            $stmt = $conn->prepare("SELECT * FROM ve WHERE ID_SuKien = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $tickets]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID sự kiện']);
        }
        break;

    case 'POST':
        // Thêm vé mới
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['ID_SuKien']) || !isset($data['HangVe']) || !isset($data['GiaVe']) || !isset($data['SoLuong'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin vé']);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO ve (ID_SuKien, HangVe, GiaVe, SoLuong) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", 
            $data['ID_SuKien'],
            $data['HangVe'],
            $data['GiaVe'],
            $data['SoLuong']
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Thêm vé thành công', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thêm vé thất bại']);
        }
        break;

    case 'PUT':
        // Cập nhật thông tin vé
        parse_str(file_get_contents('php://input'), $put_vars);
        $id = $put_vars['ID_Ve'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID vé']);
            break;
        }

        $stmt = $conn->prepare("UPDATE ve SET HangVe=?, GiaVe=?, SoLuong=? WHERE ID_Ve=?");
        $stmt->bind_param("siii",
            $put_vars['HangVe'],
            $put_vars['GiaVe'],
            $put_vars['SoLuong'],
            $id
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật vé thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật vé thất bại']);
        }
        break;

    case 'DELETE':
        // Xóa vé
        parse_str(file_get_contents('php://input'), $del_vars);
        $id = $del_vars['ID_Ve'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID vé']);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM ve WHERE ID_Ve=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Xóa vé thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa vé thất bại']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Phương thức không hỗ trợ']);
        break;
}

$conn->close();
?> 