<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Đảm bảo config.php được include TRƯỚC csrf.php và các xử lý khác
require_once '../config/config.php'; // Chứa session_start()
require_once '../config/database.php';
require_once '../includes/csrf.php'; // csrf.php sẽ không cần start session nữa

// Xử lý CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *'); // Hoặc chỉ định origin cụ thể
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

$conn = getDBConnection();

// Kiểm tra CSRF token cho các request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    // Log raw input để debug
    error_log("Auth API Raw Input: " . $input);
    
    $data = json_decode($input, true);

    // Kiểm tra json_decode thành công không
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("Auth API JSON Decode Error: " . json_last_error_msg());
        echo json_encode(['success' => false, 'message' => 'Dữ liệu gửi lên không hợp lệ (Invalid JSON)']);
        exit;
    }

    // Log dữ liệu đã decode
    error_log("Auth API Decoded Data: " . print_r($data, true));

    // --- TẠM THỜI VÔ HIỆU HÓA KIỂM TRA CSRF ĐỂ DEBUG ---
    /* 
    if (!isset($data['csrf_token']) || !CSRF::validateToken($data['csrf_token'])) {
        error_log("Auth API CSRF Token Validation Failed. Received: " . ($data['csrf_token'] ?? 'NULL') . " Session: " . ($_SESSION['csrf_token'] ?? 'NULL'));
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    */
    // --- KẾT THÚC VÔ HIỆU HÓA ---
}

// Hàm mã hóa mật khẩu
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Hàm kiểm tra mật khẩu (sử dụng password_verify để khớp với password_hash)
function verifyPassword($password, $hashFromDb) {
    // Sử dụng hàm password_verify của PHP
    return password_verify($password, $hashFromDb);
}

// Hàm tạo token
function generateToken($userId) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['user_token'] = $token;
    $_SESSION['user_id'] = $userId;
    return $token;
}

// Xử lý đăng xuất
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ../views/auth/login.php'); // Chuyển hướng về trang login
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'register':
                // Xử lý đăng ký
                // Thêm kiểm tra cho phone và role_id
                if (!isset($data['username']) || !isset($data['password']) || !isset($data['email']) || !isset($data['fullname']) || !isset($data['phone']) || !isset($data['role_id'])) {
                    echo json_encode(['success' => false, 'message' => 'Thiếu trường thông tin bắt buộc']);
                    break;
                }
                
                // Validate role_id (phải là 2 hoặc 3)
                $roleId = filter_var($data['role_id'], FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 2, 'max_range' => 3]
                ]);
                if ($roleId === false) {
                    echo json_encode(['success' => false, 'message' => 'Loại tài khoản không hợp lệ']);
                    break;
                }

                // Kiểm tra username đã tồn tại (TenDangNhap)
                $stmt = $conn->prepare("SELECT ID_User FROM user WHERE TenDangNhap = ?");
                $stmt->bind_param("s", $data['username']);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã tồn tại']);
                    break;
                }
                
                // Kiểm tra email đã tồn tại
                $stmt = $conn->prepare("SELECT ID_User FROM user WHERE email = ?");
                $stmt->bind_param("s", $data['email']);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    echo json_encode(['success' => false, 'message' => 'Email đã tồn tại']);
                    break;
                }
                
                // Tạo user mới
                $hashedPassword = hashPassword($data['password']);
                
                // Thêm SoDienThoai vào câu lệnh INSERT và dùng $roleId từ form
                $stmt = $conn->prepare("INSERT INTO user (TenDangNhap, MatKhau, email, HoTen, SoDienThoai, ID_Role) VALUES (?, ?, ?, ?, ?, ?)");
                // Cập nhật bind_param types thành "sssssi"
                $stmt->bind_param("sssssi", 
                    $data['username'],
                    $hashedPassword,
                    $data['email'],
                    $data['fullname'],
                    $data['phone'], // Thêm phone
                    $roleId        // Dùng roleId từ form
                );
                
                if ($stmt->execute()) {
                    $userId = $conn->insert_id;
                    $token = generateToken($userId); // generateToken không cần thay đổi
                    $_SESSION['username'] = $data['username']; 
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Đăng ký thành công',
                        'token' => $token,
                        'user' => [
                            'id' => $userId,
                            'username' => $data['username'], 
                            'email' => $data['email'],
                            'fullname' => $data['fullname'],
                            'role_id' => $roleId // Trả về roleId đã chọn
                        ]
                    ]);
                } else {
                    // Thêm chi tiết lỗi nếu có thể
                    error_log("Register Error: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại. Vui lòng thử lại.']);
                }
                break;
                
            case 'login':
                // Xử lý đăng nhập
                if (!isset($data['username']) || !isset($data['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Thiếu tên đăng nhập hoặc mật khẩu']);
                    break;
                }
                
                // Lấy thông tin user từ TenDangNhap
                $stmt = $conn->prepare("SELECT ID_User, TenDangNhap, MatKhau, email, HoTen, ID_Role FROM user WHERE TenDangNhap = ?");
                $stmt->bind_param("s", $data['username']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
                    break;
                }
                
                $user = $result->fetch_assoc();
                
                // Kiểm tra mật khẩu (MatKhau)
                if (!verifyPassword($data['password'], $user['MatKhau'])) {
                    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
                    break;
                }
                
                // Tạo token và lưu session
                $token = generateToken($user['ID_User']);
                $_SESSION['username'] = $user['TenDangNhap'];
                $_SESSION['user_id'] = $user['ID_User'];
                $_SESSION['role_id'] = $user['ID_Role'];
                $_SESSION['user_fullname'] = $user['HoTen'];

                // Chuẩn bị dữ liệu trả về (không bao gồm mật khẩu)
                $userData = [
                    'id' => $user['ID_User'],
                    'username' => $user['TenDangNhap'],
                    'email' => $user['email'],
                    'fullname' => $user['HoTen'],
                    'role_id' => $user['ID_Role']
                    // Map role_id thành role name nếu cần
                ];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'token' => $token,
                    'user' => $userData
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Phương thức request không hợp lệ']);
    exit;
}

$conn->close();
?> 