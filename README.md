#otp

**otp** is PHP library that enable otp auth (2-step verification)

## Installation

Add a dependency on `akymos/otp` to your `composer.json` file.

    {
        "require": {
            "akymos/otp": "dev-master"
        }
    }

##Usage
    //Time Based
    $tb = new Akymos\Otp\TimeBased();
    $tb->setSecretKey("yoursecretkey");
    $tb->verify("457584");

    //Counter Based
    $cb = new Akymos\Otp\CounterBased();
    $cb->setSecretKey("yoursecretkey");
    $cb->verify("343434", 1);
