<?php
class QRspec {
    public static $capacity = array(
        array(0, 0, 0, 0),
        array(17, 14, 11, 7),
        array(32, 26, 20, 14),
        array(53, 42, 32, 24),
        array(78, 62, 46, 34),
        array(106, 84, 60, 44),
        array(134, 106, 74, 58),
        array(154, 122, 86, 64),
        array(192, 152, 108, 84),
        array(230, 180, 130, 98),
        array(271, 213, 151, 119),
        array(321, 251, 177, 137),
        array(367, 287, 203, 155),
        array(425, 331, 241, 177),
        array(458, 362, 258, 194),
        array(520, 412, 292, 220),
        array(586, 450, 322, 250),
        array(644, 504, 364, 280),
        array(718, 560, 394, 310),
        array(792, 624, 442, 338),
        array(858, 666, 482, 382),
        array(929, 711, 509, 403),
        array(1003, 779, 565, 439),
        array(1091, 857, 611, 461),
        array(1171, 911, 661, 511),
        array(1273, 997, 715, 535),
        array(1367, 1059, 751, 593),
        array(1465, 1125, 805, 625),
        array(1528, 1190, 868, 658),
        array(1628, 1264, 908, 698),
        array(1732, 1370, 982, 742),
        array(1840, 1452, 1030, 790),
        array(1952, 1538, 1112, 842),
        array(2068, 1628, 1168, 898),
        array(2188, 1722, 1228, 958),
        array(2303, 1809, 1283, 983),
        array(2431, 1911, 1351, 1051),
        array(2563, 1989, 1423, 1093),
        array(2699, 2099, 1499, 1139),
        array(2809, 2213, 1579, 1219),
        array(2953, 2331, 1663, 1273)
    );
    
    public static function getDataCapacity($version, $level) {
        if ($version < 1 || $version > 40) {
            return 0;
        }
        
        $levelIndex = array('L' => 0, 'M' => 1, 'Q' => 2, 'H' => 3);
        if (!isset($levelIndex[$level])) {
            return 0;
        }
        
        return self::$capacity[$version - 1][$levelIndex[$level]];
    }
    
    public static function getWidth($version) {
        if ($version < 1 || $version > 40) {
            return 0;
        }
        return ($version - 1) * 4 + 21;
    }
    
    public static function getAlignmentPattern($version) {
        if ($version < 1 || $version > 40) {
            return array();
        }
        
        $patterns = array(
            1 => array(),
            2 => array(6, 18),
            3 => array(6, 22),
            4 => array(6, 26),
            5 => array(6, 30),
            6 => array(6, 34),
            7 => array(6, 22, 38),
            8 => array(6, 24, 42),
            9 => array(6, 26, 46),
            10 => array(6, 28, 50),
            11 => array(6, 30, 54),
            12 => array(6, 32, 58),
            13 => array(6, 34, 62),
            14 => array(6, 26, 46, 66),
            15 => array(6, 26, 48, 70),
            16 => array(6, 26, 50, 74),
            17 => array(6, 30, 54, 78),
            18 => array(6, 30, 56, 82),
            19 => array(6, 30, 58, 86),
            20 => array(6, 34, 62, 90),
            21 => array(6, 28, 50, 72, 94),
            22 => array(6, 26, 50, 74, 98),
            23 => array(6, 30, 54, 78, 102),
            24 => array(6, 28, 54, 80, 106),
            25 => array(6, 32, 58, 84, 110),
            26 => array(6, 30, 58, 86, 114),
            27 => array(6, 34, 62, 90, 118),
            28 => array(6, 26, 50, 74, 98, 122),
            29 => array(6, 30, 54, 78, 102, 126),
            30 => array(6, 26, 52, 78, 104, 130),
            31 => array(6, 30, 56, 82, 108, 134),
            32 => array(6, 34, 60, 86, 112, 138),
            33 => array(6, 30, 58, 86, 114, 142),
            34 => array(6, 34, 62, 90, 118, 146),
            35 => array(6, 30, 54, 78, 102, 126, 150),
            36 => array(6, 24, 50, 76, 102, 128, 154),
            37 => array(6, 28, 54, 80, 106, 132, 158),
            38 => array(6, 32, 58, 84, 110, 136, 162),
            39 => array(6, 26, 54, 82, 110, 138, 166),
            40 => array(6, 30, 58, 86, 114, 142, 170)
        );
        
        return $patterns[$version];
    }
} 