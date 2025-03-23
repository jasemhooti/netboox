<?php
class QRsplit {
    public $dataStr;
    public $input;
    public $modeHint;
    
    public function __construct($dataStr, $input, $modeHint) {
        $this->dataStr = $dataStr;
        $this->input = $input;
        $this->modeHint = $modeHint;
    }
    
    public static function isdigitat($str, $pos) {
        if ($pos >= strlen($str)) {
            return false;
        }
        return ord($str[$pos]) >= ord('0') && ord($str[$pos]) <= ord('9');
    }
    
    public static function isalnumat($str, $pos) {
        if ($pos >= strlen($str)) {
            return false;
        }
        return self::lookAnTable(ord($str[$pos])) >= 0;
    }
    
    private static function lookAnTable($c) {
        return ($c >= 0x20 && $c <= 0x7e) ? self::$anTable[$c - 0x20] : -1;
    }
    
    private static $anTable = array(
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        36, -1, -1, -1, 37, 38, -1, -1, -1, -1, 39, 40, -1, 41, 42, 43,
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 44, -1, -1, -1, -1, -1,
        -1, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
        25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, -1, -1, -1, -1, -1
    );
    
    public function splitString() {
        while (strlen($this->dataStr) > 0) {
            if ($this->dataStr == '') {
                return 0;
            }
            
            $mode = $this->identifyMode(0);
            
            switch ($mode) {
                case QR_MODE_NUM:
                    $length = $this->eatNum();
                    break;
                case QR_MODE_AN:
                    $length = $this->eatAn();
                    break;
                case QR_MODE_KANJI:
                    $length = $this->eatKanji();
                    break;
                default:
                    $length = $this->eat8();
                    break;
            }
            
            if ($length == 0) {
                return 0;
            }
            
            $this->dataStr = substr($this->dataStr, $length);
        }
        
        return 1;
    }
    
    public function eatNum() {
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        $p = 0;
        
        while (self::isdigitat($this->dataStr, $p)) {
            $p++;
        }
        
        $run = $p;
        $mode = $this->identifyMode($p);
        
        if ($mode == QR_MODE_8) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                + QRinput::estimateBitsMode8(1) - QRinput::estimateBitsMode8($run + 1);
            if ($dif > 0) {
                return $run;
            }
        }
        if ($mode == QR_MODE_AN) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln
                + QRinput::estimateBitsModeAn(1) - QRinput::estimateBitsModeAn($run + 1);
            if ($dif > 0) {
                return $run;
            }
        }
        
        $this->input->append(QR_MODE_NUM, $run, str_split($this->dataStr));
        return $run;
    }
    
    public function eatAn() {
        $la = QRspec::lengthIndicator(QR_MODE_AN, $this->input->getVersion());
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        
        $p = 0;
        while (self::isalnumat($this->dataStr, $p)) {
            if (self::isdigitat($this->dataStr, $p)) {
                $q = $p;
                while (self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }
                
                $dif = QRinput::estimateBitsModeAn($p) + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                    - QRinput::estimateBitsModeAn($q);
                    
                if ($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else {
                $p++;
            }
        }
        
        $run = $p;
        if (!self::isalnumat($this->dataStr, $p)) {
            $dif = QRinput::estimateBitsModeAn($run) + 4 + $la
                + QRinput::estimateBitsMode8(1) - QRinput::estimateBitsMode8($run + 1);
            if ($dif > 0) {
                return $run;
            }
        }
        
        $this->input->append(QR_MODE_AN, $run, str_split($this->dataStr));
        return $run;
    }
    
    public function eatKanji() {
        $p = 0;
        
        while ($this->identifyMode($p) == QR_MODE_KANJI) {
            $p += 2;
        }
        
        $run = $p;
        $this->input->append(QR_MODE_KANJI, $p, str_split($this->dataStr));
        return $run;
    }
    
    public function eat8() {
        $la = QRspec::lengthIndicator(QR_MODE_AN, $this->input->getVersion());
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        
        $p = 1;
        $dataStrLen = strlen($this->dataStr);
        
        while ($p < $dataStrLen) {
            $mode = $this->identifyMode($p);
            if ($mode == QR_MODE_KANJI) {
                break;
            }
            if ($mode == QR_MODE_NUM) {
                $q = $p;
                while (self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsMode8($p) + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln
                    - QRinput::estimateBitsMode8($q);
                if ($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else if ($mode == QR_MODE_AN) {
                $q = $p;
                while (self::isalnumat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsMode8($p) + QRinput::estimateBitsModeAn($q - $p) + 4 + $la
                    - QRinput::estimateBitsMode8($q);
                if ($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else {
                $p++;
            }
        }
        
        $run = $p;
        $this->input->append(QR_MODE_8, $p, str_split($this->dataStr));
        return $run;
    }
    
    public function identifyMode($pos) {
        if ($pos >= strlen($this->dataStr)) {
            return QR_MODE_NULL;
        }
        
        $c = $this->dataStr[$pos];
        
        if (self::isdigitat($this->dataStr, $pos)) {
            return QR_MODE_NUM;
        } else if (self::isalnumat($this->dataStr, $pos)) {
            return QR_MODE_AN;
        } else if ($this->modeHint == QR_MODE_KANJI) {
            if ($pos + 1 < strlen($this->dataStr)) {
                $d = $this->dataStr[$pos + 1];
                $word = (ord($c) << 8) | ord($d);
                if (($word >= 0x8140 && $word <= 0x9ffc) || ($word >= 0xe040 && $word <= 0xebbf)) {
                    return QR_MODE_KANJI;
                }
            }
        }
        
        return QR_MODE_8;
    }
} 