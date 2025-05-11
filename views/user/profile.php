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
    <title>Thông tin cá nhân - Hệ thống sự kiện</title>
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
        .dropdown-menu .dropdown-item:hover {
            background-color: #0f9d58;
            color: #fff;
        }
        .dropdown-menu .dropdown-item.active {
            background-color: #0f9d58;
            color: #fff;
        }

        /* Profile specific styles */
        .profile-container {
            padding: 2rem 0;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-right: 1.5rem;
            overflow: hidden;
            position: relative;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar i {
            position: absolute;
            font-size: 2.5rem;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            color: #6c757d;
            font-style: italic;
        }

        .btn-update-profile {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-update-profile:hover {
            background-color: #0c7c45;
            color: white;
        }

        .nav-tabs .nav-link {
            color: #495057 !important;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container profile-container">
        <h1 class="page-title text-center">Thông tin cá nhân</h1>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar" id="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h2 class="profile-name" id="displayFullname"></h2>
                            <p class="profile-role" id="displayRole"></p>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="changeAvatarBtn">
                                <i class="fas fa-camera me-1"></i> Đổi ảnh đại diện
                            </button>
                        </div>
                    </div>
                    
                    <!-- Form ẩn để upload ảnh đại diện -->
                    <form id="avatarForm" style="display: none;">
                        <?php echo CSRF::getTokenInput(); ?>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Chọn ảnh đại diện mới</label>
                            <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">Hỗ trợ định dạng JPG, PNG, GIF (kích thước tối đa 5MB)</div>
                            <div id="avatarError" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary btn-sm" onclick="uploadAvatar()">
                                <i class="fas fa-upload me-1"></i> Tải lên
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelAvatarUpload()">
                                <i class="fas fa-times me-1"></i> Hủy
                            </button>
                        </div>
                    </form>
                    
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab" aria-controls="info-tab-pane" aria-selected="true">
                                <i class="fas fa-info-circle me-1"></i> Thông tin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">
                                <i class="fas fa-key me-1"></i> Đổi mật khẩu
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Thông tin cá nhân tab -->
                        <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                            <form id="profileForm">
                                <?php echo CSRF::getTokenInput(); ?>
                                
                                <div class="mb-3 row">
                                    <label class="col-md-3 col-form-label">Tên đăng nhập:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control-plaintext" id="username" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="fullname" class="col-md-3 col-form-label">Họ và tên:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                                        <div id="fullnameError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="email" class="col-md-3 col-form-label">Email:</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div id="emailError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="phone" class="col-md-3 col-form-label">Số điện thoại:</label>
                                    <div class="col-md-9">
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <div id="phoneError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-update-profile" onclick="updateProfile()">
                                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Đổi mật khẩu tab -->
                        <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                            <form id="passwordForm">
                                <?php echo CSRF::getTokenInput(); ?>
                                
                                <div class="mb-3 row">
                                    <label for="old_password" class="col-md-3 col-form-label">Mật khẩu hiện tại:</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                                        <div id="oldPasswordError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="new_password" class="col-md-3 col-form-label">Mật khẩu mới:</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div id="newPasswordError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="confirm_password" class="col-md-3 col-form-label">Nhập lại mật khẩu mới:</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div id="confirmPasswordError" class="text-danger"></div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-update-profile" onclick="updatePassword()">
                                        <i class="fas fa-key me-1"></i> Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadUserProfile();
            
            // Xử lý sự kiện khi người dùng nhập dữ liệu
            $('#fullname').on('input', function() {
                validateField('fullname', $(this).val());
            });
            
            $('#email').on('input', function() {
                validateField('email', $(this).val());
            });
            
            $('#phone').on('input', function() {
                validateField('phone', $(this).val());
            });
            
            $('#new_password').on('input', function() {
                validateField('new_password', $(this).val());
                // Kiểm tra lại confirm password nếu đã nhập
                if ($('#confirm_password').val()) {
                    validateField('confirm_password', $('#confirm_password').val());
                }
            });
            
            $('#confirm_password').on('input', function() {
                validateField('confirm_password', $(this).val());
            });
            
            // Hiển thị form upload ảnh đại diện
            $('#changeAvatarBtn').on('click', function() {
                $('#avatarForm').toggle();
            });
            
            // Xem trước ảnh đại diện khi người dùng chọn file
            $('#avatar').on('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    // Kiểm tra kích thước file (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        $('#avatarError').text('Kích thước file không được vượt quá 5MB');
                        this.value = '';
                        return;
                    }
                    
                    // Kiểm tra định dạng file
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        $('#avatarError').text('Chỉ chấp nhận file hình ảnh (JPG, PNG, GIF)');
                        this.value = '';
                        return;
                    }
                    
                    // Xóa thông báo lỗi nếu có
                    $('#avatarError').text('');
                    
                    // Hiển thị ảnh xem trước
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-avatar').html('<img src="' + e.target.result + '" alt="Avatar Preview">');
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        
        function loadUserProfile() {
            $.ajax({
                url: '../../api/profile.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const userData = response.data;
                        
                        // Hiển thị thông tin người dùng
                        $('#username').val(userData.TenDangNhap);
                        $('#fullname').val(userData.HoTen);
                        $('#email').val(userData.Email);
                        $('#phone').val(userData.SoDienThoai);
                        $('#displayFullname').text(userData.HoTen);
                        $('#displayRole').text(userData.TenRole);
                        
                        // Hiển thị ảnh đại diện
                        if (userData.HinhAnh) {
                            if (userData.HinhAnh === 'avatar.jpg') {
                                // Hiển thị avatar mặc định
                                $('#profile-avatar').html('<img src="../../Hinh/avatar/avatar.jpg" alt="Default Avatar">');
                            } else {
                                // Hiển thị ảnh đại diện từ server
                                $('#profile-avatar').html('<img src="../../' + userData.HinhAnh + '" alt="Avatar">');
                                // Cập nhật avatar trong navbar
                                $('#userAvatarContainer').html('<img src="../../' + userData.HinhAnh + '" alt="Avatar" class="user-avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">');
                            }
                        } else {
                            // Hiển thị avatar mặc định nếu không có ảnh
                            $('#profile-avatar').html('<img src="../../Hinh/avatar/avatar.jpg" alt="Default Avatar">');
                        }
                    } else {
                        alert('Lỗi khi tải thông tin: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
        
        function uploadAvatar() {
            // Kiểm tra xem người dùng đã chọn file chưa
            const fileInput = $('#avatar')[0];
            if (!fileInput.files || !fileInput.files[0]) {
                $('#avatarError').text('Vui lòng chọn file ảnh');
                return;
            }
            
            // Tạo Form Data
            const formData = new FormData();
            formData.append('avatar', fileInput.files[0]);
            formData.append('csrf_token', $('input[name="csrf_token"]').val());
            
            // Gửi request
            $.ajax({
                url: '../../api/profile.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        alert('Cập nhật ảnh đại diện thành công!');
                        // Hiển thị ảnh đại diện mới
                        $('#profile-avatar').html('<img src="../../' + response.avatar + '" alt="Avatar">');
                        // Cập nhật avatar trong navbar
                        $('#userAvatarContainer').html('<img src="../../' + response.avatar + '" alt="Avatar" class="user-avatar">');
                        // Ẩn form upload
                        $('#avatarForm').hide();
                        // Reset form
                        $('#avatarForm')[0].reset();
                    } else {
                        $('#avatarError').text(response.message);
                    }
                },
                error: function() {
                    $('#avatarError').text('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }
        
        function cancelAvatarUpload() {
            // Ẩn form upload
            $('#avatarForm').hide();
            // Reset form
            $('#avatarForm')[0].reset();
            // Xóa thông báo lỗi
            $('#avatarError').text('');
            // Tải lại ảnh đại diện hiện tại
            loadUserProfile();
        }
        
        // Hàm kiểm tra từng trường riêng lẻ
        function validateField(fieldName, value) {
            switch(fieldName) {
                case 'fullname':
                    if (!value || value.trim().length < 2) {
                        $('#fullnameError').text('Họ và tên phải từ 2 ký tự trở lên!');
                        return false;
                    } else {
                        $('#fullnameError').text('');
                        return true;
                    }
                    break;
                    
                case 'email':
                    if (!/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/.test(value)) {
                        $('#emailError').text('Email không hợp lệ!');
                        return false;
                    } else {
                        $('#emailError').text('');
                        return true;
                    }
                    break;
                    
                case 'phone':
                    if (!/^0\d{9,10}$/.test(value)) {
                        $('#phoneError').text('Số điện thoại không hợp lệ!');
                        return false;
                    } else {
                        $('#phoneError').text('');
                        return true;
                    }
                    break;
                    
                case 'new_password':
                    if (value && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(value)) {
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
        
        function validateProfileForm() {
            let isValid = true;
            
            // Reset lỗi cũ
            $('.text-danger').empty();
            
            // Kiểm tra từng trường
            isValid = validateField('fullname', $('#fullname').val()) && isValid;
            isValid = validateField('email', $('#email').val()) && isValid;
            isValid = validateField('phone', $('#phone').val()) && isValid;
            
            return isValid;
        }
        
        function validatePasswordForm() {
            let isValid = true;
            
            // Reset lỗi cũ
            $('#oldPasswordError, #newPasswordError, #confirmPasswordError').empty();
            
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
        
        function updateProfile() {
            if (!validateProfileForm()) return;
            
            const formData = {
                fullname: $('#fullname').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };
            
            $.ajax({
                url: '../../api/profile.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        alert('Cập nhật thông tin thành công!');
                        loadUserProfile(); // Tải lại thông tin
                    } else {
                        if (response.errors) {
                            // Hiển thị lỗi cụ thể cho từng trường
                            for (const field in response.errors) {
                                $(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                    }
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            for (const field in response.errors) {
                                $(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            alert('Có lỗi xảy ra khi kết nối với server');
                        }
                    } catch (e) {
                        alert('Có lỗi xảy ra khi kết nối với server');
                    }
                }
            });
        }
        
        function updatePassword() {
            if (!validatePasswordForm()) return;
            
            const formData = {
                old_password: $('#old_password').val(),
                new_password: $('#new_password').val(),
                fullname: $('#fullname').val(), // Cần gửi thông tin cơ bản để không bị mất
                email: $('#email').val(),
                phone: $('#phone').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };
            
            $.ajax({
                url: '../../api/profile.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        alert('Đổi mật khẩu thành công!');
                        // Reset form mật khẩu
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
        }
    </script>
</body>
</html>