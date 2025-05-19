<?php
function getUserAvatarPath($filename) {
    if (!$filename || $filename === 'avatar.jpg') {
        return '/SuKien/cnm/Hinh/avatar/avatar.jpg';
    }
    if ($filename[0] === '/') {
        return $filename;
    }
    return '/SuKien/cnm/' . ltrim($filename, '/');
}
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/SuKien/cnm/index.php">
            <img src="/SuKien/cnm/Hinh/logo/logo.png" alt="Logo" height="40" class="me-2">
            HỆ THỐNG TỔ CHỨC SỰ KIỆN
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/SuKien/cnm/index.php">Trang chủ</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): 
                    $role_id = $_SESSION['role_id'] ?? 0;
                ?>
                    <?php if ($role_id == 1): // Admin ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/manager/staff.php">Quản lý tài khoản</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/manager/events.php">Quản lý sự kiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/manager/statistics.php">Xem thống kê</a>
                        </li>
                    <?php elseif ($role_id == 2): // Khách hàng ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/product/allproduct.php">Sự kiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/contact.php">Liên hệ hỗ trợ</a>
                        </li>
                    <?php elseif ($role_id == 3): // Đơn vị tổ chức ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/product/allproduct.php">Sự kiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/manager/events.php">Quản lý sự kiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/organizer/statistics.php">Xem thống kê</a>
                        </li>
                    <?php elseif ($role_id == 4): // Nhân viên hỗ trợ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/support/manage-events.php">Quản lý sự kiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/SuKien/cnm/views/support/chat.php">Chat hỗ trợ</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (in_array($role_id, [2,3])): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="/SuKien/cnm/views/my-tickets.php" title="Vé của tôi">
                                <i class="fas fa-ticket-alt fa-lg"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="/SuKien/cnm/views/product/cart.php" title="Giỏ hàng">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img id="navbarUserAvatar" src="<?php echo getUserAvatarPath($_SESSION['HinhAnh'] ?? null); ?>" alt="User Avatar" class="me-2" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                            <?php 
                            if (isset($_SESSION['user_fullname']) && !empty($_SESSION['user_fullname'])) {
                                echo htmlspecialchars($_SESSION['user_fullname']);
                            } elseif (isset($_SESSION['username'])) {
                                echo htmlspecialchars($_SESSION['username']);
                            } else {
                                echo 'Bạn';
                            }
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/SuKien/cnm/views/user/profile.php">Thông tin cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/SuKien/cnm/api/auth.php?action=logout">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/SuKien/cnm/views/auth/login.php">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/SuKien/cnm/views/auth/register.php">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/SuKien/cnm/api/profile.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data && data.data.HinhAnh) {
                function getUserAvatarPath(filename) {
                    if (!filename || filename === 'avatar.jpg') return '/SuKien/cnm/Hinh/avatar/avatar.jpg';
                    if (filename.startsWith('http') || filename.startsWith('/')) return filename;
                    return '/SuKien/cnm/' + filename.replace(/^\/+/, '');
                }
                document.getElementById('navbarUserAvatar').src = getUserAvatarPath(data.data.HinhAnh);
            }
        });
});
</script> 