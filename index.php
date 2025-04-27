<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Lấy danh sách sự kiện nổi bật từ API
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/events.php';
$response = file_get_contents($apiUrl);
$events = [];
$newEvents = [];
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        $events = $data['data'];
        // Lọc sự kiện mới trong 7 ngày gần nhất dựa vào NgayTao
        $now = new DateTime();
        foreach ($events as $event) {
            if (isset($event['NgayTao'])) {
                $created = new DateTime($event['NgayTao']);
                $interval = $now->diff($created);
                if ($interval->days <= 7 && $created <= $now) {
                    $newEvents[] = $event;
                }
            }
        }
    }
}
function getEventImagePath($filename) {
    if (!$filename) return 'Hinh/logo/logo.png';
    $posterPath = 'Hinh/poster/' . $filename;
    $mainPath = 'Hinh/' . $filename;
    if (file_exists($posterPath)) return $posterPath;
    if (file_exists($mainPath)) return $mainPath;
    return 'Hinh/logo/logo.png';
}
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

        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
        }

        .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .carousel-caption {
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
        }

        .carousel-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .carousel-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
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
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: bold;
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
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin: 15px 0 10px;
        }

        .event-info {
            color: #666;
            font-size: 1rem;
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
            .carousel-item {
                height: 300px;
            }
            .carousel-title {
                font-size: 2rem;
            }
            .carousel-subtitle {
                font-size: 1rem;
            }
            .event-image {
                height: 180px;
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
                HỆ THỐNG TỔ CHỨC SỰ KIỆN
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
                                 <li><a class="dropdown-item" href="views/user/profile.php">Hồ sơ</a></li> 
                                 <li><hr class="dropdown-divider"></li> 
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

    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('Hinh/banner/banner1.jpg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Chào mừng đến với trang chủ</h1>
                    <p class="carousel-subtitle">Khám phá những sự kiện thú vị và ý nghĩa</p>
                    <a href="views/events/list.php" class="btn btn-hero">Xem sự kiện</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner2.jpeg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Sự kiện đặc biệt</h1>
                    <p class="carousel-subtitle">Tham gia các hoạt động thú vị cùng chúng tôi</p>
                    <a href="views/events/list.php" class="btn btn-hero">Khám phá ngay</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner3.jpg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Kết nối cộng đồng</h1>
                    <p class="carousel-subtitle">Cùng nhau tạo nên những khoảnh khắc đáng nhớ</p>
                    <a href="views/events/list.php" class="btn btn-hero">Tham gia ngay</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Events Section -->
    <section class="container">
        <h2 class="section-title">SỰ KIỆN NỔI BẬT</h2>
        <div class="row">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                <div class="col-md-3">
                    <div class="event-card">
                        <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" class="event-image w-100">
                        <div class="p-3">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                            <p class="event-info">
                                <i class="fas fa-calendar"></i> 
                                <span class="event-date"><?php echo date('d/m/Y H:i', strtotime($event['ThoiGianBatDau'])); ?></span>
                            </p>
                            <p class="event-info">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php echo htmlspecialchars($event['DiaDiem']); ?>
                            </p>
                            <a href="views/events/detail.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        Hiện tại chưa có sự kiện nào được tổ chức. Vui lòng quay lại sau!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- New Events Section -->
    <section class="container mt-5">
        <h2 class="section-title" style="color: #ff5722;">SỰ KIỆN MỚI</h2>
        <div class="row">
            <?php if (!empty($newEvents)): ?>
                <?php foreach (array_slice($newEvents, 0, 4) as $event): ?>
                <div class="col-md-3">
                    <div class="event-card border border-warning">
                        <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" class="event-image w-100">
                        <div class="p-3">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                            <p class="event-info">
                                <i class="fas fa-calendar-plus"></i> 
                                <span class="event-date">Tạo lúc: <?php echo date('d/m/Y H:i', strtotime($event['NgayTao'])); ?></span>
                            </p>
                            <p class="event-info">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php echo htmlspecialchars($event['DiaDiem']); ?>
                            </p>
                            <a href="views/events/detail.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-warning w-100 text-white">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-secondary">
                        Không có sự kiện mới trong 7 ngày gần đây.
                    </div>
                </div>
            <?php endif; ?>
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
