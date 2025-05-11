<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/config.php';

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception("Không thể kết nối đến database");
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (!isset($_GET['event_id'])) {
                throw new Exception("Thiếu ID sự kiện");
            }

            $event_id = intval($_GET['event_id']);
            if ($event_id <= 0) {
                throw new Exception("ID sự kiện không hợp lệ");
            }

            $query = "SELECT b.*, u.HoTen as user_name, u.HinhAnh as user_avatar 
                     FROM binhluan b 
                     JOIN user u ON b.ID_User = u.ID_User 
                     WHERE b.ID_Sukien = ? 
                     ORDER BY b.NgayTao DESC";
            
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }

            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = [
                    'ID_BinhLuan' => $row['ID_BinhLuan'],
                    'ID_Sukien' => $row['ID_Sukien'],
                    'ID_User' => $row['ID_User'],
                    'NoiDung' => $row['NoiDung'],
                    'NgayTao' => $row['NgayTao'],
                    'user_name' => $row['user_name'],
                    'user_avatar' => $row['user_avatar'] ? '../../Hinh/avatar/' . $row['user_avatar'] : '../../Hinh/avatar/avatar.jpg'
                ];
            }

            $stmt->close();
            $conn->close();

            echo json_encode([
                'success' => true,
                'data' => $comments,
                'event_id' => $event_id
            ]);
            break;

        case 'POST':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Vui lòng đăng nhập");
            }

            // Xử lý xóa bình luận nếu có action=delete
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $comment_id = isset($_POST['ID_BinhLuan']) ? intval($_POST['ID_BinhLuan']) : 0;
                if ($comment_id <= 0) {
                    throw new Exception("ID bình luận không hợp lệ");
                }

                // Kiểm tra quyền xóa
                $query = "SELECT ID_User FROM binhluan WHERE ID_BinhLuan = ?";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }

                $stmt->bind_param("i", $comment_id);
                if (!$stmt->execute()) {
                    throw new Exception("Lỗi kiểm tra bình luận: " . $stmt->error);
                }

                $result = $stmt->get_result();
                $comment = $result->fetch_assoc();

                if (!$comment) {
                    throw new Exception("Không tìm thấy bình luận");
                }

                if ($comment['ID_User'] != $_SESSION['user_id'] && $_SESSION['role_id'] != 1) {
                    throw new Exception("Bạn không có quyền xóa bình luận này");
                }

                $query = "DELETE FROM binhluan WHERE ID_BinhLuan = ?";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Lỗi chuẩn bị truy vấn xóa: " . $conn->error);
                }

                $stmt->bind_param("i", $comment_id);
                if (!$stmt->execute()) {
                    throw new Exception("Lỗi xóa bình luận: " . $stmt->error);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa bình luận thành công'
                ]);
                exit;
            }

            // Xử lý thêm bình luận như cũ
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data) {
                throw new Exception("Dữ liệu không hợp lệ");
            }

            if (!isset($data['ID_Sukien']) || !isset($data['NoiDung'])) {
                throw new Exception("Thiếu thông tin bình luận");
            }

            $event_id = intval($data['ID_Sukien']);
            $content = trim($data['NoiDung']);

            if ($event_id <= 0) {
                throw new Exception("ID sự kiện không hợp lệ");
            }

            if (empty($content)) {
                throw new Exception("Nội dung bình luận không được để trống");
            }

            $query = "INSERT INTO binhluan (ID_Sukien, ID_User, NoiDung, NgayTao) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }

            $stmt->bind_param("iis", $event_id, $_SESSION['user_id'], $content);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi thêm bình luận: " . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Thêm bình luận thành công',
                'id' => $conn->insert_id
            ]);
            break;

        case 'DELETE':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("Vui lòng đăng nhập");
            }

            if (!isset($_GET['id']) && !isset($_POST['ID_BinhLuan'])) {
                throw new Exception("Thiếu ID bình luận");
            }

            $comment_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['ID_BinhLuan']);
            if ($comment_id <= 0) {
                throw new Exception("ID bình luận không hợp lệ");
            }

            // Kiểm tra quyền xóa
            $query = "SELECT ID_User FROM binhluan WHERE ID_BinhLuan = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }

            $stmt->bind_param("i", $comment_id);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi kiểm tra bình luận: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $comment = $result->fetch_assoc();

            if (!$comment) {
                throw new Exception("Không tìm thấy bình luận");
            }

            if ($comment['ID_User'] != $_SESSION['user_id'] && $_SESSION['role_id'] != 1) {
                throw new Exception("Bạn không có quyền xóa bình luận này");
            }

            $query = "DELETE FROM binhluan WHERE ID_BinhLuan = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn xóa: " . $conn->error);
            }

            $stmt->bind_param("i", $comment_id);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi xóa bình luận: " . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Xóa bình luận thành công'
            ]);
            break;

        default:
            throw new Exception("Phương thức không được hỗ trợ");
    }
} catch (Exception $e) {
    error_log("Comments API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} 
?> 