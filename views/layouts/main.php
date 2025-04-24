<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include $content; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Hàm hiển thị thông báo
        function showAlert(message, type = 'success') {
            const alertDiv = $('.alert');
            alertDiv.removeClass('alert-success alert-danger').addClass(`alert-${type}`);
            alertDiv.html(message).fadeIn();
            setTimeout(() => alertDiv.fadeOut(), 3000);
        }

        // Hàm xử lý lỗi API
        function handleApiError(xhr) {
            let message = 'Có lỗi xảy ra';
            try {
                const response = JSON.parse(xhr.responseText);
                message = response.message || message;
            } catch (e) {}
            showAlert(message, 'danger');
        }
    </script>
</body>
</html> 