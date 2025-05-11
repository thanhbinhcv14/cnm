<?php
require_once '../../config/config.php';
require_once '../../includes/csrf.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../views/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../Hinh/logo/logo.png">
    <title>Quản lý nhân viên - Hệ thống sự kiện</title>
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

        /* Staff page specific styles */
        .staff-container {
            padding: 2rem 0;
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .staff-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            padding: 1rem;
        }

        .btn-add-staff {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .btn-add-staff:hover {
            background-color: #0c7c45;
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container staff-container">
        <h1 class="page-title text-center">Quản lý tài khoản</h1>
        
        <!-- Add Staff Button -->
        <div class="text-end mb-4">
            <button type="button" class="btn btn-add-staff" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fas fa-plus"></i> Thêm tài khoản mới
            </button>
        </div>

        <!-- Search box -->
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tài khoản...">
            </div>
        </div>

        <!-- Staff List -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="staffTable">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">STT</th>
                                <th scope="col">Họ và tên</th>
                                <th scope="col">Tên đăng nhập</th>
                                <th scope="col">Email</th>
                                <th scope="col">Số điện thoại</th>
                                <th scope="col">Vai trò</th>
                                <th scope="col" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="staffList">
                            <!-- Staff data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm tài khoản mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <?php echo CSRF::getTokenInput(); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div id="usernameError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                            <div id="fullnameError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div id="emailError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                            <div id="phoneError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" selected disabled>-- Chọn vai trò --</option>
                                <option value="2">Khách hàng</option>
                                <option value="3">Đơn vị tổ chức</option>
                                <option value="4">Nhân viên</option>
                            </select>
                            <div id="roleError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="passwordError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddStaffForm()">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm">
                        <?php echo CSRF::getTokenInput(); ?>
                        <input type="hidden" id="edit_user_id" name="edit_user_id">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div id="usernameError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                            <div id="fullnameError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div id="emailError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                            <div id="phoneError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" selected disabled>-- Chọn vai trò --</option>
                                <option value="2">Khách hàng</option>
                                <option value="3">Đơn vị tổ chức</option>
                                <option value="4">Nhân viên</option>
                            </select>
                            <div id="roleError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div id="passwordError" class="text-danger"></div> <!-- Phần hiển thị lỗi -->
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditStaffForm()">Sửa</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadStaffList();
            
            // Xử lý sự kiện khi người dùng nhập dữ liệu trong form thêm mới
            $('#addStaffForm #username, #editStaffForm #username').on('input', function() {
                validateField('username', $(this).val(), $(this).closest('form').attr('id'));
            });
            
            $('#addStaffForm #fullname, #editStaffForm #fullname').on('input', function() {
                validateField('fullname', $(this).val(), $(this).closest('form').attr('id'));
            });
            
            $('#addStaffForm #email, #editStaffForm #email').on('input', function() {
                validateField('email', $(this).val(), $(this).closest('form').attr('id'));
            });
            
            $('#addStaffForm #phone, #editStaffForm #phone').on('input', function() {
                validateField('phone', $(this).val(), $(this).closest('form').attr('id'));
            });
            
            $('#addStaffForm #password, #editStaffForm #password').on('input', function() {
                validateField('password', $(this).val(), $(this).closest('form').attr('id'));
            });
            
            $('#addStaffForm #role, #editStaffForm #role').on('change', function() {
                validateField('role', $(this).val(), $(this).closest('form').attr('id'));
            });
        });

        // Hàm kiểm tra từng trường riêng lẻ
        function validateField(fieldName, value, formId) {
            const errorElement = $(`#${formId} #${fieldName}Error`);
            
            switch(fieldName) {
                case 'username':
                    if (!/^[a-zA-Z0-9_]{4,}$/.test(value)) {
                        errorElement.text('Tên đăng nhập phải từ 4 ký tự trở lên và không chứa ký tự đặc biệt!');
                    } else {
                        errorElement.text('');
                    }
                    break;
                    
                case 'fullname':
                    if (!value || value.trim().length < 2) {
                        errorElement.text('Họ và tên phải từ 2 ký tự trở lên!');
                    } else {
                        errorElement.text('');
                    }
                    break;
                    
                case 'email':
                    if (!/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/.test(value)) {
                        errorElement.text('Email không hợp lệ!');
                    } else {
                        errorElement.text('');
                    }
                    break;
                    
                case 'phone':
                    if (!/^0\d{9,10}$/.test(value)) {
                        errorElement.text('Số điện thoại không hợp lệ!');
                    } else {
                        errorElement.text('');
                    }
                    break;
                    
                case 'password':
                    // Nếu là form sửa và trường mật khẩu trống thì không báo lỗi
                    if (formId === 'editStaffForm' && (!value || value.trim() === '')) {
                        errorElement.text('');
                    } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(value)) {
                        errorElement.text('Mật khẩu phải từ 6 ký tự, có chữ hoa, chữ thường và số!');
                    } else {
                        errorElement.text('');
                    }
                    break;
                    
                case 'role':
                    if (!value || value === "") {
                        errorElement.text('Vui lòng chọn vai trò!');
                    } else {
                        errorElement.text('');
                    }
                    break;
            }
        }

        function loadStaffList() {
            $.ajax({
                url: '../../api/staff.php',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        allStaffData = response.data;
                        displayStaffList(allStaffData);
                    } else {
                        alert('Lỗi khi tải danh sách nhân viên: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi kết nối với server');
                }
            });
        }

        function displayStaffList(staffList) {
            const staffListDiv = $('#staffList');
            staffListDiv.empty();
            staffList.forEach((staff, idx) => {
                const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${staff.HoTen}</td>
                        <td>${staff.TenDangNhap}</td>
                        <td>${staff.Email}</td>
                        <td>${staff.SoDienThoai}</td>
                        <td>${staff.TenRole}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary me-1" onclick="editStaff(${staff.ID_User})">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteStaff(${staff.ID_User})">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </td>
                    </tr>
                `;
                staffListDiv.append(row);
            });
        }

        function validateStaffForm(formData) {
            let isValid = true;

            // Reset lỗi cũ
            $('.text-danger').empty();

            // Kiểm tra tên đăng nhập
            if (!/^[a-zA-Z0-9_]{4,}$/.test(formData.username)) {
                $('#usernameError').text('Tên đăng nhập phải từ 4 ký tự trở lên và không chứa ký tự đặc biệt!');
                isValid = false;
            }

            // Kiểm tra họ tên
            if (!formData.fullname || formData.fullname.trim().length < 2) {
                $('#fullnameError').text('Họ và tên phải từ 2 ký tự trở lên!');
                isValid = false;
            }

            // Kiểm tra email
            if (!/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/.test(formData.email)) {
                $('#emailError').text('Email không hợp lệ!');
                isValid = false;
            }

            // Kiểm tra số điện thoại
            if (!/^0\d{9,10}$/.test(formData.phone)) {
                $('#phoneError').text('Số điện thoại không hợp lệ!');
                isValid = false;
            }

            // Kiểm tra mật khẩu
            if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(formData.password)) {
                $('#passwordError').text('Mật khẩu phải từ 6 ký tự, có chữ hoa, chữ thường và số!');
                isValid = false;
            }

            // Kiểm tra vai trò
            if (!formData.role_id || formData.role_id === "") {
                $('#roleError').text('Vui lòng chọn vai trò!');
                isValid = false;
            }

            return isValid;
        }

        function submitAddStaffForm() {
            const formData = {
                username: $('#username').val(),
                fullname: $('#fullname').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                role_id: $('#role').val(),
                password: $('#password').val(),
                csrf_token: $('input[name="csrf_token"]').val()
            };

            if (!validateStaffForm(formData)) return;

            $.ajax({
                url: '../../api/staff.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        alert('Thêm tài khoản thành công!');
                        $('#addStaffModal').modal('hide');
                        $('#addStaffForm')[0].reset();
                        $('.text-danger').empty(); // Xóa tất cả thông báo lỗi
                        loadStaffList();
                    } else {
                        // Hiển thị lỗi từ server nếu có
                        if (response.errors) {
                            // Nếu server trả về lỗi cụ thể cho từng trường
                            for (const field in response.errors) {
                                $(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            // Nếu server chỉ trả về thông báo lỗi chung
                            alert('Lỗi: ' + response.message);
                        }
                    }
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            for (const field in response.errors) {
                                $(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            alert('Có lỗi xảy ra khi kết nối với server');
                        }
                    } catch (e) {
                        alert('Có lỗi xảy ra khi kết nối với server');
                    }
                }
            });
        }

        function editStaff(userId) {
            // Tìm thông tin người dùng từ dữ liệu đã tải
            const user = allStaffData.find(staff => staff.ID_User === userId);
            if (!user) {
                alert('Không tìm thấy thông tin người dùng!');
                return;
            }

            // Điền thông tin vào form sửa
            const editForm = $('#editStaffForm');
            editForm.find('#edit_user_id').val(user.ID_User);
            editForm.find('#username').val(user.TenDangNhap);
            editForm.find('#fullname').val(user.HoTen);
            editForm.find('#email').val(user.Email);
            editForm.find('#phone').val(user.SoDienThoai);
            editForm.find('#role').val(user.Role_ID);
            editForm.find('#password').val(''); // Để trống mật khẩu, người dùng sẽ nhập nếu muốn đổi

            // Reset các thông báo lỗi
            editForm.find('.text-danger').empty();

            // Hiển thị modal
            $('#editStaffModal').modal('show');
        }

        function submitEditStaffForm() {
            const editForm = $('#editStaffForm');
            const formData = {
                user_id: editForm.find('#edit_user_id').val(),
                username: editForm.find('#username').val(),
                fullname: editForm.find('#fullname').val(),
                email: editForm.find('#email').val(),
                phone: editForm.find('#phone').val(),
                role_id: editForm.find('#role').val(),
                password: editForm.find('#password').val(), // Nếu để trống, API sẽ không thay đổi mật khẩu
                csrf_token: editForm.find('input[name="csrf_token"]').val()
            };

            // Kiểm tra form hợp lệ
            if (!validateEditStaffForm(formData)) return;

            $.ajax({
                url: '../../api/staff.php',
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        alert('Cập nhật tài khoản thành công!');
                        $('#editStaffModal').modal('hide');
                        loadStaffList();
                    } else {
                        // Hiển thị lỗi từ server nếu có
                        if (response.errors) {
                            // Nếu server trả về lỗi cụ thể cho từng trường
                            for (const field in response.errors) {
                                editForm.find(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            // Nếu server chỉ trả về thông báo lỗi chung
                            alert('Lỗi: ' + response.message);
                        }
                    }
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            for (const field in response.errors) {
                                editForm.find(`#${field}Error`).text(response.errors[field]);
                            }
                        } else {
                            alert('Có lỗi xảy ra khi kết nối với server');
                        }
                    } catch (e) {
                        alert('Có lỗi xảy ra khi kết nối với server');
                    }
                }
            });
        }

        function validateEditStaffForm(formData) {
            let isValid = true;
            const editForm = $('#editStaffForm');
            
            // Reset lỗi cũ
            editForm.find('.text-danger').empty();

            // Kiểm tra tên đăng nhập
            if (!/^[a-zA-Z0-9_]{4,}$/.test(formData.username)) {
                editForm.find('#usernameError').text('Tên đăng nhập phải từ 4 ký tự trở lên và không chứa ký tự đặc biệt!');
                isValid = false;
            }

            // Kiểm tra họ tên
            if (!formData.fullname || formData.fullname.trim().length < 2) {
                editForm.find('#fullnameError').text('Họ và tên phải từ 2 ký tự trở lên!');
                isValid = false;
            }

            // Kiểm tra email
            if (!/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/.test(formData.email)) {
                editForm.find('#emailError').text('Email không hợp lệ!');
                isValid = false;
            }

            // Kiểm tra số điện thoại
            if (!/^0\d{9,10}$/.test(formData.phone)) {
                editForm.find('#phoneError').text('Số điện thoại không hợp lệ!');
                isValid = false;
            }

            // Kiểm tra mật khẩu - chỉ kiểm tra nếu người dùng nhập mật khẩu mới
            if (formData.password && formData.password.trim() !== '') {
                if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(formData.password)) {
                    editForm.find('#passwordError').text('Mật khẩu phải từ 6 ký tự, có chữ hoa, chữ thường và số!');
                    isValid = false;
                }
            }

            // Kiểm tra vai trò
            if (!formData.role_id || formData.role_id === "") {
                editForm.find('#roleError').text('Vui lòng chọn vai trò!');
                isValid = false;
            }

            return isValid;
        }

        function deleteStaff(userId) {
            if (confirm('Bạn có chắc chắn muốn xóa tài khoản này?')) {
                $.ajax({
                    url: '../../api/staff.php',
                    type: 'DELETE',
                    data: JSON.stringify({
                        user_id: userId,
                        csrf_token: $('input[name="csrf_token"]').val()
                    }),
                    success: function(response) {
                        if (response.success) {
                            alert('Xóa tài khoản thành công!');
                            loadStaffList();
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi kết nối với server');
                    }
                });
            }
        }

        // --- Tìm kiếm realtime ---
        let allStaffData = [];
        $(document).on('input', '#searchInput', function() {
            const keyword = $(this).val().toLowerCase();
            const filtered = allStaffData.filter(staff =>
                staff.HoTen.toLowerCase().includes(keyword) ||
                staff.TenDangNhap.toLowerCase().includes(keyword) ||
                staff.Email.toLowerCase().includes(keyword) ||
                staff.SoDienThoai.toLowerCase().includes(keyword) ||
                staff.TenRole.toLowerCase().includes(keyword)
            );
            displayStaffList(filtered);
        });
    </script>
</body>
</html>
