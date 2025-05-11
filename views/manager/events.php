<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'] ?? 0, [1,3])) {
    header('Location: ../../index.php');
    exit;
}
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/events.php';
if (($_SESSION['role_id'] ?? 0) == 3) {
    $apiUrl .= '?user_id=' . $_SESSION['user_id'];
}
$response = file_get_contents($apiUrl);
$events = [];
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        $events = $data['data'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sự kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../includes/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container mt-4">
        <h1 class="mb-4">Quản lý sự kiện</h1>
        <!-- Nút Thêm sự kiện -->
        <div class="mb-3 text-end">
            <button class="btn btn-success" id="btnOpenAddModal"><i class="fas fa-plus"></i> Thêm sự kiện</button>
        </div>
        <!-- Modal Thêm/Sửa sự kiện -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Thêm sự kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="eventFormModal">
                  <input type="hidden" name="ID_SuKien" id="ID_SuKien">
                  <div class="mb-3">
                    <label for="TenSuKien" class="form-label">Tên sự kiện</label>
                    <input type="text" name="TenSuKien" id="TenSuKien" class="form-control" placeholder="Tên sự kiện" required>
                  </div>
                  <div class="mb-3">
                    <label for="ThoiGianBatDau" class="form-label">Thời gian bắt đầu</label>
                    <input type="datetime-local" name="ThoiGianBatDau" id="ThoiGianBatDau" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label for="ThoiGianKetThuc" class="form-label">Thời gian kết thúc</label>
                    <input type="datetime-local" name="ThoiGianKetThuc" id="ThoiGianKetThuc" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label for="DiaDiem" class="form-label">Địa điểm</label>
                    <input type="text" name="DiaDiem" id="DiaDiem" class="form-control" placeholder="Địa điểm" required>
                  </div>
                  <div class="mb-3">
                    <label for="HinhAnh" class="form-label">Tên file hình</label>
                    <input type="text" name="HinhAnh" id="HinhAnh" class="form-control" placeholder="Tên file hình">
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-success" id="btnSaveModal">Lưu</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Danh sách sự kiện -->
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Tên sự kiện</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Địa điểm</th>
                    <th>Hình ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="eventTable">
                <?php foreach ($events as $event): ?>
                    <tr data-id="<?php echo $event['ID_SuKien']; ?>">
                        <td><?php echo htmlspecialchars($event['TenSuKien']); ?></td>
                        <td><?php echo htmlspecialchars($event['ThoiGianBatDau']); ?></td>
                        <td><?php echo htmlspecialchars($event['ThoiGianKetThuc']); ?></td>
                        <td><?php echo htmlspecialchars($event['DiaDiem'] ?? ($event['TenDiaDiem'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($event['HinhAnh']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $event['ID_SuKien']; ?>">Sửa</button>
                            <button class="btn btn-danger btn-sm btn-delete">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mở modal thêm mới
    document.getElementById('btnOpenAddModal').onclick = function() {
        document.getElementById('eventModalLabel').textContent = 'Thêm sự kiện';
        document.getElementById('eventFormModal').reset();
        document.getElementById('ID_SuKien').value = '';
        document.getElementById('btnSaveModal').textContent = 'Thêm';
        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
        modal.show();
    };
    // Mở modal sửa
    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.onclick = function() {
            var tr = this.closest('tr');
            document.getElementById('eventModalLabel').textContent = 'Sửa sự kiện';
            document.getElementById('ID_SuKien').value = tr.dataset.id;
            document.getElementById('TenSuKien').value = tr.children[0].textContent;
            document.getElementById('ThoiGianBatDau').value = tr.children[1].textContent.replace(' ', 'T');
            document.getElementById('ThoiGianKetThuc').value = tr.children[2].textContent.replace(' ', 'T');
            document.getElementById('DiaDiem').value = tr.children[3].textContent;
            document.getElementById('HinhAnh').value = tr.children[4].textContent;
            document.getElementById('btnSaveModal').textContent = 'Lưu';
            var modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        };
    });
    // Submit form modal (thêm/sửa)
    document.getElementById('btnSaveModal').onclick = function() {
        var id = document.getElementById('ID_SuKien').value;
        var data = {
            TenSuKien: document.getElementById('TenSuKien').value,
            HinhAnh: document.getElementById('HinhAnh').value,
            ThoiGianBatDau: document.getElementById('ThoiGianBatDau').value,
            ThoiGianKetThuc: document.getElementById('ThoiGianKetThuc').value,
            ID_DiaDiem: 1, // Cần sửa lại cho đúng nếu có dropdown địa điểm
            DiaDiem: document.getElementById('DiaDiem').value,
            ID_User: <?php echo $_SESSION['user_id']; ?>
        };
        if (id) {
            // Sửa
            data.ID_SuKien = id;
            fetch('../../api/events.php', {
                method: 'PUT',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(data)
            }).then(res => res.json()).then(res => location.reload());
        } else {
            // Thêm
            fetch('../../api/events.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            }).then(res => res.json()).then(res => location.reload());
        }
    };
    // Xóa
    Array.from(document.querySelectorAll('.btn-delete')).forEach(btn => {
        btn.onclick = function() {
            if (confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) {
                const id = this.closest('tr').dataset.id;
                fetch('../../api/events.php', {
                    method: 'DELETE',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'ID_SuKien=' + id
                }).then(res => res.json()).then(res => location.reload());
            }
        }
    });
    </script>
</body>
</html>
