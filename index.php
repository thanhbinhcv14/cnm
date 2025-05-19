<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Lấy danh sách sự kiện nổi bật từ API
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/SuKien/cnm/api/events.php';
$response = file_get_contents($apiUrl);
$events = [];
$newEvents = [];
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
        $events = $data['data'];
        // Lọc sự kiện mới trong 7 ngày gần nhất dựa vào NgayTao
        $now = new DateTime();
        $sevenDaysAgo = (clone $now)->modify('-7 days');
        foreach ($events as $event) {
            if (isset($event['NgayTao'])) {
                $created = new DateTime($event['NgayTao']);
                if ($created >= $sevenDaysAgo && $created <= $now) {
                    $newEvents[] = $event;
                }
            }
        }
        // Nếu không có sự kiện nào trong 7 ngày gần nhất, hiển thị tất cả sự kiện
        if (empty($newEvents)) {
            $newEvents = $events;
        }
    }
}
function getEventImagePath($filename) {
    if (!$filename) return 'Hinh/logo/logo.png';
    $posterPath = 'Hinh/poster/' . $filename;
    $mainPath = 'Hinh/avatar/' . $filename;
    if (file_exists($posterPath)) return $posterPath;
    if (file_exists($mainPath)) return $mainPath;
    return 'Hinh/logo/logo.png';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="Hinh/logo/logo.png">
    <title>Trang chủ - Hệ thống sự kiện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/views/partials/navbar.php'; ?>

    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('Hinh/banner/banner1.jpg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Chào mừng đến với trang chủ</h1>
                    <p class="carousel-subtitle">Khám phá những sự kiện thú vị và ý nghĩa</p>
                    <a href="views/product/allproduct.php" class="btn btn-hero">Xem sự kiện</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner2.jpeg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Sự kiện đặc biệt</h1>
                    <p class="carousel-subtitle">Tham gia các hoạt động thú vị cùng chúng tôi</p>
                    <a href="views/product/allproduct.php" class="btn btn-hero">Khám phá ngay</a>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('Hinh/banner/banner3.jpg');">
                <div class="carousel-caption">
                    <h1 class="carousel-title">Kết nối cộng đồng</h1>
                    <p class="carousel-subtitle">Cùng nhau tạo nên những khoảnh khắc đáng nhớ</p>
                    <a href="views/product/allproduct.php" class="btn btn-hero">Tham gia ngay</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- New Events Section -->
    <section class="container mt-5">
        <h2 class="section-title" style="color: #ff5722;">SỰ KIỆN MỚI</h2>
        <div class="row">
            <?php if (!empty($newEvents)): ?>
                <?php foreach (array_slice($newEvents, 0, 4) as $event): ?>
                    <div class="col-md-3">
                        <div class="event-card">
                            <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" class="event-image w-100">
                            <div class="p-3">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                                <p class="event-info">
                                    <i class="fas fa-tag"></i> 
                                    <span class="event-category"><?php echo htmlspecialchars($event['TheLoai'] ?? 'Chưa phân loại'); ?></span>
                                </p>
                                <p class="event-info">
                                    <i class="fas fa-calendar"></i> 
                                    <span class="event-date"><?php echo date('d/m/Y H:i', strtotime($event['ThoiGianBatDau'])); ?></span>
                                </p>
                                <p class="event-info">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($event['DiaDiem']); ?>
                                </p>
                                <a href="/SuKien/cnm/views/product/productdetails.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-secondary">
                        Không có sự kiện mới trong 7 ngày gần đây.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Events Section -->
    <section class="container">
        <h2 class="section-title">SỰ KIỆN NỔI BẬT</h2>
        <div class="row">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                <div class="col-md-3">
                    <div class="event-card">
                        <img src="<?php echo htmlspecialchars(getEventImagePath($event['HinhAnh'])); ?>" alt="<?php echo htmlspecialchars($event['TenSuKien']); ?>" class="event-image w-100">
                        <div class="p-3">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['TenSuKien']); ?></h3>
                            <p class="event-info">
                                <i class="fas fa-tag"></i> 
                                <span class="event-category"><?php echo htmlspecialchars($event['TheLoai'] ?? 'Chưa phân loại'); ?></span>
                            </p>
                            <p class="event-info">
                                <i class="fas fa-calendar"></i> 
                                <span class="event-date"><?php echo date('d/m/Y H:i', strtotime($event['ThoiGianBatDau'])); ?></span>
                            </p>
                            <p class="event-info">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?php echo htmlspecialchars($event['DiaDiem']); ?>
                            </p>
                            <a href="/SuKien/cnm/views/product/productdetails.php?id=<?php echo $event['ID_SuKien']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        Hiện tại chưa có sự kiện nào được tổ chức. Vui lòng quay lại sau!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Chatbot Icon -->
    <div class="chatbot-icon" id="chatbot-icon">
        <img src="Hinh/chatbox/chatbox.jpg" alt="Chatbot">
    </div>

    <!-- Chatbot Modal -->
    <div class="chatbot-modal" id="chatbot-modal">
        <div class="chatbot-modal-header">
            <h3>🤖 Chatbot Hỗ Trợ</h3>
            <button class="close-btn" id="close-chatbot">&times;</button>
        </div>
        <div class="chatbot-modal-body" id="chat-box">
            <p><strong>Bot:</strong> Xin chào! Tôi có thể giúp gì cho bạn?</p>
        </div>
        <div class="chatbot-modal-footer">
            <?php if (isset($_SESSION["user_id"])): ?>
                <form id="chat-form">
                    <input type="text" id="chat-input" name="message" placeholder="Nhập câu hỏi...">
                    <button type="submit">Gửi</button>
                </form>
            <?php else: ?>
                <p class="text-center">Vui lòng <a href="views/auth/login.php">đăng nhập</a> để chat</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .chatbot-icon {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        cursor: pointer;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .chatbot-icon:hover {
        transform: scale(1.1);
    }

    .chatbot-icon img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .chatbot-modal {
        display: none;
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 350px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        overflow: hidden;
    }

    .chatbot-modal-header {
        background: #00bfa5;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
    }

    .chatbot-modal-body {
        padding: 15px;
        height: 300px;
        overflow-y: auto;
        background: #f5f5f5;
    }

    .chatbot-modal-footer {
        padding: 15px;
        border-top: 1px solid #eee;
    }

    #chat-form {
        display: flex;
        gap: 10px;
    }

    #chat-input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
    }

    #chat-form button {
        background: #00bfa5;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 20px;
        cursor: pointer;
    }

    #chat-form button:hover {
        background: #00a896;
    }

    .message {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 15px;
        max-width: 80%;
    }

    .user-message {
        background: #e3f2fd;
        margin-left: auto;
    }

    .bot-message {
        background: white;
    }

    .message-time {
        font-size: 0.7rem;
        color: #666;
        margin-top: 4px;
    }
    </style>

    <?php include_once __DIR__ . '/views/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotIcon = document.getElementById('chatbot-icon');
            const chatbotModal = document.getElementById('chatbot-modal');
            const closeChatbot = document.getElementById('close-chatbot');
            const chatBox = document.getElementById('chat-box');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');

            // Hiển thị/ẩn chatbot khi click vào icon
            chatbotIcon.addEventListener('click', function() {
                chatbotModal.style.display = chatbotModal.style.display === 'none' || chatbotModal.style.display === '' ? 'block' : 'none';
                if (chatbotModal.style.display === 'block') {
                    loadChatHistory();
                    chatInput?.focus();
                }
            });

            // Đóng chatbot
            closeChatbot.addEventListener('click', function() {
                chatbotModal.style.display = 'none';
            });

            // Load lịch sử chat
            function loadChatHistory() {
                if (!<?php echo isset($_SESSION["user_id"]) ? "true" : "false"; ?>) return;
                
                fetch('api/chatbox.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chatBox.innerHTML = '';
                            data.data.forEach(message => {
                                appendMessage('user', message.user, message.time);
                                appendMessage('bot', message.bot, message.time);
                            });
                            scrollToBottom();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Thêm tin nhắn vào chatbox
            function appendMessage(type, text, time) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}-message`;
                
                const messageContent = document.createElement('div');
                messageContent.textContent = text;
                
                const timeDiv = document.createElement('div');
                timeDiv.className = 'message-time';
                timeDiv.textContent = new Date(time).toLocaleString();
                
                messageDiv.appendChild(messageContent);
                messageDiv.appendChild(timeDiv);
                chatBox.appendChild(messageDiv);
                
                scrollToBottom();
            }

            // Cuộn xuống cuối chatbox
            function scrollToBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // Xử lý gửi tin nhắn
            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = chatInput.value.trim();
                    
                    if (!message) return;
                    
                    // Hiển thị tin nhắn người dùng
                    appendMessage('user', message, new Date());
                    
                    // Gửi tin nhắn đến server
                    fetch('api/chatbox.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message: message })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hiển thị phản hồi từ bot
                            appendMessage('bot', data.data.answer, new Date());
                        }
                    })
                    .catch(error => console.error('Error:', error));
                    
                    chatInput.value = '';
                });

                // Gửi tin nhắn khi nhấn Enter
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        chatForm.dispatchEvent(new Event('submit'));
                    }
                });
            }
        });
    </script>
</body>
</html>
