<?php

namespace Akymos\Otp;

use Akymos\Otp\Util\Generator;

class TimeBased extends Generator{

    public function generateByTime($unixTime) {
        return $this->generateCode($this->timecode($unixTime));
    }

    public function now() {
        return $this->generateCode($this->timecode(time()));
    }

    public function verify($otp, $window = 5, $unixTime = null) {
        if($unixTime === null)
            $unixTime = time();

        for ($ts = $unixTime - $window; $ts <= $unixTime + $window; $ts++){
            if($otp == $this->generateByTime($ts)){
                return true;
            }
        }
        return false;
    }

    public function getUri($name) {
        return "otpauth://totp/".urlencode($name)."?secret={$this->key}";
    }

    public function getQrCodeBinary($name, $size = 100){
        return $this->generateQrCode($this->getUri($name), $size, true);
    }

    public function getQrCodeDataUri($name, $size = 100){
        return $this->generateQrCode($this->getUri($name), $size, false);
    }

    protected function timecode($timestamp) {
        return (int)( (((int)$timestamp * 1000) / ($this->interval * 1000)));
    }
} 