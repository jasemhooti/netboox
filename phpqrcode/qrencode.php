<?php
require_once 'qrbitstream.php';
require_once 'qrcanvas.php';

class QRencode {
    public $version;
    public $level;
    public $data;
    public $canvas;
    
    public function __construct($data, $version = 1, $level = 'L') {
        $this->data = $data;
        $this->version = $version;
        $this->level = $level;
        
        // محاسبه اندازه کد QR
        $size = ($version - 1) * 4 + 21;
        $this->canvas = new QRcanvas($size, $size);
    }
    
    public function encode() {
        // ایجاد بیت‌استریم
        $stream = new QRbitstream();
        $stream->appendBytes(strlen($this->data), array_values(unpack('C*', $this->data)));
        
        // محاسبه کد تصحیح خطا
        $ec = $this->calculateErrorCorrection($stream);
        $stream->append($ec);
        
        // رسم کد QR
        $this->drawQRCode($stream);
        
        return true;
    }
    
    private function calculateErrorCorrection($stream) {
        // این بخش باید با توجه به نسخه و سطح تصحیح خطا پیاده‌سازی شود
        // برای سادگی، یک کد تصحیح خطای ساده را برمی‌گردانیم
        $ec = new QRbitstream();
        $ec->allocate(8);
        return $ec;
    }
    
    private function drawQRCode($stream) {
        $size = $this->canvas->width;
        $data = $stream->data;
        
        // رنگ‌های مورد نیاز
        $black = imagecolorallocate($this->canvas->image, 0, 0, 0);
        $white = imagecolorallocate($this->canvas->image, 255, 255, 255);
        
        // رسم الگوی موقعیت
        $this->drawPositionPattern(0, 0, $black);
        $this->drawPositionPattern($size - 7, 0, $black);
        $this->drawPositionPattern(0, $size - 7, $black);
        
        // رسم داده‌ها
        $p = 0;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($p < count($data)) {
                    $this->canvas->setPixel($x, $y, $data[$p] ? $black : $white);
                    $p++;
                }
            }
        }
    }
    
    private function drawPositionPattern($x, $y, $color) {
        // رسم الگوی موقعیت 7x7
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if (($i == 0 || $i == 6 || $j == 0 || $j == 6) ||
                    ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4)) {
                    $this->canvas->setPixel($x + $i, $y + $j, $color);
                }
            }
        }
    }
    
    public function output($type = 'png') {
        return $this->canvas->output($type);
    }
    
    public function save($filename, $type = 'png') {
        return $this->canvas->save($filename, $type);
    }
} 