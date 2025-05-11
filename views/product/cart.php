<?php
require_once '../../config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Giỏ hàng - Hệ thống sự kiện</title>
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
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color)) !important;
        }
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .cart-item-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .cart-item-price {
            font-size: 1.1rem;
            color: #ff5722;
            font-weight: bold;
        }
        .cart-item-quantity {
            font-size: 1rem;
            color: #666;
        }
        .cart-total {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        .btn-remove {
            color: #dc3545;
            cursor: pointer;
            transition: color 0.3s;
        }
        .btn-remove:hover {
            color: #c82333;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
        }
        .empty-cart i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        .btn-checkout {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-checkout:hover {
            background-color: #0c7c45;
            transform: translateY(-2px);
        }
        .dropdown-menu {
            background-color: #fff;
        }
        .dropdown-menu .dropdown-item {
            color: #000;
            transition: background 0.2s, color 0.2s;
            padding: 8px 20px;
            font-size: 0.95rem;
            background-color: transparent;
        }
        .dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
            background-color: #0f9d58;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container mt-4">
        <div class="cart-container">
            <h2 class="mb-4">Giỏ hàng của bạn</h2>
            
            <div class="row">
                <div class="col-md-8">
                    <div id="cart-items">
                        <!-- Cart items will be loaded here -->
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="cart-total">
                        <h4 class="mb-3">Tổng cộng</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span id="subtotal">0 đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí dịch vụ:</span>
                            <span>0 đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Tổng tiền:</span>
                            <span id="total" class="fw-bold text-danger">0 đ</span>
                        </div>
                        <button class="btn btn-checkout w-100" id="checkout-btn">
                            Thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chọn phương thức thanh toán -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="paymentModalLabel">Chọn phương thức thanh toán</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-around mb-4">
              <button class="btn btn-outline-success" id="pay-momo">Momo QR</button>
              <button class="btn btn-outline-primary" id="pay-vnpay">VNPAY QR</button>
            </div>
            <div id="qr-section" class="text-center" style="display:none;">
              <h6 id="qr-title"></h6>
              <img id="qr-image" src="" alt="QR Code" style="max-width:250px;max-height:250px;">
              <p class="mt-3">Vui lòng quét mã để thanh toán</p>
              <button class="btn btn-success mt-2" data-bs-dismiss="modal">Tôi đã thanh toán</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        // Hàm lấy đường dẫn hình ảnh sự kiện
        function getEventImagePath(filename) {
            if (!filename) return '../../Hinh/logo/logo.png';
            if (filename.endsWith('.jpg') || filename.endsWith('.jpeg') || filename.endsWith('.png') || filename.endsWith('.webp')) {
                return '../../Hinh/poster/' + filename;
            }
            return '../../Hinh/logo/logo.png';
        }

        function loadCart() {
            $.ajax({
                url: '../../api/cart.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const cartItems = response.data;
                        let html = '';
                        let subtotal = 0;

                        if (cartItems.length === 0) {
                            html = `
                                <div class="empty-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h4>Giỏ hàng trống</h4>
                                    <p>Hãy thêm vé vào giỏ hàng để tiếp tục</p>
                                </div>
                            `;
                        } else {
                            cartItems.forEach(item => {
                                const itemTotal = item.SoLuong * item.GiaVe;
                                subtotal += itemTotal;
                                html += `
                                    <div class="cart-item" data-id="${item.ID_GioHang}">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <img src="${'../../Hinh/poster/' + item.HinhAnh}" alt="${item.TenSuKien}" class="cart-item-image"
                                                     onerror="this.onerror=null;this.src='../../Hinh/' + item.HinhAnh;">
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="cart-item-title">${item.TenSuKien}</h5>
                                                <p class="cart-item-quantity">${item.HangVe}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <p class="cart-item-price">${formatCurrency(item.GiaVe)}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <p class="cart-item-quantity">Số lượng: ${item.SoLuong}</p>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <i class="fas fa-trash btn-remove" onclick="removeFromCart(${item.ID_GioHang})"></i>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }

                        $('#cart-items').html(html);
                        $('#subtotal').text(formatCurrency(subtotal));
                        $('#total').text(formatCurrency(subtotal));
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi tải giỏ hàng');
                }
            });
        }

        function removeFromCart(cartId) {
            if (confirm('Bạn có chắc chắn muốn xóa vé này khỏi giỏ hàng?')) {
                $.ajax({
                    url: '../../api/cart.php',
                    type: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ ID_GioHang: cartId }),
                    success: function(response) {
                        if (response.success) {
                            loadCart();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xóa vé');
                    }
                });
            }
        }

        $(document).ready(function() {
            loadCart();
            $('#checkout-btn').click(function() {
                window.location.href = 'checkout.php';
            });
        });
    </script>
</body>
</html>
