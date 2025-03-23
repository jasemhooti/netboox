<?php
require_once 'qrcanvas.php';
require_once 'qrencode.php';

class QRimage {
    public static function image($frame, $pixelPerPoint = 4, $outerFrame = 4) {
        $h = count($frame);
        $w = strlen($frame[0]);
        
        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;
        
        $base_image = imagecreatetruecolor($imgW, $imgH);
        
        $col[1] = imagecolorallocate($base_image, 0, 0, 0);
        $col[0] = imagecolorallocate($base_image, 255, 255, 255);
        
        imagefill($base_image, 0, 0, $col[0]);
        
        $y = 0;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    imagefilledrectangle($base_image, $x + $outerFrame, $y + $outerFrame, $x + $outerFrame, $y + $outerFrame, $col[1]);
                }
            }
        }
        
        $target_image = imagecreatetruecolor($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
        imagecopyresized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
        imagedestroy($base_image);
        
        return $target_image;
    }
    
    public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4) {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        
        if ($filename === false) {
            header('Content-Type: image/png');
            imagepng($image);
        } else {
            imagepng($image, $filename);
        }
        
        imagedestroy($image);
    }
    
    public static function jpeg($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4) {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        
        if ($filename === false) {
            header('Content-Type: image/jpeg');
            imagejpeg($image);
        } else {
            imagejpeg($image, $filename);
        }
        
        imagedestroy($image);
    }
    
    public static function gif($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4) {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        
        if ($filename === false) {
            header('Content-Type: image/gif');
            imagegif($image);
        } else {
            imagegif($image, $filename);
        }
        
        imagedestroy($image);
    }
} 