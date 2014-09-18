<?php

namespace Akymos\Otp;

use Akymos\Otp\Util\Generator;

class CounterBased extends Generator{

    /**
     * enerate the verify code based on given count
     *
     * @param $count count
     *
     * @return int verification code
     */
    public function generateByCount($count) {
        return $this->generateCode($count);
    }

    /**
     * Verify the given code
     *
     * @param $otp verify code
     * @param $counter counter
     *
     * @return bool true if the code is valid
     */
    public function verify($otp, $counter) {
        return ($otp == $this->generateByCount($counter));
    }

    /**
     * Get the uri for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param $name account name
     * @param     $initial_count counter
     *
     * @return string uri
     */
    public function getUri($name, $initial_count) {
        return "otpauth://hotp/".urlencode($name)."?secret={$this->key}&counter=$initial_count";
    }

    /**
     * Get the Qr for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param     $name account name
     * @param     $initial_count counter
     * @param int $size size of the Qr Code image (px)
     *
     * @return binary binary of the generated png
     */
    public function getQrCodeBinary($name, $initial_count, $size = 100){
        return $this->generateQrCode($this->getUri($name, $initial_count), $size, true);
    }

    /**
     * Get the Qr for code generation app (Ex: Google Authenticator, Authy)
     *
     * @param     $name account name
     * @param     $initial_count counter
     * @param int $size size of the Qr Code image (px)
     *
     * @return string base64 of the generated png
     */
    public function getQrCodeDataUri($name, $initial_count, $size = 100){
        return $this->generateQrCode($this->getUri($name, $initial_count), $size, false);
    }
} 