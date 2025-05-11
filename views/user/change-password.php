<?php
require_once '../../config/config.php';
require_once '../../includes/csrf.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Đổi mật khẩu - Hệ thống sự kiện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0f9d58;
            --secondary-color: #000000;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            padding: 1rem 0;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .navbar-brand img {
            height: 60px;
            width: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }
        
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
            object-fit: cover;
            border: 2px solid #fff;
            background-color: #fff;
        }

        .dropdown-menu {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
            margin-top: 10px;
        }
        .dropdown-menu .dropdown-item {
            color: #000;
            transition: background 0.2s, color 0.2s;
            padding: 8px 20px;
            font-size: 0.95rem;
            background-color: transparent;
        }
        .dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
            background-color: #0f9d58;
            color: #fff;
        }
        .dropdown-menu .dropdown-item.active {
            background-color: #0f9d58;
            color: #fff;
        }

        /* Password change specific styles */
        .password-container {
            padding: 2rem 0;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .password-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .btn-change-password {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-change-password:hover {
            background-color: #0c7c45;
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container password-container">
        <h1 class="page-title text-center">Đổi mật khẩu</h1>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="password-card">
                    <form id="passwordForm">
                        <?php echo CSRF::getTokenInput(); ?>
                        
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Mật khẩu hiện tại:</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                            <div id="oldPasswordError" class="text-danger"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div id="newPasswordError" class="text-danger"></div>
                            <div class="form-text">Mật khẩu phải từ 6 ký tự, có chữ hoa, chữ thường và số.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Nhập lại mật khẩu mới:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div id="confirmPasswordError" class="text-danger"></div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-change-password" onclick="updatePassword()">
                                <i class="fas fa-key me-1"></i> Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tải thông tin người dùng để hiển thị avatar
            loadUserAvatar();
            
            // Xử lý sự kiện khi người dùng nhập dữ liệu
            $('#new_password').on('input', function() {
                validateField('new_password', $(this).val());
                if ($('#confirm_password').val()) {
                    validateField('confirm_password', $('#confirm_password').val());
                }
            });
            
            $('#confirm_password').on('input', function() {
                validateField('confirm_password', $(this).val());
            });
        });
        
        // Hàm tải avatar người dùng
        function loadUserAvatar() {
            $.ajax({
                url: '../../api/profile.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const userData = response.data;
                        
                        // Hiển thị ảnh đại diện trong navbar
                        if (userData.HinhAnh && userData.HinhAnh !== 'avatar.jpg') {
                            // Hiển thị ảnh từ server
                            $('#userAvatarContainer').html('<img src="../../' + userData.HinhAnh + '" alt="Avatar" class="user-avatar">');
                        }
                    }
                }
            });
        }
        
        // Hàm kiểm tra từng trường riêng lẻ
        function validateField(fieldName, value) {
            switch(fieldName) {
                case 'new_password':
                    if (!value || !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(value)) {
                        $('#newPasswordError').text('Mật khẩu phải từ 6 ký tự, có chữ hoa, chữ thường và số!');
                        return false;
                    } else {
                        $('#newPasswordError').text('');
                        return true;
                    }
                    break;
                    
                case 'confirm_password':
                    if (value !== $('#new_password').val()) {
                        $('#confirmPasswordError').text('Mật khẩu nhập lại không khớp!');
                        return false;
                    } else {
                        $('#confirmPasswordError').text('');
                        return true;
                    }
                    break;
            }
        }
        
        function validatePasswordForm() {
            let isValid = true;
            
            // Reset lỗi cũ
            $('.text-danger').empty();
            
            // Kiểm tra mật khẩu cũ
            if (!$('#old_password').val()) {
                $('#oldPasswordError').text('Vui lòng nhập mật khẩu hiện tại!');
                isValid = false;
            }
            
            // Kiểm tra mật khẩu mới
            isValid = validateField('new_password', $('#new_password').val()) && isValid;
            
            // Kiểm tra nhập lại mật khẩu
            isValid = validateField('confirm_password', $('#confirm_password').val()) && isValid;
            
            return isValid;
        }
        
        function updatePassword() {
            if (!validatePasswordForm()) return;
            
            // Lấy thông tin người dùng để gửi kèm theo request
            $.ajax({
                url: '../../api/profile.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const userData = response.data;
                        
                        // Tiến hành đổi mật khẩu
                        const formData = {
                            old_password: $('#old_password').val(),
                            new_password: $('#new_password').val(),
                            fullname: userData.HoTen,
                            email: userData.Email,
                            phone: userData.SoDienThoai,
                            csrf_token: $('input[name="csrf_token"]').val()
                        };
                        
                        // Gửi yêu cầu đổi mật khẩu
                        $.ajax({
                            url: '../../api/profile.php',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify(formData),
                            success: function(response) {
                                if (response.success) {
                                    alert('Đổi mật khẩu thành công!');
                                    $('#passwordForm')[0].reset();
                                } else {
                                    if (response.errors) {
                                        // Hiển thị lỗi cụ thể cho từng trường
                                        if (response.errors.old_password) {
                                            $('#oldPasswordError').text(response.errors.old_password);
                                        }
                                        if (response.errors.new_password) {
                                            $('#newPasswordError').text(response.errors.new_password);
                                        }
                                    } else {
                                        alert('Lỗi: ' + response.message);
                                    }
                                }
                            },
                            error: function() {
                                alert('Có lỗi xảy ra khi kết nối với server');
                            }
                        });
                    } else {
                        alert('Lỗi khi tải thông tin người dùng: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
    </script>
</body>
</html> 