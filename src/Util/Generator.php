<?php

namespace Akymos\Otp\Util;

use Endroid\QrCode\QrCode;

class Generator {

    protected $key;
    protected $algorithm = 'sha1';
    protected $digitNumber = 6;
    protected $interval = 30;

    public function getDigitNumber(){
        return $this->digitNumber;
    }

    public function setDigitNumber($digitNumber){
        $this->digitNumber = $digitNumber;
    }

    public function getAlgorithm(){
        return $this->algorithm;
    }

    public function setAlgorithm($algorithm){
        $this->algorithm = $algorithm;
    }

    public function getInterval(){
        return $this->interval;
    }

    public function setInterval($interval){
        $this->interval = $interval;
    }

    public function getSecretKey(){
        return $this->key;
    }

    public function setSecretKey($key){
        if (strlen($key) < 8)
            throw new \Exception('Secret key is too short. Must be at least 16 base 32 characters');

        $this->key = $key;
    }

    public function generateSecretKey($length = 16) {
        $keyTmp = "";
        $b32 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        for ($i = 0; $i < $length; $i++)
            $keyTmp .= $b32[rand(0,31)];
        $this->key = $keyTmp;
        return $keyTmp;
    }

    protected function generateQrCode($uri, $size, $binary){
        $qrCode = new QrCode();
        $qrCode->setText($uri);
        $qrCode->setSize($size);
        $qrCode->setPadding(0);
        return ($binary) ? $qrCode->get() : $qrCode->getDataUri();
    }

    protected function generateCode($input) {
        $hash = hash_hmac($this->algorithm, $this->intToBytestring($input), $this->byteSecret());
        foreach(str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }
        $offset = $hmac[19] & 0xf;
        $code = ($hmac[$offset+0] & 0x7F) << 24 |
            ($hmac[$offset + 1] & 0xFF) << 16 |
            ($hmac[$offset + 2] & 0xFF) << 8 |
            ($hmac[$offset + 3] & 0xFF);
        return $code % pow(10, $this->digitNumber);
    }

    protected function byteSecret() {
        return Base32::decode($this->key);
    }

    protected function intToBytestring($int) {
        $result = Array();
        while($int != 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        return str_pad(join("",array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
} 