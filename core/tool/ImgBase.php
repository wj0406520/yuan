<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  图片bash64互转
+----------------------------------------------------------------------
*/
namespace core\tool;

class ImgBase
{

    static $error='';

    static $types='.gif|.jpeg|.png|.bmp';

    /**
     * [imgToStr 图片转base64字符]
     * @param  [string] $filename [图片名称]
     * @return [string]           [图片转base64字符]
     */
    static public function imgToStr($filename){

        if(!is_file($filename)){
            return false;
        }

        $types = self::$types;

        $image_info = getimagesize($filename);

        $ext = image_type_to_extension($image_info['2']);

        if(!stripos($types,$ext)){
            return false;
        }


        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filename)));

        return $base64_image_content;

    }

    /**
     * [strToImg base64字符转图片]
     * @param  [string] $base64_image_content [base64字符]
     * @return [string]                       [图片地址]
     */
    static public function strToImg($base64_image_content){

        if(!$base64_image_content){
            self::$error='fileNo';
            return false;
        }

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $upload_dir = '/image/'.date('Ym/d').'/';
            $newname = time(). mt_rand(100, 999). '.' .$type;
            $path=DATA.$upload_dir;
            if(!is_dir($path)){
                mkdir($path, 0777, true);
            }
            if (!file_put_contents($path.$newname, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                self::$error='fileFail';
                return false;
            }

          return $upload_dir.$newname;
        }else{
            self::$error='fileFail';
            return false;
        }

    }
}