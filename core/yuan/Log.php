<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  记录日志
+----------------------------------------------------------------------
*/
namespace core\yuan;


class Log
{

    //自身类
    private static $ins = NULL;

    private $dir = "";

    private function __construct()
    {

    }
    public static function init()
    {
        if (!(self::$ins instanceof self)) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }

    public function write($data)
    {
        if(is_array($data)){
            $str = json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            $str = $data;
        }
        $str .= PHP_EOL;
        $file_name = date("ymdH").'_'.$this->dir.".log";
        $dir = $this->getDir();

        $file = $dir.$file_name;
        $bool = 0;
        if(!is_file($file)){
            $bool = 1;
        }
        file_put_contents($file, $str, FILE_APPEND);
        $bool && chmod($file, 0777);
    }

    private function getDir()
    {
        //上传文件夹
        $dir = date('Ym/d').'/'.$this->dir.'/';
        // 上传路径
        $upload_dir = LOG_DIR.$dir;

        //判断文件夹是否存在
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
            chmod($upload_dir, 0777);
        }
        return $upload_dir;;

    }
}
