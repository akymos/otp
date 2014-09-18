<?php

namespace Akymos\Otp\Util;

use Endroid\QrCode\QrCode;

class Generator {

    /** @var string */
    protected $key;

    /** @var string */
    protected $algorithm = 'sha1';

    /** @var int */
    protected $digitNumber = 6;

    /** @var int */
    protected $interval = 30;

    /**
     * @return int verification digit number length
     */
    public function getDigitNumber(){
        return $this->digitNumber;
    }

    /**
     * @param $val set verification digit number length
     */
    public function setDigitNumber($val){
        $this->digitNumber = $val;
    }

    /**
     * @return string get the hashing algorithm
     */
    public function getAlgorithm(){
        return $this->algorithm;
    }

    /**
     * @param $val set the hashing algorithm
     */
    public function setAlgorithm($val){
        $this->algorithm = $val;
    }

    /**
     * @return int get the refresh interval (second)
     */
    public function getInterval(){
        return $this->interval;
    }

    /**
     * @param $val set the refresh interval (second)
     */
    public function setInterval($val){
        $this->interval = $val;
    }

    /**
     * @return string get the secret key used for generate the verification code
     */
    public function getSecretKey(){
        return $this->key;
    }

    /**
     * @param $val set the secret key used for generate the verification code
     *
     * @throws \Exception
     */
    public function setSecretKey($val){
        if (strlen($val) < 8)
            throw new \Exception('Secret key is too short. Must be at least 16 base 32 characters');

        $this->key = $val;
    }

    /**
     * generate a random secret key used for generate the verification code
     *
     * @param int $length length of the key
     *
     * @return string rendom generated key
     */
    public function generateSecretKey($length = 16) {
        $keyTmp = "";
        $b32 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
        for ($i = 0; $i < $length; $i++)
            $keyTmp .= $b32[rand(0,31)];
        $this->key = $keyTmp;
        return $keyTmp;
    }

    /**
     * Generate the QrCode
     *
     * @param $uri
     * @param $size
     * @param $binary
     *
     * @return mixed
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     */
    protected function generateQrCode($uri, $size, $binary){
        $qrCode = new QrCode();
        $qrCode->setText($uri);
        $qrCode->setSize($size);
        $qrCode->setPadding(0);
        return ($binary) ? $qrCode->get() : $qrCode->getDataUri();
    }

    /**
     * Generate verification code
     *
     * @param $input
     *
     * @return int
     */
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

    /**
     * Base32 decode of the secret key
     *
     * @return string
     * @throws \Exception
     */
    protected function byteSecret() {
        return Base32::decode($this->key);
    }

    /**
     * Convert an int to a byte
     *
     * @param $int
     *
     * @return string
     */
    protected function intToBytestring($int) {
        $result = Array();
        while($int != 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        return str_pad(join("",array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
} 