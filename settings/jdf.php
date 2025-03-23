<?php
class JDF {
    private static $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    private static $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    
    public static function gregorian_to_jalali($gy, $gm, $gd) {
        $g_d_m = self::$g_days_in_month;
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) + $gd + ((int)((367 * $gm - 362) / 12));
        
        $jy = -1595 + (33 * ((int)($days / 12053)));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $days -= 186;
            $jm = 7 + (int)($days / 30);
            $jd = 1 + ($days % 30);
        }
        
        return array($jy, $jm, $jd);
    }
    
    public static function jalali_to_gregorian($jy, $jm, $jd) {
        $j_d_m = self::$j_days_in_month;
        $jy1 = $jy - 979;
        $jm1 = $jm - 1;
        $jd1 = $jd - 1;
        
        $jdn = 365 * $jy1 + ((int)($jy1 / 33)) * 8 + ((int)(($jy1 % 33) + 3) / 4) + $jd1 + (($jm1 < 7) ? ($jm1 * 31) : ((7 * 31) + (($jm1 - 7) * 30)));
        
        $gy = 400 * ((int)($jdn / 146097));
        $jdn %= 146097;
        
        if ($jdn > 36524) {
            $gy += 100 * ((int)(--$jdn / 36524));
            $jdn %= 36524;
        }
        
        if ($jdn > 365) {
            $gy += 4 * ((int)(--$jdn / 1461));
            $jdn %= 1461;
        }
        
        if ($jdn > 365) {
            $gy += ((int)($jdn / 365));
            $jdn %= 365;
        }
        
        $gm = 1;
        $g_d_m = self::$g_days_in_month;
        
        while ($jdn > $g_d_m[$gm - 1]) {
            $jdn -= $g_d_m[$gm - 1];
            $gm++;
        }
        
        $gd = $jdn;
        
        return array($gy, $gm, $gd);
    }
    
    public static function format_date($date, $format = 'Y/m/d') {
        $timestamp = strtotime($date);
        $gy = date('Y', $timestamp);
        $gm = date('m', $timestamp);
        $gd = date('d', $timestamp);
        
        list($jy, $jm, $jd) = self::gregorian_to_jalali($gy, $gm, $gd);
        
        $format = str_replace('Y', $jy, $format);
        $format = str_replace('m', str_pad($jm, 2, '0', STR_PAD_LEFT), $format);
        $format = str_replace('d', str_pad($jd, 2, '0', STR_PAD_LEFT), $format);
        
        return $format;
    }
} 