<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../config/database.php';
require_once '../config/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $query = "SELECT s.ID_SuKien, s.TenSuKien, s.HinhAnh, s.ThoiGianBatDau, s.ThoiGianKetThuc, d.TenDiaDiem as DiaDiem, u.HoTen as organizer_name
                  FROM sukien s
                  JOIN user u ON s.ID_User = u.ID_User
                  JOIN diadiem d ON s.ID_DiaDiem = d.ID_DiaDiem
                  ORDER BY s.ThoiGianBatDau DESC";
        $result = $conn->query($query);
        if ($result) {
            $events = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $events]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("INSERT INTO sukien (TenSuKien, HinhAnh, ThoiGianBatDau, ThoiGianKetThuc, ID_DiaDiem, ID_User) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii",
            $data['TenSuKien'],
            $data['HinhAnh'],
            $data['ThoiGianBatDau'],
            $data['ThoiGianKetThuc'],
            $data['ID_DiaDiem'],
            $data['ID_User']
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Thêm sự kiện thành công', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thêm sự kiện thất bại']);
        }
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $put_vars);
        $id = $put_vars['ID_SuKien'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID_SuKien']);
            break;
        }
        $stmt = $conn->prepare("UPDATE sukien SET TenSuKien=?, HinhAnh=?, ThoiGianBatDau=?, ThoiGianKetThuc=?, ID_DiaDiem=?, ID_User=? WHERE ID_SuKien=?");
        $stmt->bind_param("ssssiii",
            $put_vars['TenSuKien'],
            $put_vars['HinhAnh'],
            $put_vars['ThoiGianBatDau'],
            $put_vars['ThoiGianKetThuc'],
            $put_vars['ID_DiaDiem'],
            $put_vars['ID_User'],
            $id
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật sự kiện thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật sự kiện thất bại']);
        }
        break;
    case 'DELETE':
        parse_str(file_get_contents('php://input'), $del_vars);
        $id = $del_vars['ID_SuKien'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID_SuKien']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM sukien WHERE ID_SuKien=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Xóa sự kiện thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa sự kiện thất bại']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Phương thức không hỗ trợ']);
        break;
}

$conn->close();
?> 