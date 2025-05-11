<?php
require_once '../../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Đăng nhập - Hệ thống sự kiện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #000000, #0f9d58);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .container-login-center {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
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

        .link-register {
            color: #0f9d58;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .link-register:hover {
            text-decoration: underline;
        }

        .alert {
            display: none;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-login-center">
        <div class="login-container">
            <h2 class="text-center mb-4">Đăng nhập</h2>
            <div class="alert alert-danger" role="alert"></div>
            <form id="loginForm">
                <?php echo CSRF::getTokenInput(); ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                </div>
                <div class="text-center mt-3">
                    <p>Chưa có tài khoản? <a href="register.php" class="link-register">Đăng ký ngay</a></p>
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

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const username = $('#username').val();
                const password = $('#password').val();
                const csrfToken = $('input[name="csrf_token"]').val();
                
                $.ajax({
                    url: '../../api/auth.php',
                    type: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({
                        action: 'login',
                        username: username,
                        password: password,
                        csrf_token: csrfToken
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Lưu token và user info vào cookie
                            document.cookie = `token=${response.token}; path=/`;
                            document.cookie = `user=${JSON.stringify(response.user)}; path=/`;
                            
                            showAlert('Đăng nhập thành công!', 'success');
                            
                            // Chuyển hướng về trang chủ cho tất cả user
                            setTimeout(() => {
                                window.location.href = '../../index.php'; 
                            }, 1000);
                        } else {
                            showAlert(response.message);
                        }
                    },
                    error: function() {
                        showAlert('Có lỗi xảy ra, vui lòng thử lại sau');
                    }
                });
            });
        });
    </script>
</body>
</html> 