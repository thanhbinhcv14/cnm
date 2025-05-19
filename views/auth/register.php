<?php
require_once '../../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Đăng ký - Hệ thống sự kiện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #000000, #0f9d58);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .container-register-center {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 4px;
            display: block;
            color: #333;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #0f9d58;
            box-shadow: 0 0 0 0.25rem rgba(15, 157, 88, 0.25);
        }

        .btn-primary {
            background-color: #0f9d58;
            border-color: #0f9d58;
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0c7c45;
            border-color: #0c7c45;
        }

        .text-center {
            text-align: center;
            margin-top: 15px;
        }

        .link-login {
            color: #0f9d58;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .link-login:hover {
            text-decoration: underline;
        }

        .alert {
            display: none;
            margin-bottom: 15px;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-top: -10px;
            margin-bottom: 15px;
        }

        .form-check {
            margin-bottom: 15px;
        }

        .form-check-input:checked {
            background-color: #0f9d58;
            border-color: #0f9d58;
        }
    </style>
</head>
<body>
    <div class="container-register-center">
        <div class="register-container">
            <h2 class="text-center mb-4">Đăng ký tài khoản</h2>
            <div class="alert alert-danger" role="alert"></div>
            <form id="registerForm">
                <?php echo CSRF::getTokenInput(); ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="password-requirements">
                        Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Loại tài khoản</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="" selected disabled>-- Chọn loại tài khoản --</option>
                        <option value="2">Khách hàng</option>
                        <option value="3">Đơn vị tổ chức</option>
                    </select>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Tôi đồng ý với <a href="#" class="link-login">điều khoản sử dụng</a>
                    </label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                </div>
                <div class="text-center mt-3">
                    <p>Đã có tài khoản? <a href="login.php" class="link-login">Đăng nhập ngay</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function showAlert(message, type = 'danger') {
                const alert = $('.alert');
                alert.removeClass('alert-success alert-danger')
                     .addClass(`alert-${type}`)
                     .text(message)
                     .fadeIn();
                
                setTimeout(() => {
                    alert.fadeOut();
                }, 3000);
            }

            function validatePassword(password) {
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/;
                return regex.test(password);
            }

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                const username = $('#username').val();
                const email = $('#email').val();
                const fullname = $('#fullname').val();
                const phone = $('#phone').val();
                const password = $('#password').val();
                const confirmPassword = $('#confirmPassword').val();
                const roleId = $('#role').val();
                const csrfToken = $('input[name="csrf_token"]').val();
                
                if (!roleId) {
                    showAlert('Vui lòng chọn loại tài khoản');
                    return;
                }

                if (!validatePassword(password)) {
                    showAlert('Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số');
                    return;
                }
                
                if (password !== confirmPassword) {
                    showAlert('Mật khẩu xác nhận không khớp');
                    return;
                }
                
                $.ajax({
                    url: '../../api/auth.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        action: 'register',
                        username: username,
                        email: email,
                        fullname: fullname,
                        phone: phone,
                        password: password,
                        role_id: parseInt(roleId),
                        csrf_token: csrfToken
                    }),
                    success: function(response) {
                        if (response.success) {
                            showAlert('Đăng ký thành công!', 'success');
                            setTimeout(() => {
                                window.location.href = 'login.php';
                            }, 1000);
                        } else {
                            showAlert(response.message || 'Đăng ký thất bại. Vui lòng thử lại.');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Có lỗi xảy ra, vui lòng thử lại sau';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert(msg);
                    }
                });
            });
        });
    </script>
</body>
</html> 