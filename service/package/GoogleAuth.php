<?php

namespace service\package;

class GoogleAuth
{
    //自身类
    private static $ins = NULL;

    private static $m = NULL;

    private function __construct()
    {
        include ROOT.'service/PHPGangsta/GoogleAuthenticator.php';
        self::$m = new \PHPGangsta_GoogleAuthenticator();
    }
    public static function init()
    {
        if (!(self::$ins instanceof self)) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function get($s='')
    {
        // otpauth://totp/下发系统9525?secret=BVLQR7ORLKYEP7XC
        $secret= $s?$s:self::$m->createSecret();
        $qr = self::$m->getQRCodeGoogleUrl('wwwxf'.substr($secret,0,4),$secret);
        return [
            'secret'=>$secret,
            'qr'=>$qr
        ];
    }

    public function check($secret, $code)
    {
        return self::$m->verifyCode($secret, $code, 2);
    }

}