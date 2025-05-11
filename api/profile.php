<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/csrf.php';

// Xử lý CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Đảm bảo có phiên đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ]);
    exit;
}

$conn = getDBConnection();

// Lấy thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT ID_User, TenDangNhap, HoTen, Email, SoDienThoai, ID_Role, HinhAnh FROM user WHERE ID_User = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin người dùng']);
            exit;
        }
        
        $user = $result->fetch_assoc();
        
        // Nếu không có hình ảnh, sử dụng hình mặc định
        if (empty($user['HinhAnh'])) {
            $user['HinhAnh'] = 'avatar.jpg';
        }
        
        // Lấy tên vai trò
        $role_stmt = $conn->prepare("SELECT TenRole FROM phanquyen WHERE ID_Role = ?");
        $role_stmt->bind_param("i", $user['ID_Role']);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result();
        $role = $role_result->fetch_assoc();
        $user['TenRole'] = $role['TenRole'] ?? 'Không xác định';
        
        // Trả về thông tin người dùng (không bao gồm mật khẩu)
        echo json_encode(['success' => true, 'data' => $user]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy thông tin: ' . $e->getMessage()]);
        exit;
    }
}

// Upload hình ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    try {
        
        $user_id = $_SESSION['user_id'];
        $uploadDir = '../Hinh/avatars/';
        
        // Đảm bảo thư mục tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $file = $_FILES['avatar'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileType = $file['type'];
        $fileError = $file['error'];
        
        // Kiểm tra lỗi upload
        if ($fileError !== 0) {
            $errorMessages = [
                1 => 'Kích thước file quá lớn',
                2 => 'Kích thước file quá lớn',
                3 => 'File upload không hoàn tất',
                4 => 'Không có file được upload',
                6 => 'Không tìm thấy thư mục tạm',
                7 => 'Không thể ghi file',
                8 => 'Phần mở rộng PHP đã dừng upload'
            ];
            $errorMessage = isset($errorMessages[$fileError]) ? $errorMessages[$fileError] : 'Lỗi không xác định';
            echo json_encode(['success' => false, 'message' => 'Lỗi upload: ' . $errorMessage]);
            exit;
        }
        
        // Kiểm tra kích thước file (5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode([
                'success' => false, 
                'message' => 'Kích thước file không được vượt quá 5MB'
            ]);
            exit;
        }
        
        // Kiểm tra định dạng file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file hình ảnh (JPG, PNG, GIF)']);
            exit;
        }
        
        // Tạo tên file duy nhất
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'user_' . $user_id . '_' . time() . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;
        
        // Di chuyển file tạm đến thư mục đích
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            // Cập nhật đường dẫn hình ảnh trong database
            $relativePath = 'Hinh/avatars/' . $newFileName;
            $stmt = $conn->prepare("UPDATE user SET HinhAnh = ? WHERE ID_User = ?");
            $stmt->bind_param("si", $relativePath, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 
                'message' => 'Upload hình ảnh thành công', 
                'avatar' => $relativePath]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật thông tin hình ảnh trong database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể upload file']);
        }
        
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi upload hình ảnh: ' . $e->getMessage()]);
        exit;
    }
}

// Cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'PUT' || ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['avatar']))) {
    try {
        // Lấy dữ liệu từ request
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Kiểm tra CSRF token
        if (!isset($input['csrf_token']) || !CSRF::validateToken($input['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'CSRF token không hợp lệ']);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $fullname = $input['fullname'] ?? '';
        $email = $input['email'] ?? '';
        $phone = $input['phone'] ?? '';
        $old_password = $input['old_password'] ?? '';
        $new_password = $input['new_password'] ?? '';
        
        // Mảng lỗi
        $errors = [];
        
        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($fullname) || strlen(trim($fullname)) < 2) {
            $errors['fullname'] = 'Họ và tên phải từ 2 ký tự trở lên';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        if (empty($phone) || !preg_match('/^0\d{9,10}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }
        
        // Kiểm tra email đã tồn tại chưa (nếu thay đổi email)
        $check_email_stmt = $conn->prepare("SELECT ID_User FROM user WHERE Email = ? AND ID_User != ?");
        $check_email_stmt->bind_param("si", $email, $user_id);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();
        
        if ($check_email_result->num_rows > 0) {
            $errors['email'] = 'Email đã được sử dụng bởi tài khoản khác';
        }
        
        // Kiểm tra số điện thoại đã tồn tại chưa (nếu thay đổi số điện thoại)
        $check_phone_stmt = $conn->prepare("SELECT ID_User FROM user WHERE SoDienThoai = ? AND ID_User != ?");
        $check_phone_stmt->bind_param("si", $phone, $user_id);
        $check_phone_stmt->execute();
        $check_phone_result = $check_phone_stmt->get_result();
        
        if ($check_phone_result->num_rows > 0) {
            $errors['phone'] = 'Số điện thoại đã được sử dụng bởi tài khoản khác';
        }
        
        // Nếu có thay đổi mật khẩu
        $password_update = false;
        if (!empty($new_password)) {
            // Kiểm tra độ mạnh của mật khẩu mới
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $new_password)) {
                $errors['new_password'] = 'Mật khẩu mới phải từ 6 ký tự, có chữ hoa, chữ thường và số';
            }
            
            // Kiểm tra mật khẩu cũ
            $check_password_stmt = $conn->prepare("SELECT MatKhau FROM user WHERE ID_User = ?");
            $check_password_stmt->bind_param("i", $user_id);
            $check_password_stmt->execute();
            $check_password_result = $check_password_stmt->get_result();
            $user_data = $check_password_result->fetch_assoc();
            
            if (empty($old_password) || !password_verify($old_password, $user_data['MatKhau'])) {
                $errors['old_password'] = 'Mật khẩu cũ không chính xác';
            } else {
                $password_update = true;
            }
        }
        
        // Nếu có lỗi
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $errors]);
            exit;
        }
        
        // Cập nhật thông tin
        if ($password_update) {
            // Cập nhật cả mật khẩu
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE user SET HoTen = ?, Email = ?, SoDienThoai = ?, MatKhau = ? WHERE ID_User = ?");
            $update_stmt->bind_param("ssssi", $fullname, $email, $phone, $hashed_password, $user_id);
        } else {
            // Chỉ cập nhật thông tin cơ bản
            $update_stmt = $conn->prepare("UPDATE user SET HoTen = ?, Email = ?, SoDienThoai = ? WHERE ID_User = ?");
            $update_stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
        }
        
        if ($update_stmt->execute()) {
            // Cập nhật tên người dùng trong session
            $_SESSION['user_fullname'] = $fullname;
            
            echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật thông tin']);
        }
        
        $conn->close();
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật thông tin: ' . $e->getMessage()]);
        exit;
    }
}

// Nếu không phải GET hoặc PUT/POST
echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
exit; 