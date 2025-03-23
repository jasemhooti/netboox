<?php
// حالت‌های کد QR
define('QR_MODE_NUM', 1);
define('QR_MODE_AN', 2);
define('QR_MODE_8', 4);
define('QR_MODE_KANJI', 8);
define('QR_MODE_NULL', 0);

// سطوح تصحیح خطا
define('QR_ECLEVEL_L', 1);
define('QR_ECLEVEL_M', 0);
define('QR_ECLEVEL_Q', 3);
define('QR_ECLEVEL_H', 2);

// الگوهای ماسک
define('QR_MASK_PATTERN000', 0);
define('QR_MASK_PATTERN001', 1);
define('QR_MASK_PATTERN010', 2);
define('QR_MASK_PATTERN011', 3);
define('QR_MASK_PATTERN100', 4);
define('QR_MASK_PATTERN101', 5);
define('QR_MASK_PATTERN110', 6);
define('QR_MASK_PATTERN111', 7);

// ثابت‌های چندجمله‌ای مولد
define('QR_G15', (1 << 10) | (1 << 8) | (1 << 5) | (1 << 4) | (1 << 2) | (1 << 1) | (1 << 0));
define('QR_G18', (1 << 12) | (1 << 11) | (1 << 10) | (1 << 9) | (1 << 8) | (1 << 5) | (1 << 2) | (1 << 0));
define('QR_G15_MASK', (1 << 14) | (1 << 12) | (1 << 10) | (1 << 4) | (1 << 1));

// ثابت‌های الگوی موقعیت
define('QR_PATTERN_POSITION_TABLE', array(
    array(),
    array(6, 18),
    array(6, 22),
    array(6, 26),
    array(6, 30),
    array(6, 34),
    array(6, 22, 38),
    array(6, 24, 42),
    array(6, 26, 46),
    array(6, 28, 50),
    array(6, 30, 54),
    array(6, 32, 58),
    array(6, 34, 62),
    array(6, 26, 46, 66),
    array(6, 26, 48, 70),
    array(6, 26, 50, 74),
    array(6, 30, 54, 78),
    array(6, 30, 56, 82),
    array(6, 30, 58, 86),
    array(6, 34, 62, 90),
    array(6, 28, 50, 72, 94),
    array(6, 26, 50, 74, 98),
    array(6, 30, 54, 78, 102),
    array(6, 28, 54, 80, 106),
    array(6, 32, 58, 84, 110),
    array(6, 30, 58, 86, 114),
    array(6, 34, 62, 90, 118),
    array(6, 26, 50, 74, 98, 122),
    array(6, 30, 54, 78, 102, 126),
    array(6, 26, 52, 78, 104, 130),
    array(6, 30, 56, 82, 108, 134),
    array(6, 34, 60, 86, 112, 138),
    array(6, 30, 58, 86, 114, 142),
    array(6, 34, 62, 90, 118, 146),
    array(6, 30, 54, 78, 102, 126, 150),
    array(6, 24, 50, 76, 102, 128, 154),
    array(6, 28, 54, 80, 106, 132, 158),
    array(6, 32, 58, 84, 110, 136, 162),
    array(6, 26, 54, 82, 110, 138, 166),
    array(6, 30, 58, 86, 114, 142, 170)
));

// ثابت‌های ظرفیت داده
define('QR_CAPACITY_TABLE', array(
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
)); 