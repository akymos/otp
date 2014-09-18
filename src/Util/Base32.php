<?php


namespace Akymos\Otp\Util;


class Base32 {

    private static $lut = array(
        "A" => 0,	"B" => 1,
        "C" => 2,	"D" => 3,
        "E" => 4,	"F" => 5,
        "G" => 6,	"H" => 7,
        "I" => 8,	"J" => 9,
        "K" => 10,	"L" => 11,
        "M" => 12,	"N" => 13,
        "O" => 14,	"P" => 15,
        "Q" => 16,	"R" => 17,
        "S" => 18,	"T" => 19,
        "U" => 20,	"V" => 21,
        "W" => 22,	"X" => 23,
        "Y" => 24,	"Z" => 25,
        "2" => 26,	"3" => 27,
        "4" => 28,	"5" => 29,
        "6" => 30,	"7" => 31
    );

    public static function decode($b32) {
        $b32 	= strtoupper($b32);

        if (!preg_match('/^[ABCDEFGHIJKLMNOPQRSTUVWXYZ234567]+$/', $b32, $match))
            throw new \Exception('Invalid characters in the base32 string.');

        $l 	= strlen($b32);
        $n	= 0;
        $j	= 0;
        $binary = "";

        for ($i = 0; $i < $l; $i++) {

            $n = $n << 5;
            $n = $n + self::$lut[$b32[$i]];
            $j = $j + 5;

            if ($j >= 8) {
                $j = $j - 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }

        return $binary;
    }
} 