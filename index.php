<?php
// Bỏ session_start() ở đây, vì config.php sẽ xử lý
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

require_once 'config/config.php';
require_once 'config/database.php';

// Lấy danh sách sự kiện nổi bật
$conn = getDBConnection();
$query = "SELECT s.ID_SuKien, s.TenSuKien, s.HinhAnh, s.ThoiGianBatDau, u.HoTen as organizer_name 
          FROM sukien s 
          JOIN user u ON s.ID_User = u.ID_User 
          ORDER BY s.ThoiGianBatDau DESC 
          LIMIT 6";
$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}

$events = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="Hinh/logo/logo.png">
    <title>Trang chủ - Hệ thống sự kiện</title>
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

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('Hinh/banner/banner.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-hero {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-hero:hover {
            background-color: #0c7c45;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            text-align: center;
            margin: 50px 0;
            color: var(--secondary-color);
        }

        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-image {
            height: 200px;
            object-fit: cover;
        }

        .event-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 15px 0 10px;
        }

        .event-info {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .event-date {
            color: var(--primary-color);
            font-weight: bold;
        }

        .footer {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 50px 0;
            margin-top: 50px;
        }

        .footer-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: white;
        }

        .social-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: white;
            transition: color 0.3s ease;
        }

        .social-icon:hover {
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="Hinh/logo/logo.png" alt="Logo" height="40" class="me-2">
                Hệ thống sự kiện
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="views/events/list.php">Sự kiện</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Chào, 
                                <?php 
                                // Kiểm tra user_fullname có tồn tại không, nếu không thì dùng username
                                if (isset($_SESSION['user_fullname']) && !empty($_SESSION['user_fullname'])) {
                                    echo htmlspecialchars($_SESSION['user_fullname']);
                                } elseif (isset($_SESSION['username'])) {
                                    echo htmlspecialchars($_SESSION['username']); // Hiển thị TenDangNhap thay thế
                                } else {
                                    echo 'Bạn'; // Lời chào mặc định nếu cả hai không có
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                <!-- Thêm link đến dashboard/profile nếu cần -->
                                <!-- <li><a class="dropdown-item" href="views/user/dashboard.php">Bảng điều khiển</a></li> -->
                                <!-- <li><a class="dropdown-item" href="views/user/profile.php">Hồ sơ</a></li> -->
                                <!-- <li><hr class="dropdown-divider"></li> -->
                                <li><a class="dropdown-item" href="api/auth.php?action=logout">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="views/auth/register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Kết nối và tham gia sự kiện</h1>
            <p class="hero-subtitle">Khám phá các sự kiện thú vị và kết nối với cộng đồng</p>
            <a href="views/events/list.php" class="btn btn-hero">Xem sự kiện</a>
        </div>
    </section>

    <!-- Events Section -->
    <section class="container">
        <h2 class="section-title">Sự kiện nổi bật</h2>
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-4">
                <div class="event-card">
                    <img src="<?php echo htmlspecialchars($event['HinhAnh']); ?>" alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" class="event-image w-100">
                    <div class="p-3">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                        <p class="event-info">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($event['organizer_name']); ?>
                        </p>
                        <p class="event-info">
                            <i class="fas fa-calendar"></i> 
                            <span class="event-date"><?php echo date('d/m/Y', strtotime($event['ThoiGianBatDau'])); ?></span>
                        </p>
                        <a href="views/events/detail.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="footer-title">Hệ thống sự kiện</h3>
                    <p>Kết nối cộng đồng thông qua các sự kiện ý nghĩa</p>
                </div>
                <div class="col-md-3">
                    <h3 class="footer-title">Liên kết</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="footer-link">Trang chủ</a></li>
                        <li><a href="views/events/list.php" class="footer-link">Sự kiện</a></li>
                        <li><a href="views/auth/login.php" class="footer-link">Đăng nhập</a></li>
                        <li><a href="views/auth/register.php" class="footer-link">Đăng ký</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3 class="footer-title">Chính sách</h3>
                    <ul class="list-unstyled">
                        <li><a href="views/pages/privacy.php" class="footer-link">Chính sách bảo mật</a></li>
                        <li><a href="views/pages/terms.php" class="footer-link">Điều khoản dịch vụ</a></li>
                        <li><a href="views/pages/about.php" class="footer-link">Về chúng tôi</a></li>
                        <li><a href="views/pages/contact.php" class="footer-link">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h3 class="footer-title">Kết nối</h3>
                    <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
