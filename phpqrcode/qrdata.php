<?php
class QRdata {
    public $data;
    public $mode;
    public $version;
    public $errorCorrectionLevel;
    
    public function __construct($data, $mode = QR_MODE_8, $version = 1, $errorCorrectionLevel = QR_ECLEVEL_L) {
        $this->data = $data;
        $this->mode = $mode;
        $this->version = $version;
        $this->errorCorrectionLevel = $errorCorrectionLevel;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getMode() {
        return $this->mode;
    }
    
    public function getVersion() {
        return $this->version;
    }
    
    public function getErrorCorrectionLevel() {
        return $this->errorCorrectionLevel;
    }
    
    public function getCapacity() {
        return QR_CAPACITY_TABLE[$this->version][$this->errorCorrectionLevel];
    }
    
    public function getLength() {
        return strlen($this->data);
    }
    
    public function isDataValid() {
        switch($this->mode) {
            case QR_MODE_NUM:
                return preg_match('/^[0-9]*$/', $this->data);
            case QR_MODE_AN:
                return preg_match('/^[A-Z0-9 $%*+\-./:]*$/', $this->data);
            case QR_MODE_8:
                return true;
            case QR_MODE_KANJI:
                return preg_match('/^[\x{4E00}-\x{9FFF}]*$/u', $this->data);
            default:
                return false;
        }
    }
    
    public function getModeIndicator() {
        switch($this->mode) {
            case QR_MODE_NUM:
                return 0x1;
            case QR_MODE_AN:
                return 0x2;
            case QR_MODE_8:
                return 0x4;
            case QR_MODE_KANJI:
                return 0x8;
            default:
                return 0x0;
        }
    }
    
    public function getCharacterCountIndicator() {
        $length = $this->getLength();
        switch($this->mode) {
            case QR_MODE_NUM:
                if($this->version < 10) return 10;
                if($this->version < 27) return 12;
                return 14;
            case QR_MODE_AN:
                if($this->version < 10) return 9;
                if($this->version < 27) return 11;
                return 13;
            case QR_MODE_8:
                if($this->version < 10) return 8;
                if($this->version < 27) return 16;
                return 16;
            case QR_MODE_KANJI:
                if($this->version < 10) return 8;
                if($this->version < 27) return 10;
                return 12;
            default:
                return 0;
        }
    }
} 