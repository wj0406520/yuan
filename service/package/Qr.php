<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  显示二维码
+----------------------------------------------------------------------
*/
namespace service\package;


class Qr
{
	public static function string($str = "")
	{
        include_once TOOL.'QrCode.php';
        \QRcode::png($str,false,QR_ECLEVEL_Q,8,3);
        exit;
	}
	public static function scan($img_path = "")
	{
        include_once TOOL.'QrCode.php';
        $image = new ZBarCodeImage("test2.jpg");
		$scanner = new ZBarCodeScanner();
		$barcode = $scanner->scan($image);
        exit;
	}
}