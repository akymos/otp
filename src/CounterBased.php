<?php

namespace Akymos\Otp;

use Akymos\Otp\Util;

class CounterBased extends Generator{

    public function generateByCount($count) {
        return $this->generateCode($count);
    }

    public function verify($otp, $counter) {
        return ($otp == $this->generateByCount($counter));
    }

    public function getUri($name, $initial_count) {
        return "otpauth://hotp/".urlencode($name)."?secret={$this->key}&counter=$initial_count";
    }

    public function getQrCodeBinary($name, $initial_count, $size = 100){
        return $this->generateQrCode($this->getUri($name, $initial_count), $size, true);
    }

    public function getQrCodeDataUri($name, $initial_count, $size = 100){
        return $this->generateQrCode($this->getUri($name, $initial_count), $size, false);
    }
} 