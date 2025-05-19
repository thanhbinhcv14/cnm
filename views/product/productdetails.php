<?php
require_once '../../config/config.php';

// Lấy ID sự kiện từ URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin sự kiện từ API
$eventApiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/events.php?id=' . $event_id;
$eventResponse = file_get_contents($eventApiUrl);
$event = null;

if ($eventResponse !== false) {
    $eventData = json_decode($eventResponse, true);
    if (isset($eventData['success']) && $eventData['success'] && isset($eventData['data'])) {
        $event = $eventData['data'];
    }
}

// Lấy danh sách vé từ API
$ticketApiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/tickets.php?event_id=' . $event_id;
$ticketResponse = file_get_contents($ticketApiUrl);
$tickets = [];

if ($ticketResponse !== false) {
    $ticketData = json_decode($ticketResponse, true);
    if (isset($ticketData['success']) && $ticketData['success'] && isset($ticketData['data'])) {
        $tickets = $ticketData['data'];
    }
}

if (!$event) {
    header("Location: ../../index.php");
    exit();
}

// function getBaseUrl() {
//     $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
//     $host = $_SERVER['HTTP_HOST'];
//     $script = $_SERVER['SCRIPT_NAME'];
//     $path = dirname($script);
//     return rtrim($protocol . '://' . $host . $path, '/') . '/';
// }

function getEventImagePath($filename) {
    if (!$filename) return '../../Hinh/logo/logo.png';
    $posterPath = '../../Hinh/poster/' . $filename;
    $mainPath = '../../Hinh/' . $filename;
    if (file_exists($posterPath)) return $posterPath;
    if (file_exists($mainPath)) return $mainPath;
    return '../../Hinh/logo/logo.png';
}

// //Hàm chuẩn hóa đường dẫn avatar cho user
// function getUserAvatar($filename) {
//     if (!$filename || $filename === 'avatar.jpg') {
//         return '/SuKien/cnm/Hinh/avatar/avatar.jpg';
//     }
//     if ($filename[0] === '/') {
//         return $filename;
//     }
//     return 'SuKien/cnm/' . ltrim($filename, '/');
// }

