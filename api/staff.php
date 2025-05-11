<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 0) != 1) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền truy cập']);
    exit;
}

$conn = getDBConnection();

// Lấy danh sách tài khoản (trừ admin)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT u.ID_User, u.HoTen, u.TenDangNhap, u.Email, u.SoDienThoai, r.TenRole FROM user u JOIN phanquyen r ON u.ID_Role = r.ID_Role WHERE u.ID_Role != 1 ORDER BY u.ID_User DESC";
    $result = $conn->query($sql);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
    }
    $conn->close();
    exit;
}

// Thêm tài khoản mới
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['csrf_token']) || !CSRF::validateToken($input['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'CSRF token không hợp lệ']);
        exit;
    }
    $username = trim($input['username'] ?? '');
    $fullname = trim($input['fullname'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $role_id = intval($input['role_id'] ?? 0);
    $password = $input['password'] ?? '';
    if (!$username || !$fullname || !$email || !$phone || !$role_id || !$password) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
        exit;
    }
    // Kiểm tra trùng username/email
    $stmt = $conn->prepare('SELECT ID_User FROM user WHERE TenDangNhap = ? OR Email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc email đã tồn tại']);
        exit;
    }
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO user (TenDangNhap, MatKhau, Email, HoTen, SoDienThoai, ID_Role) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssi', $username, $hashedPassword, $email, $fullname, $phone, $role_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thêm tài khoản thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm tài khoản']);
    }
    $conn->close();
    exit;
}

// Xóa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['csrf_token']) || !CSRF::validateToken($input['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'CSRF token không hợp lệ']);
        exit;
    }
    $user_id = intval($input['user_id'] ?? 0);
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID tài khoản']);
        exit;
    }
    // Không cho xóa admin
    $stmt = $conn->prepare('DELETE FROM user WHERE ID_User = ? AND ID_Role != 1');
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa tài khoản thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa tài khoản']);
    }
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Phương thức không hỗ trợ']); 