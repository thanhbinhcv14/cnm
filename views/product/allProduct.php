<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/events.php';
if (!empty($search)) {
    $apiUrl .= '?search=' . urlencode($search);
}
$response = file_get_contents($apiUrl);
$events = [];
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        $events = $data['data'];
    }
}
function getEventImagePath($filename) {
    if (!$filename) return '../../Hinh/logo/logo.png';
    $posterPath = '../../Hinh/poster/' . $filename;
    $mainPath = '../../Hinh/' . $filename;
    if (file_exists($posterPath)) return $posterPath;
    if (file_exists($mainPath)) return $mainPath;
    return '../../Hinh/logo/logo.png';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả sự kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../includes/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container mt-4">
        <h1 class="section-title">TẤT CẢ SỰ KIỆN</h1>
        <!-- Form tìm kiếm -->
        <form action="" method="GET" class="mb-4 d-flex justify-content-center">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sự kiện..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary rounded-end">
                    <i class="fas fa-search text-white"></i>
                </button>
            </div>
        </form>
        <!-- Hiển thị danh sách sự kiện -->
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-3 mb-4">
                    <div class="event-card">
                        <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" 
                             alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" 
                             class="event-image w-100"
                             onerror="this.onerror=null;this.src='../../Hinh/logo/logo.png';">
                        <div class="p-3">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                            <p class="event-info">
                                <i class="fas fa-calendar"></i> 
                                <span class="event-date"><?php echo date('d/m/Y H:i', strtotime($event['ThoiGianBatDau'])); ?></span>
                            </p>
                            <p class="event-info">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php echo htmlspecialchars($event['DiaDiem'] ?? ($event['TenDiaDiem'] ?? '')); ?>
                            </p>
                            <a href="productdetails.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php include_once __DIR__ . '/../partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
