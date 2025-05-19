<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1,3])) {
    header('Location: ../../index.php');
    exit;
}
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$eventId) {
    echo '<div class="alert alert-danger">Không tìm thấy sự kiện!</div>';
    exit;
}
$apiUrl = '../../api/events.php?id=' . $eventId;
$ticketStatsUrl = '../../api/events.php?id=' . $eventId . '&ticket_stats=1';
// Lấy thông tin sự kiện
$event = null;
$ticketStats = [];
$response = @file_get_contents($apiUrl);
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        $event = $data['data'];
    }
}
// Lấy thống kê vé
$response2 = @file_get_contents($ticketStatsUrl);
if ($response2 !== false) {
    $data2 = json_decode($response2, true);
    if (isset($data2['success']) && $data2['success'] && isset($data2['data'])) {
        $ticketStats = $data2['data'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê sự kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Thống kê sự kiện</h1>
        <?php if ($event): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                    <p><strong>Thời gian:</strong> <?php echo htmlspecialchars($event['ThoiGianBatDau']); ?> - <?php echo htmlspecialchars($event['ThoiGianKetThuc']); ?></p>
                    <p><strong>Địa điểm:</strong> <?php echo htmlspecialchars($event['TenDiaDiem'] ?? $event['DiaDiem']); ?></p>
                    <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($event['MoTa'] ?? ''); ?></p>
                </div>
            </div>
            <div class="mb-4">
                <h4>Bảng thống kê vé theo loại</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Hạng vé</th>
                            <th>Giá vé</th>
                            <th>Số lượng còn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ticketStats as $ticket): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ticket['HangVe']); ?></td>
                                <td><?php echo number_format($ticket['GiaVe']); ?> VNĐ</td>
                                <td><?php echo htmlspecialchars($ticket['SoLuongCon']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['TrangThai']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mb-4">
                <h4>Biểu đồ vé theo loại</h4>
                <canvas id="ticketChart" height="120"></canvas>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Không tìm thấy sự kiện!</div>
        <?php endif; ?>
        <a href="events.php" class="btn btn-secondary">Quay lại</a>
    </div>
    <script>
    // Dữ liệu cho biểu đồ
    const ticketStats = <?php echo json_encode($ticketStats); ?>;
    const labels = ticketStats.map(t => t.HangVe + ' - ' + Number(t.GiaVe).toLocaleString('vi-VN') + ' VNĐ');
    const soLuongCon = ticketStats.map(t => Number(t.SoLuongCon));
    // Vẽ biểu đồ cột
    const ctx = document.getElementById('ticketChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số lượng vé còn',
                data: soLuongCon,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Số lượng vé còn theo từng loại vé' }
            }
        }
    });
    </script>
</body>
</html>
