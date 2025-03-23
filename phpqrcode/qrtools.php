<?php
class QRtools {
    public static function binarize($frame) {
        if (!is_array($frame)) {
            return array();
        }
        
        $binarized = array();
        foreach ($frame as $row) {
            $binarized[] = str_replace(array('0', '1'), array(' ', '1'), $row);
        }
        
        return $binarized;
    }
    
    public static function tcpdfBarcodeArray($code, $mode = 'QR,L', $tcPdfVersion = '4') {
        $barcode_array = array();
        
        if (!is_array($mode)) {
            $mode = explode(',', $mode);
        }
        
        $ecc = 'L';
        $version = '1';
        
        if (count($mode) > 1) {
            $ecc = $mode[1];
        }
        
        if (count($mode) > 2) {
            $version = $mode[2];
        }
        
        $barcode_array['code'] = $code;
        $barcode_array['mode'] = $mode;
        $barcode_array['ecc'] = $ecc;
        $barcode_array['version'] = $version;
        
        $barcode_array['barcode'] = self::getBarcodeArray($code, $ecc, $version);
        
        return $barcode_array;
    }
    
    public static function getBarcodeArray($code, $ecc = 'L', $version = '1') {
        $input = new QRinput($version, $ecc);
        $input->append($code);
        
        $frame = QRencode::encode($input);
        $frame = self::binarize($frame);
        
        return $frame;
    }
    
    public static function markTime($marker) {
        list($usec, $sec) = explode(' ', microtime());
        $time = ((float)$usec + (float)$sec);
        
        if (!isset($GLOBALS['qr_time_elapsed'])) {
            $GLOBALS['qr_time_elapsed'] = array();
        }
        
        if (!isset($GLOBALS['qr_time_elapsed'][$marker])) {
            $GLOBALS['qr_time_elapsed'][$marker] = array();
        }
        
        $GLOBALS['qr_time_elapsed'][$marker][] = $time;
    }
    
    public static function timeElapsed($marker = null) {
        if ($marker === null) {
            $marker = array_key_last($GLOBALS['qr_time_elapsed']);
        }
        
        if (!isset($GLOBALS['qr_time_elapsed'][$marker])) {
            return 0;
        }
        
        $times = $GLOBALS['qr_time_elapsed'][$marker];
        if (count($times) < 2) {
            return 0;
        }
        
        return end($times) - reset($times);
    }
    
    public static function log($outfile, $err) {
        if ($outfile !== false) {
            if ($fp = fopen($outfile, 'a')) {
                fwrite($fp, $err);
                fclose($fp);
            }
        }
    }
    
    public static function dump($data) {
        if (is_array($data)) {
            $data = print_r($data, true);
        }
        
        echo '<pre>';
        echo htmlspecialchars($data);
        echo '</pre>';
    }
    
    public static function getQRCodeImage($code, $size = 10, $level = 'L', $margin = 4) {
        $input = new QRinput($size, $level);
        $input->append($code);
        
        $frame = QRencode::encode($input);
        $frame = self::binarize($frame);
        
        $image = QRimage::image($frame, 4, $margin);
        
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
} 