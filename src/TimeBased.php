<?php

namespace Akymos\Otp;

use Akymos\Otp\Util\Generator;

class TimeBased extends Generator{

    /**
     * Generate the verify code based on given timestamp
     *
     * @param $unixTime unix timestamp
     *
     * @return int verification code
     */
    public function generateByTime($unixTime) {
        return $this->generateCode($this->timecode($unixTime));
    }

    /**
     * Generate the verify code based on current time
     *
     * @return int
     */
    public function now() {
        return $this->generateCode($this->timecode(time()));
    }

    /**
     * Verify the given code
     *
     * @param      $otp verify code
     * @param int  $window time window
     * @param null $unixTime timestamp. Default time()
     *
     * @return bool true if the code is valid
     */
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

    /**
     * Get the uri for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param $name account name
     *
     * @return string uri
     */
    public function getUri($name) {
        return "otpauth://totp/".urlencode($name)."?secret={$this->key}";
    }

    /**
     * Get the Qr for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param     $name account name
     * @param int $size size of the Qr Code image (px)
     *
     * @return binary binary of the generated png
     */
    public function getQrCodeBinary($name, $size = 100){
        return $this->generateQrCode($this->getUri($name), $size, true);
    }

    /**
     * Get the Qr for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param     $name account name
     * @param int $size size of the Qr Code image (px)
     *
     * @return string base64 of the generated png
     */
    public function getQrCodeDataUri($name, $size = 100){
        return $this->generateQrCode($this->getUri($name), $size, false);
    }

    /**
     * Generate the timecode based on timestamp and refresh interval
     *
     * @param $timestamp timestamp
     *
     * @return int generated timecode
     */
    protected function timecode($timestamp) {
        return (int)( (((int)$timestamp * 1000) / ($this->interval * 1000)));
    }
} 