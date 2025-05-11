<?php
require_once '../config/config.php';
require_once '../includes/phpqrcode/qrlib.php';

class QRGenerator {
    public static function generateQRCode($data, $filename = null) {
        if ($filename === null) {
            $filename = 'temp_' . time() . '.png';
        }
        
        $path = UPLOAD_DIR . 'qr_codes/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $filepath = $path . $filename;
        
        QRcode::png($data, $filepath, QR_CODE_LEVEL, QR_CODE_SIZE, QR_CODE_MARGIN);
        
        return $filepath;
    }
    
    public static function generateEventQRCode($eventId) {
        $data = BASE_URL . '/event.php?id=' . $eventId;
        $filename = 'event_' . $eventId . '.png';
        return self::generateQRCode($data, $filename);
    }
    
}
?> 