function getQrCodePath($filename) {
    if (!$filename) return '/SuKien/cnm/Hinh/qr_codes/default.png';
    // Loại bỏ ../ ở đầu nếu có
    $filename = preg_replace('#^\.\./#', '', $filename);
    // Nếu đã có Hinh/qr_codes ở đầu thì thêm /SuKien/cnm/ phía trước
    if (strpos($filename, 'Hinh/qr_codes/') === 0) {
        return '/SuKien/cnm/' . $filename;
    }
    // Nếu đã là đường dẫn tuyệt đối
    if ($filename[0] === '/') return $filename;
    // Trường hợp còn lại
    return '/SuKien/cnm/Hinh/qr_codes/' . $filename;
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Trang chủ - Hệ thống sự kiện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
        --primary-color: #0f9d58;
        --secondary-color: #000000;
    }
    body, .event-details {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .navbar {
        background: linear-gradient(to right, var(--secondary-color), var(--primary-color)) !important;
        padding: 1rem 0;
    }
    .navbar-brand {
        color: white !important;
        font-weight: bold;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
    }
    .navbar-brand img {
        height: 60px;
        width: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        margin-right: 12px;
    }
    .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        transition: color 0.3s ease;
    }
    .nav-link:hover, .nav-link.active {
        color: #fff !important;
    }
    .dropdown-menu {
        background-color: #fff;
    }
    .dropdown-menu .dropdown-item {
        color: #000;
        transition: background 0.2s, color 0.2s;
    }
    .dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
        background-color: #0f9d58;
        color: #fff;
    }
    .event-header {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .event-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    .event-image:hover {
        transform: scale(1.02);
    }
    .event-info {
        padding: 2rem;
    }
    .event-title {
        font-size: 2rem;
        font-weight: bold;
        margin: 20px 0 15px;
        color: var(--primary-color);
    }
    .event-meta {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .event-meta p {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    .event-meta i {
        width: 30px;
        text-align: center;
    }
    .event-description {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .event-description h4 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }
    .event-description p {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #333;
    }
    .ticket-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        margin-bottom: 30px;
        padding: 2rem;
        position: sticky;
        top: 20px;
    }
    .ticket-card:hover {
        transform: translateY(-5px);
    }
    .ticket-type {
        font-size: 1.3rem;
        font-weight: bold;
        color: #333;
    }
    .ticket-price {
        font-size: 1.8rem;
        color: #ff5722;
        font-weight: bold;
    }
    .quantity-control {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1rem 0;
    }
    .quantity-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 1px solid #ddd;
        background: white;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .quantity-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .quantity-input {
        width: 80px;
        height: 45px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 0.5rem;
        font-size: 1.2rem;
    }
    .btn-buy {
        background: #ff5722;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 25px;
        font-weight: bold;
        font-size: 1.2rem;
        transition: all 0.3s;
        width: 100%;
        margin-top: 1rem;
    }
    .btn-buy:hover {
        background: #e64a19;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
    }
    .login-required {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        margin-top: 1rem;
    }
    .login-required a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: bold;
    }
    .login-required a:hover {
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .event-image {
            height: 300px;
        }
        .event-title {
            font-size: 1.6rem;
        }
        .ticket-card {
            position: static;
            margin-top: 2rem;
        }
    }
    .comment {
        border-bottom: 1px solid #eee;
        padding: 1.5rem 0;
        transition: background-color 0.3s ease;
    }
    .comment:hover {
        background-color: #f8f9fa;
    }
    .comment:last-child {
        border-bottom: none;
    }
    .comment-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .comment-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eee;
    }
    .comment-user {
        font-weight: bold;
        margin-right: 1rem;
        color: #0f9d58;
    }
    .comment-date {
        color: #666;
        font-size: 0.9rem;
    }
    .comment-content {
        margin-left: 3.5rem;
        color: #333;
        line-height: 1.5;
        white-space: pre-wrap;
    }
    .comment-actions {
        margin-left: 3.5rem;
        margin-top: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .comment:hover .comment-actions {
        opacity: 1;
    }
    .comment-actions button {
        padding: 0.2rem 0.8rem;
        font-size: 0.9rem;
        border-radius: 15px;
    }
    .new-comment {
        animation: highlightNew 2s ease-out;
    }
    @keyframes highlightNew {
        0% { background-color: #e3f2fd; }
        100% { background-color: transparent; }
    }
</style>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Event Image and Basic Info -->
            <div class="col-md-8">
                <div class="event-header">
                    <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" 
                         alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" 
                         class="event-image">
                    <div class="event-info">
                        <h1 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h1>
                        <div class="event-meta mb-4">
                            <p class="mb-2">
                                <i class="fas fa-calendar-alt text-primary"></i> 
                                <strong>Thời gian:</strong> 
                                <?php echo date('d/m/Y H:i', strtotime($event['ThoiGianBatDau'])); ?> - 
                                <?php echo date('d/m/Y H:i', strtotime($event['ThoiGianKetThuc'])); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-danger"></i> 
                                <strong>Địa điểm:</strong> 
                                <?php echo htmlspecialchars($event['TenDiaDiem']); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-user text-success"></i> 
                                <strong>Người tổ chức:</strong> 
                                <?php echo htmlspecialchars($event['organizer_name']); ?>
                            </p>
                        </div>
                        <div class="event-description">
                            <h4>Mô tả sự kiện</h4>
                            <p><?php echo nl2br(htmlspecialchars($event['NoiDung'] ?? 'Chưa có mô tả')); ?></p>
                            <?php if (!empty($event['qrcode'])): ?>                                
                                </div>
                                <div class="event-description">
                                    <h4>Mã QR sự kiện</h4>
                                    <div class="text-center">
                                        <img src="<?php echo getQrCodePath($event['qrcode']); ?>" alt="QR Code Sự kiện" style="max-width:200px;">
                                    </div>
                                    
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Purchase Section -->
            <div class="col-md-4">
                <div class="ticket-card">
                    <h3 class="mb-4">Mua vé</h3>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="ticket-option mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="ticket-type"><?php echo htmlspecialchars($ticket['HangVe'] ?? 'Vé thường'); ?></span>
                                    <span class="ticket-price"><?php echo number_format($ticket['GiaVe'] ?? 0, 0, ',', '.'); ?>đ</span>
                                </div>
                                <div class="quantity-control">
                                    <button class="quantity-btn" onclick="decreaseQuantity(<?php echo $ticket['ID_Ve']; ?>)">-</button>
                                    <input type="number" id="quantity-<?php echo $ticket['ID_Ve']; ?>" 
                                           class="quantity-input" value="0" min="0" 
                                           max="<?php echo $ticket['SoLuong'] ?? 0; ?>" readonly>
                                    <button class="quantity-btn" onclick="increaseQuantity(<?php echo $ticket['ID_Ve']; ?>)">+</button>
                                </div>
                                <p class="text-muted mt-2">
                                    Còn lại: <?php echo $ticket['SoLuong'] ?? 0; ?> vé
                                </p>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-buy w-100" onclick="addToCart()">
                                Thêm vào giỏ hàng
                            </button>
                        <?php else: ?>
                            <div class="login-required">
                                <p>Vui lòng <a href="../../views/auth/login.php">đăng nhập</a> để mua vé</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Chưa có vé được bán cho sự kiện này.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Bình luận</h4>
                        <span id="commentCount" class="badge bg-light text-primary">0 bình luận</span>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form id="commentForm" class="mb-4">
                                <div class="form-group">
                                    <div class="d-flex align-items-start">
                                        <img src="<?php echo (getUserAvatarPath($_SESSION['HinhAnh'] ?? null)) ?>" 
                                             alt="Your avatar" 
                                             class="comment-avatar me-3">
                                        <div class="flex-grow-1">
                                            <textarea class="form-control" id="commentContent" rows="3" 
                                                      placeholder="Viết bình luận của bạn..." required></textarea>
                                            <button type="submit" class="btn btn-primary mt-2">
                                                <i class="fas fa-paper-plane me-2"></i>Gửi bình luận
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Vui lòng <a href="../../views/auth/login.php" class="alert-link">đăng nhập</a> để bình luận
                            </div>
                        <?php endif; ?>

                        <div id="commentsContainer">
                            <div id="commentsList" class="mt-4">
                                <!-- Comments will be loaded here -->
                                <div class="text-center" id="loadingComments">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải bình luận...</span>
                                    </div>
                                </div>
                            </div>
                            <div id="noComments" class="text-center mt-4 d-none">
                                <i class="fas fa-comments text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function increaseQuantity(ticketId) {
            const input = document.getElementById(`quantity-${ticketId}`);
            const max = parseInt(input.getAttribute('max'));
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQuantity(ticketId) {
            const input = document.getElementById(`quantity-${ticketId}`);
            if (parseInt(input.value) > 0) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function addToCart() {
            const tickets = <?php echo json_encode($tickets); ?>;
            let added = 0;
            let errors = [];
            let totalToAdd = 0;

            tickets.forEach(ticket => {
                const quantity = parseInt(document.getElementById(`quantity-${ticket.ID_Ve}`).value);
                if (quantity > 0) {
                    totalToAdd++;
                    fetch('../../api/cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            ID_Ve: ticket.ID_Ve,
                            SoLuong: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            added++;
                        } else {
                            errors.push(data.message);
                        }
                        if (added + errors.length === totalToAdd) {
                            if (added > 0) {
                                alert('Đã thêm vào giỏ hàng thành công');
                                window.location.href = '../../views/product/cart.php';
                            } else {
                                alert('Có lỗi xảy ra: ' + errors.join('; '));
                            }
                        }
                    })
                    .catch(error => {
                        errors.push('Lỗi mạng');
                        if (added + errors.length === totalToAdd) {
                            if (added > 0) {
                                alert('Đã thêm vào giỏ hàng thành công');
                                window.location.href = '../../views/product/cart.php';
                            } else {
                                alert('Có lỗi xảy ra: ' + errors.join('; '));
                            }
                        }
                    });
                }
            });

            if (tickets.every(ticket => parseInt(document.getElementById(`quantity-${ticket.ID_Ve}`).value) === 0)) {
                alert('Vui lòng chọn ít nhất một vé');
            }
        }

        // Debug function
        function debugLog(message, data) {
            console.log(`[Debug] ${message}:`, data);
        }

        // Simplified loadComments function
        function loadComments() {
            const loadingSpinner = document.getElementById('loadingComments');
            const commentsList = document.getElementById('commentsList');
            const noComments = document.getElementById('noComments');
            const commentCount = document.getElementById('commentCount');

            if (loadingSpinner) loadingSpinner.style.display = 'block';
            if (commentsList) commentsList.innerHTML = '';
            if (noComments) noComments.classList.add('d-none');

            const eventId = <?php echo $event['ID_Sukien']; ?>;
            debugLog('Loading comments for event ID', eventId);

            fetch(`../../api/comments.php?event_id=${eventId}`)
                .then(response => {
                    debugLog('Response status', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    debugLog('Comments data', data);
                    if (loadingSpinner) loadingSpinner.style.display = 'none';
                    
                    if (data.success) {
                        const comments = data.data;
                        if (commentCount) commentCount.textContent = `${comments.length} bình luận`;

                        if (comments.length === 0) {
                            if (noComments) noComments.classList.remove('d-none');
                            return;
                        }

                        comments.forEach(comment => {
                            const commentElement = document.createElement('div');
                            commentElement.className = 'comment';
                            commentElement.innerHTML = `
                                <div class="comment-header">
                                    <img src="${comment.user_avatar || '../../Hinh/avatar/avatar.jpg'}" 
                                         alt="${comment.user_name}" 
                                         class="comment-avatar">
                                    <div class="ms-3">
                                        <div class="comment-user">${comment.user_name}</div>
                                        <div class="comment-date">
                                            <i class="far fa-clock me-1"></i>
                                            ${formatDate(comment.NgayTao)}
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-content">${formatContent(comment.NoiDung)}</div>
                                ${comment.ID_User == <?php echo $_SESSION['user_id'] ?? 0; ?> || <?php echo $_SESSION['role_id'] ?? 0; ?> == 1 ? `
                                    <div class="comment-actions">
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteComment(${comment.ID_BinhLuan})">
                                            <i class="fas fa-trash-alt me-1"></i>Xóa
                                        </button>
                                    </div>
                                ` : ''}
                            `;
                            if (commentsList) commentsList.appendChild(commentElement);
                        });
                    } else {
                        throw new Error(data.message || 'Có lỗi xảy ra khi tải bình luận');
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    debugLog('Error details', error);
                    if (loadingSpinner) loadingSpinner.style.display = 'none';
                    if (commentsList) commentsList.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${error.message || 'Có lỗi xảy ra khi tải bình luận'}
                        </div>
                    `;
                });
        }

        // Format date to relative time
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'Vừa xong';
            if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
            if (diff < 2592000) return `${Math.floor(diff / 86400)} ngày trước`;
            
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Format comment content
        function formatContent(content) {
            return content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/\n/g, '<br>');
        }

        // Submit comment
        document.getElementById('commentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const content = document.getElementById('commentContent').value.trim();
            
            if (!content) {
                alert('Vui lòng nhập nội dung bình luận');
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';

            const eventId = <?php echo $event['ID_Sukien']; ?>;
            debugLog('Submitting comment for event ID', eventId);

            const commentData = {
                ID_Sukien: eventId,
                NoiDung: content
            };
            debugLog('Comment data', commentData);

            fetch('../../api/comments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(commentData)
            })
            .then(response => {
                debugLog('Response status', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                debugLog('Submit response', data);
                if (data.success) {
                    document.getElementById('commentContent').value = '';
                    loadComments();
                    document.getElementById('commentsList').scrollIntoView({ behavior: 'smooth' });
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra khi gửi bình luận');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                debugLog('Error details', error);
                alert(error.message || 'Có lỗi xảy ra khi gửi bình luận');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi bình luận';
            });
        });

        // Delete comment
        function deleteComment(commentId) {
            if (!commentId || !confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                return;
            }

            debugLog('Deleting comment ID', commentId);

            const commentElement = document.querySelector(`[onclick=\"deleteComment(${commentId})\"]`)?.closest('.comment');
            if (commentElement) {
                commentElement.style.opacity = '0.5';
            }

            fetch('../../api/comments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&ID_BinhLuan=${commentId}`
            })
            .then(response => {
                debugLog('Response status', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                debugLog('Delete response', data);
                if (data.success) {
                    if (commentElement) {
                        commentElement.style.height = commentElement.offsetHeight + 'px';
                        commentElement.style.transition = 'all 0.3s ease';
                        setTimeout(() => {
                            commentElement.style.height = '0';
                            commentElement.style.padding = '0';
                            commentElement.style.margin = '0';
                            setTimeout(() => {
                                loadComments();
                            }, 300);
                        }, 100);
                    } else {
                        loadComments();
                    }
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra khi xóa bình luận');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                debugLog('Error details', error);
                alert(error.message || 'Có lỗi xảy ra khi xóa bình luận');
                if (commentElement) {
                    commentElement.style.opacity = '1';
                }
            });
        }

        // Load comments when page loads
        document.addEventListener('DOMContentLoaded', loadComments);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
