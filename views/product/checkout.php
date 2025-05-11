<?php
require_once '../../config/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { max-width: 700px; margin-top: 40px; }
        .qr-section { display: none; text-align: center; }
        #qr-image { max-width: 250px; max-height: 250px; }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Thanh toán giỏ hàng</h2>
        <div id="cart-items"></div>
        <div class="mb-3">
            <strong>Tổng tiền: <span id="total"></span></strong>
        </div>
        <h4>Chọn phương thức thanh toán</h4>
        <button class="btn btn-outline-success me-2" id="pay-momo">Momo QR</button>
        <button class="btn btn-outline-primary" id="pay-vnpay">VNPAY QR</button>
        <div id="qr-section" class="qr-section mt-4">
            <h6 id="qr-title"></h6>
            <img id="qr-image" src="" alt="QR Code">
            <p class="mt-3">Vui lòng quét mã để thanh toán</p>
            <button class="btn btn-success mt-2" id="confirm-payment">Tôi đã thanh toán</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
    let cartData = [];
    let totalAmount = 0;
    let selectedMethod = '';
    function loadCart() {
        $.ajax({
            url: '../../api/cart.php',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    cartData = response.data;
                    let html = '<ul class="list-group mb-3">';
                    totalAmount = 0;
                    response.data.forEach(item => {
                        let itemTotal = item.SoLuong * item.GiaVe;
                        totalAmount += itemTotal;
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${item.TenSuKien} (${item.HangVe}) x ${item.SoLuong}</span>
                            <span>${formatCurrency(itemTotal)}</span>
                        </li>`;
                    });
                    html += '</ul>';
                    $('#cart-items').html(html);
                    $('#total').text(formatCurrency(totalAmount));
                } else {
                    $('#cart-items').html('<div class="alert alert-warning">' + response.message + '</div>');
                }
            }
        });
    }
    function getQrDynamic(method) {
        if (!cartData || cartData.length === 0) {
            alert('Chưa có dữ liệu giỏ hàng!');
            return;
        }
        let noidung = '';
        cartData.forEach(item => {
            noidung += item.TenSuKien + ' (' + item.HangVe + ') x ' + item.SoLuong + '; ';
        });
        if (!totalAmount || !method) {
            alert('Thiếu dữ liệu tổng tiền hoặc phương thức!');
            return;
        }
        $.ajax({
            url: '../../api/generate_qr.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                phuongthuc: method,
                tongtien: totalAmount,
                noidung: noidung
            }),
            success: function(response) {
                if (response.success && response.qr_image) {
                    $('#qr-image').attr('src', response.qr_image);
                    $('#qr-title').text('Thanh toán bằng ' + method);
                    $('#qr-section').show();
                } else {
                    $('#qr-image').attr('src', '');
                    $('#qr-title').text('Không tạo được mã QR');
                    alert(response.message || 'Không tạo được mã QR');
                }
            },
            error: function() {
                alert('Lỗi khi gọi API sinh mã QR!');
            }
        });
    }
    $(document).ready(function() {
        loadCart();
        $('#pay-momo').click(function() {
            selectedMethod = 'Momo';
            getQrDynamic('Momo');
        });
        $('#pay-vnpay').click(function() {
            selectedMethod = 'VNPAY';
            getQrDynamic('VNPAY');
        });
        $('#confirm-payment').click(function() {
            if (!selectedMethod) {
                alert('Vui lòng chọn phương thức thanh toán!');
                return;
            }
            // Gọi API lưu thông tin thanh toán
            $.ajax({
                url: '../../api/checkout.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    items: cartData,
                    tongtien: totalAmount,
                    phuongthuc: selectedMethod
                }),
                success: function(response) {
                    if (response.success) {
                        if (response.qr_image) {
                            $('#qr-image').attr('src', response.qr_image);
                            $('#qr-title').text('Mã QR thanh toán của bạn');
                            $('#qr-section').show();
                        }
                        alert('Cảm ơn bạn đã thanh toán! Vé sẽ được gửi về tài khoản của bạn.');
                        window.location.href = '../../index.php';
                    } else {
                        alert(response.message || 'Có lỗi xảy ra khi lưu thanh toán.');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi lưu thanh toán.');
                }
            });
        });
    });
    </script>
</body>
</html> 