<?php
class QRmask {
    public static function getMask($width, $frame, $mask) {
        $bitMask = array();
        
        for ($y = 0; $y < $width; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($frame[$y][$x] & 0x80) {
                    $bitMask[$y][$x] = 0;
                } else {
                    $bitMask[$y][$x] = self::getMaskBit($mask, $x, $y);
                }
            }
        }
        
        return $bitMask;
    }
    
    public static function getMaskBit($mask, $x, $y) {
        switch ($mask) {
            case 0:
                return ($x + $y) & 1;
            case 1:
                return $y & 1;
            case 2:
                return $x % 3;
            case 3:
                return ($x + $y) % 3;
            case 4:
                return ((int)($y / 2) + (int)($x / 3)) & 1;
            case 5:
                return ($x * $y) & 1;
            case 6:
                return ($x * $y) % 3;
            case 7:
                return (($x * $y) % 3 + ($x + $y) & 1) & 1;
            default:
                return 0;
        }
    }
    
    public static function getBestMask($width, $frame) {
        $minDemerit = PHP_INT_MAX;
        $bestMask = 0;
        
        for ($mask = 0; $mask < 8; $mask++) {
            $bitMask = self::getMask($width, $frame, $mask);
            $demerit = self::getDemerit($width, $bitMask);
            
            if ($demerit < $minDemerit) {
                $minDemerit = $demerit;
                $bestMask = $mask;
            }
        }
        
        return $bestMask;
    }
    
    private static function getDemerit($width, $bitMask) {
        $demerit = 0;
        
        // بررسی الگوهای افقی
        for ($y = 0; $y < $width; $y++) {
            $head = 0;
            $count = 1;
            
            for ($x = 1; $x < $width; $x++) {
                if ($bitMask[$y][$x] == $bitMask[$y][$x-1]) {
                    $count++;
                    if ($count >= 5) {
                        $demerit += 3 + ($count - 5);
                    }
                } else {
                    $count = 1;
                }
            }
        }
        
        // بررسی الگوهای عمودی
        for ($x = 0; $x < $width; $x++) {
            $head = 0;
            $count = 1;
            
            for ($y = 1; $y < $width; $y++) {
                if ($bitMask[$y][$x] == $bitMask[$y-1][$x]) {
                    $count++;
                    if ($count >= 5) {
                        $demerit += 3 + ($count - 5);
                    }
                } else {
                    $count = 1;
                }
            }
        }
        
        // بررسی الگوهای 2x2
        for ($y = 0; $y < $width - 1; $y++) {
            for ($x = 0; $x < $width - 1; $x++) {
                if ($bitMask[$y][$x] == $bitMask[$y][$x+1] &&
                    $bitMask[$y][$x] == $bitMask[$y+1][$x] &&
                    $bitMask[$y][$x] == $bitMask[$y+1][$x+1]) {
                    $demerit += 3;
                }
            }
        }
        
        // بررسی نسبت 1:1:3:1:1
        for ($y = 0; $y < $width - 6; $y++) {
            for ($x = 0; $x < $width - 6; $x++) {
                if ($bitMask[$y][$x] == 1 &&
                    $bitMask[$y][$x+1] == 0 &&
                    $bitMask[$y][$x+2] == 0 &&
                    $bitMask[$y][$x+3] == 0 &&
                    $bitMask[$y][$x+4] == 0 &&
                    $bitMask[$y][$x+5] == 1 &&
                    $bitMask[$y][$x+6] == 0) {
                    $demerit += 40;
                }
            }
        }
        
        for ($x = 0; $x < $width - 6; $x++) {
            for ($y = 0; $y < $width - 6; $y++) {
                if ($bitMask[$y][$x] == 1 &&
                    $bitMask[$y+1][$x] == 0 &&
                    $bitMask[$y+2][$x] == 0 &&
                    $bitMask[$y+3][$x] == 0 &&
                    $bitMask[$y+4][$x] == 0 &&
                    $bitMask[$y+5][$x] == 1 &&
                    $bitMask[$y+6][$x] == 0) {
                    $demerit += 40;
                }
            }
        }
        
        return $demerit;
    }
} 