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
        $sevenDaysAgo = (clone $now)->modify('-7 days');
        foreach ($events as $event) {
            if (isset($event['NgayTao'])) {
                $created = new DateTime($event['NgayTao']);
                if ($created >= $sevenDaysAgo && $created <= $now) {
                    $newEvents[] = $event;
                }
            }
        }
        // Nếu không có sự kiện nào trong 7 ngày gần nhất, hiển thị tất cả sự kiện
        if (empty($newEvents)) {
            $newEvents = $events;
        }
    }
}
function getEventImagePath($filename) {
    if (!$filename) return 'Hinh/logo/logo.png';
    $posterPath = 'Hinh/poster/' . $filename;
    $mainPath = 'Hinh/avatar/' . $filename;
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
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/views/partials/navbar.php'; ?>

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
                    <a href="views/product/allproduct.php" class="btn btn-hero">Xem sự kiện</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner2.jpeg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Sự kiện đặc biệt</h1>
                    <p class="carousel-subtitle">Tham gia các hoạt động thú vị cùng chúng tôi</p>
                    <a href="views/product/allproduct.php" class="btn btn-hero">Khám phá ngay</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner3.jpg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Kết nối cộng đồng</h1>
                    <p class="carousel-subtitle">Cùng nhau tạo nên những khoảnh khắc đáng nhớ</p>
                    <a href="views/product/allproduct.php" class="btn btn-hero">Tham gia ngay</a>
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

    <!-- New Events Section -->
    <section class="container mt-5">
        <h2 class="section-title" style="color: #ff5722;">SỰ KIỆN MỚI</h2>
        <div class="row">
            <?php if (!empty($newEvents)): ?>
                <?php foreach (array_slice($newEvents, 0, 4) as $event): ?>
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
                                <a href="views/product/productdetails.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
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
                            <a href="views/product/productdetails.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
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

    <?php include_once __DIR__ . '/views/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
