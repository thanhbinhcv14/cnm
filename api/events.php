<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../config/database.php';
require_once '../config/config.php';

$conn = getDBConnection();

function addTotalTickets(&$events, $conn) {
    foreach ($events as &$event) {
        $stmt2 = $conn->prepare("SELECT SUM(SoLuong) as total FROM ve WHERE ID_SuKien = ?");
        $stmt2->bind_param("i", $event['ID_SuKien']);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $row2 = $res2->fetch_assoc();
        $event['TongSoVe'] = (int)($row2['total'] ?? 0);
        $stmt2->close();
    }
    unset($event);
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        if ($search !== '') {
            $searchParam = '%' . $conn->real_escape_string($search) . '%';
            $query = "SELECT s.ID_SuKien, s.TenSuKien, s.HinhAnh, s.TheLoai, s.ThoiGianBatDau, s.ThoiGianKetThuc, d.TenDiaDiem as DiaDiem, u.HoTen as organizer_name
                      FROM sukien s
                      JOIN user u ON s.ID_User = u.ID_User
                      JOIN diadiem d ON s.ID_DiaDiem = d.ID_DiaDiem
                      WHERE (s.TenSuKien LIKE ? OR d.TenDiaDiem LIKE ?)" . ($user_id > 0 ? " AND s.ID_User = ?" : "") . "
                      ORDER BY s.ThoiGianBatDau DESC";
            if ($user_id > 0) {
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssi", $searchParam, $searchParam, $user_id);
            } else {
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $searchParam, $searchParam);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $events = $result->fetch_all(MYSQLI_ASSOC);
            addTotalTickets($events, $conn);
            echo json_encode(['success' => true, 'data' => $events]);
            break;
        } else if ($user_id > 0) {
            $query = "SELECT s.ID_SuKien, s.TenSuKien, s.HinhAnh, s.TheLoai, s.ThoiGianBatDau, s.ThoiGianKetThuc, d.TenDiaDiem as DiaDiem, u.HoTen as organizer_name
                      FROM sukien s
                      JOIN user u ON s.ID_User = u.ID_User
                      JOIN diadiem d ON s.ID_DiaDiem = d.ID_DiaDiem
                      WHERE s.ID_User = ?
                      ORDER BY s.ThoiGianBatDau DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $events = $result->fetch_all(MYSQLI_ASSOC);
            addTotalTickets($events, $conn);
            echo json_encode(['success' => true, 'data' => $events]);
            break;
        }
        if (isset($_GET['id'])) {
            // Lấy thông tin chi tiết một sự kiện
            $event_id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT s.*, d.TenDiaDiem, u.HoTen as organizer_name, s.qrcode
                                  FROM sukien s
                                  JOIN user u ON s.ID_User = u.ID_User
                                  JOIN diadiem d ON s.ID_DiaDiem = d.ID_DiaDiem
                                  WHERE s.ID_SuKien = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            
            if ($event) {
                echo json_encode(['success' => true, 'data' => $event]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sự kiện']);
            }
        } else {
            // Lấy danh sách tất cả sự kiện
            $query = "SELECT s.ID_SuKien, s.TenSuKien, s.HinhAnh, s.TheLoai, s.ThoiGianBatDau, s.ThoiGianKetThuc, d.TenDiaDiem as DiaDiem, u.HoTen as organizer_name,s.qrcode
                      FROM sukien s
                      JOIN user u ON s.ID_User = u.ID_User
                      JOIN diadiem d ON s.ID_DiaDiem = d.ID_DiaDiem
                      ORDER BY s.ThoiGianBatDau DESC";
            $result = $conn->query($query);
            if ($result) {
                $events = $result->fetch_all(MYSQLI_ASSOC);
                addTotalTickets($events, $conn);
                echo json_encode(['success' => true, 'data' => $events]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
            }
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("INSERT INTO sukien (TenSuKien, HinhAnh, TheLoai, ThoiGianBatDau, ThoiGianKetThuc, ID_DiaDiem, ID_User) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii",
            $data['TenSuKien'],
            $data['HinhAnh'],
            $data['TheLoai'],
            $data['ThoiGianBatDau'],
            $data['ThoiGianKetThuc'],
            $data['ID_DiaDiem'],
            $data['ID_User']
        );
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            // Sinh QR code cho sự kiện mới
            require_once '../qr/qr_generator.php';
            $qrData = "http://localhost/SuKien/cnm/index.php?id=" . $newId;
            $qrFilename = "sukien_" . $newId . ".png";
            $qrPath = QRGenerator::generateQRCode($qrData, $qrFilename);
            $qrRelativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $qrPath);
            $conn->query("UPDATE sukien SET qrcode='$qrRelativePath' WHERE ID_SuKien=$newId");
            echo json_encode(['success' => true, 'message' => 'Thêm sự kiện thành công', 'id' => $newId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thêm sự kiện thất bại']);
        }
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $put_vars);
        $id = $put_vars['ID_SuKien'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID_SuKien']);
            break;
        }
        $stmt = $conn->prepare("UPDATE sukien SET TenSuKien=?, HinhAnh=?, TheLoai=?, ThoiGianBatDau=?, ThoiGianKetThuc=?, ID_DiaDiem=?, ID_User=? WHERE ID_SuKien=?");
        $stmt->bind_param("ssssiii",
            $put_vars['TenSuKien'],
            $put_vars['HinhAnh'],
            $put_vars['TheLoai'],
            $put_vars['ThoiGianBatDau'],
            $put_vars['ThoiGianKetThuc'],
            $put_vars['ID_DiaDiem'],
            $put_vars['ID_User'],
            $id
        );
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật sự kiện thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật sự kiện thất bại']);
        }
        break;
    case 'DELETE':
        parse_str(file_get_contents('php://input'), $del_vars);
        $id = $del_vars['ID_SuKien'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID_SuKien']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM sukien WHERE ID_SuKien=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Xóa sự kiện thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa sự kiện thất bại']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Phương thức không hỗ trợ']);
        break;
}

$conn->close();
?> 