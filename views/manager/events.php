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
        <!-- Danh sách sự kiện -->
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Tên sự kiện</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Địa điểm</th>
                    <th>Thể loại</th>
                    <th>Hình ảnh</th>
                    <th>Tổng số vé</th>
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
                        <td><?php echo htmlspecialchars($event['TheLoai'] ?? ''); ?></td>
                        <td>
                            <?php if (!empty($event['HinhAnh'])): ?>
                                <img src="../../Hinh/poster/<?php echo htmlspecialchars($event['HinhAnh']); ?>" alt="Hình sự kiện" style="max-width:80px;max-height:60px;object-fit:cover;">
                            <?php else: ?>
                                <img src="../../Hinh/logo/logo.png" alt="No image" style="max-width:80px;max-height:60px;object-fit:cover;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $event['TongSoVe']; ?></td>
                       
                        <td>
                            <a class="btn btn-info btn-sm" href="reports.php?id=<?php echo $event['ID_SuKien']; ?>">Xem thống kê</a>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $event['ID_SuKien']; ?>">Sửa</button>
                            <button class="btn btn-danger btn-sm btn-delete">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Chi tiết sự kiện -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Chi tiết sự kiện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Thông tin sự kiện</h4>
                            <div id="eventDetailInfo"></div>
                        </div>
                        <div class="col-md-6">
                            <h4>Thông tin vé</h4>
                            <div id="ticketDetailInfo"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa sự kiện -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Thêm sự kiện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="eventFormModal" enctype="multipart/form-data">
    <input type="hidden" name="ID_SuKien" id="ID_SuKien">

    <!-- THÔNG TIN CHUNG -->
    <div class="mb-4">
        <h5 class="fw-bold text-primary">Thông tin sự kiện</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="TenSuKien" class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
                <input type="text" name="TenSuKien" id="TenSuKien" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="TheLoai" class="form-label">Thể loại</label>
                <input type="text" name="TheLoai" id="TheLoai" class="form-control" placeholder="Ví dụ: Âm nhạc, Hội thảo...">
            </div>
        </div>
    </div>

    <!-- THỜI GIAN -->
    <div class="mb-4">
        <h5 class="fw-bold text-primary">Thời gian tổ chức</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ThoiGianBatDau" class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                <input type="datetime-local" name="ThoiGianBatDau" id="ThoiGianBatDau" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="ThoiGianKetThuc" class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                <input type="datetime-local" name="ThoiGianKetThuc" id="ThoiGianKetThuc" class="form-control" required>
            </div>
        </div>
    </div>

    <!-- ĐỊA ĐIỂM VÀ MÔ TẢ -->
    <div class="mb-4">
        <h5 class="fw-bold text-primary">Địa điểm & Mô tả</h5>
        <div class="mb-3">
            <label for="DiaDiem" class="form-label">Địa điểm tổ chức <span class="text-danger">*</span></label>
            <input type="text" name="DiaDiem" id="DiaDiem" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="MoTa" class="form-label">Mô tả chi tiết</label>
            <textarea name="MoTa" id="MoTa" class="form-control" rows="4" placeholder="Nội dung mô tả sự kiện..."></textarea>
        </div>
    </div>

    <!-- HÌNH ẢNH -->
    <div class="mb-4">
        <h5 class="fw-bold text-primary">Hình ảnh sự kiện</h5>
        <div class="mb-3">
            <input type="file" name="HinhAnh" id="HinhAnh" class="form-control" accept="image/*">
        </div>
    </div>

    <!-- HẠNG VÉ -->
    <div class="mb-4">
        <h5 class="fw-bold text-primary">Thông tin hạng vé</h5>
        <div class="mb-3 col-md-3">
            <label for="soLuongHangVe" class="form-label">Số lượng hạng vé <span class="text-danger">*</span></label>
            <select id="soLuongHangVe" name="soLuongHangVe" class="form-select" required>
                <option value="1" selected>1</option>
                <option value="2">2</option>
            </select>
        </div>
        <div id="hangVeInputs" class="row"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mở modal thêm mới
    document.getElementById('btnOpenAddModal').onclick = function() {
        document.getElementById('eventModalLabel').textContent = 'Thêm sự kiện';
        document.getElementById('eventFormModal').reset();
        document.getElementById('ID_SuKien').value = '';
        document.getElementById('btnSaveModal').textContent = 'Thêm';
        document.getElementById('soLuongHangVe').value = 1;
        renderHangVeInputs(1);
        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
        modal.show();
    };

    // Mở modal chi tiết
    document.querySelectorAll('.btn-view').forEach(function(btn) {
        btn.onclick = function() {
            const id = this.dataset.id;
            fetch(`../../api/events.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const event = data.data;
                        document.getElementById('eventDetailInfo').innerHTML = `
                            <p><strong>Tên sự kiện:</strong> ${event.TenSuKien}</p>
                            <p><strong>Thời gian bắt đầu:</strong> ${event.ThoiGianBatDau}</p>
                            <p><strong>Thời gian kết thúc:</strong> ${event.ThoiGianKetThuc}</p>
                            <p><strong>Địa điểm:</strong> ${event.TenDiaDiem}</p>
                            <p><strong>Mô tả:</strong> ${event.MoTa || 'Không có'}</p>
                        `;
                        document.getElementById('ticketDetailInfo').innerHTML = `
                            <p><strong>Tổng số vé:</strong> ${event.TongSoVe || 0}</p>
                            <p><strong>Đã bán:</strong> ${event.SoVeDaBan || 0}</p>
                            <p><strong>Còn lại:</strong> ${event.SoVeConLai || 0}</p>
                            <p><strong>Giá vé:</strong> ${event.GiaVe ? event.GiaVe.toLocaleString('vi-VN') + ' VNĐ' : 'Chưa có'}</p>
                        `;
                        var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                        modal.show();
                    }
                });
        };
    });

    // Mở modal sửa
    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.onclick = function() {
            const id = this.dataset.id;
            fetch(`../../api/events.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const event = data.data;
                        document.getElementById('eventModalLabel').textContent = 'Sửa sự kiện';
                        document.getElementById('ID_SuKien').value = event.ID_SuKien;
                        document.getElementById('TenSuKien').value = event.TenSuKien;
                        document.getElementById('ThoiGianBatDau').value = event.ThoiGianBatDau.replace(' ', 'T');
                        document.getElementById('ThoiGianKetThuc').value = event.ThoiGianKetThuc.replace(' ', 'T');
                        document.getElementById('DiaDiem').value = event.TenDiaDiem;
                        document.getElementById('GiaVe').value = event.GiaVe || '';
                        document.getElementById('MoTa').value = event.MoTa || '';
                        document.getElementById('btnSaveModal').textContent = 'Lưu';
                        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                    }
                });
        };
    });

    // Submit form modal (thêm/sửa)
    document.getElementById('btnSaveModal').onclick = function() {
        const formData = new FormData(document.getElementById('eventFormModal'));
        const data = Object.fromEntries(formData.entries());
        const id = data.ID_SuKien;

        if (id) {
            // Sửa
            fetch('../../api/events.php', {
                method: 'PUT',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert('Cập nhật thất bại: ' + res.message);
                }
            });
        } else {
            // Thêm
            fetch('../../api/events.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert('Thêm thất bại: ' + res.message);
                }
            });
        }
    };

    // Xóa
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.onclick = function() {
            if (confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) {
                const id = this.closest('tr').dataset.id;
                fetch('../../api/events.php', {
                    method: 'DELETE',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'ID_SuKien=' + id
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert('Xóa thất bại: ' + res.message);
                    }
                });
            }
        }
    });

    // Render input động cho hạng vé
    function renderHangVeInputs(count) {
        let html = '';
        for (let i = 1; i <= count; i++) {
            html += `
            <div class="border rounded p-2 mb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tên hạng vé ${i}</label>
                        <input type="text" class="form-control" name="HangVe[]" placeholder="Ví dụ: Thường, Vip..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số lượng vé</label>
                        <input type="number" class="form-control" name="SoLuongVe[]" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giá vé (VNĐ)</label>
                        <input type="number" class="form-control" name="GiaVe[]" min="0" required>
                    </div>
                </div>
            </div>`;
        }
        document.getElementById('hangVeInputs').innerHTML = html;
    }
    document.getElementById('soLuongHangVe').addEventListener('change', function() {
        renderHangVeInputs(this.value);
    });
    renderHangVeInputs(1);

    // Preview ảnh khi chọn file
    const inputHinhAnh = document.getElementById('HinhAnh');
    const previewHinhAnh = document.getElementById('previewHinhAnh');
    inputHinhAnh.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                previewHinhAnh.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            previewHinhAnh.src = '../../Hinh/logo/logo.png';
        }
    });
    </script>
</body>
</html>
