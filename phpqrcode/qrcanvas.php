<?php
class QRcanvas {
    public $width;
    public $height;
    public $image;
    
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        imagefill($this->image, 0, 0, imagecolorallocate($this->image, 255, 255, 255));
    }
    
    public function setPixel($x, $y, $color) {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return false;
        }
        imagesetpixel($this->image, $x, $y, $color);
        return true;
    }
    
    public function getPixel($x, $y) {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return false;
        }
        return imagecolorat($this->image, $x, $y);
    }
    
    public function output($type = 'png') {
        switch ($type) {
            case 'png':
                header('Content-Type: image/png');
                imagepng($this->image);
                break;
            case 'jpeg':
            case 'jpg':
                header('Content-Type: image/jpeg');
                imagejpeg($this->image);
                break;
            case 'gif':
                header('Content-Type: image/gif');
                imagegif($this->image);
                break;
            default:
                return false;
        }
        return true;
    }
    
    public function save($filename, $type = 'png') {
        switch ($type) {
            case 'png':
                return imagepng($this->image, $filename);
            case 'jpeg':
            case 'jpg':
                return imagejpeg($this->image, $filename);
            case 'gif':
                return imagegif($this->image, $filename);
            default:
                return false;
        }
    }
    
    public function __destruct() {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
} 