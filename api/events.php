<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/config.php';

$conn = getDBConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy danh sách sự kiện
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param("i", $id);
        } else {
            $stmt = $conn->prepare("SELECT * FROM events ORDER BY start_date DESC");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $events]);
        break;
        
    case 'POST':
        // Tạo sự kiện mới
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $conn->prepare("INSERT INTO events (title, description, start_date, end_date, location, max_attendees) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", 
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['location'],
            $data['max_attendees']
        );
        
        if ($stmt->execute()) {
            $eventId = $conn->insert_id;
            echo json_encode(['success' => true, 'message' => 'Event created successfully', 'id' => $eventId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create event']);
        }
        break;
        
    case 'PUT':
        // Cập nhật sự kiện
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'];
        
        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, start_date = ?, end_date = ?, location = ?, max_attendees = ? WHERE id = ?");
        $stmt->bind_param("sssssii",
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['location'],
            $data['max_attendees'],
            $id
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update event']);
        }
        break;
        
    case 'DELETE':
        // Xóa sự kiện
        $id = $_GET['id'];
        
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        break;
}

$conn->close();
?> 