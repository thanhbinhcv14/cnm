<?php
// Cấu hình session (PHẢI đặt trước session_start())
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Thêm các cài đặt session khác nếu cần ở đây

// Chỉ bắt đầu session nếu chưa có session nào hoạt động
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cấu hình chung
define('BASE_URL', 'http://localhost/SuKien/cnm');
define('SITE_NAME', 'Hệ thống tổ chức sự kiện');

// Cấu hình QR code
define('QR_CODE_SIZE', 5);
define('QR_CODE_LEVEL', 'H'); // L, M, Q, H
define('QR_CODE_MARGIN', 2);

// Cấu hình upload
define('UPLOAD_DIR', '../Hinh/');
define('MAX_FILE_SIZE', 5242880); // 5MB

?> 