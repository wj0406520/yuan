<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  统一封装工具
+----------------------------------------------------------------------
*/
namespace service\package;

use core\tool\Upload;
use core\yuan\WebError;
use core\yuan\Error;

class Tool
{

    public static function setScene($arr)
    {
        $temp = [];
        foreach ($arr as $key => $value) {
            $temp[] = $key.$value;
        }
        $temp = implode('jj', $temp);
        return $temp;
    }
    public static function getScene($str)
    {
        $arr = [];
        $temp = explode('jj', $str);
        foreach ($temp as $value) {
            $k = substr($value,0,1);
            $arr[$k] = substr($value,1);
        }
        return $arr;
    }


    public static function upload($name)
    {

        $re = Upload::connect($name, $uptype='image', $size=2);
        if(!$re){
            self::errorMsg(Upload::$error);
        }
        if($re==1){
            $re = '';
        }
        return $re;
    }

    public static function errorMsg($data = '')
    {
        $error = [
            'message'=>$data,
            'name'=>$this->error
        ];
        WebError::getError($error);
    }

    public static function error($message)
    {
      Error::setMessage($message);
    }
}
