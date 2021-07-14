<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  图片处理类 验证码类 缩略图 水印
+----------------------------------------------------------------------
*/
namespace core\tool;

class Image
{
    private $make_img = null;
    private $insert_img = [];
    private $text_img = [];
    // imageInfo 分析图片的信息
    // return array()
    private function imageInfo($image) {
        // 判断图片是否存在
        if(!file_exists($image)) {
        }
        $info = getimagesize($image);
        if($info == false) {
            return false;
        }
        // 此时info分析出来,是一个数组
        $img['width'] = $info[0];
        $img['height'] = $info[1];
        $img['ext'] = substr($info['mime'],strpos($info['mime'],'/')+1);
        return $img;
    }

    // 设置图片变成圆
    public static function circular($imgpath) {
        $ext     = pathinfo($imgpath);
        $ext['extension'] = isset($ext['extension'])?$ext['extension']:'jpg';
        $src_img = null;
        switch ($ext['extension']) {
            case 'jpg':
                $src_img = imagecreatefromjpeg($imgpath);
                break;
            case 'png':
                $src_img = imagecreatefrompng($imgpath);
                break;
        }
        $wh  = getimagesize($imgpath);
        $w   = $wh[0];
        $h   = $wh[1];
        $w   = min($w, $h);
        $h   = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);

        imagefill($img, 0, 0, $bg);

        $r   = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if($ext['extension']=='png'){
                    $arr = imagecolorsforindex($src_img, $rgbColor);
                    $rgbColor = imagecolorallocatealpha($img,$arr['red'],$arr['green'],$arr['blue'],$arr['alpha']);
                }
                // $rgbColor = 0;
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }

            }
        }
        imagedestroy($src_img);
        return $img;
    }



    /*
        加水印功能
        parm String $dst 等操作图片
        parm String $water 水印小图
        parm String $save,不填则默认替换原始图
    */
    public function water($dst,$water,$save=NULL,$pos=2,$alpha=50) {
        // 先保证2个图片存在
        if(!file_exists($dst) || !file_exists($water)) {
            return false;
        }

        // 首先保证水印不能比待操作图片还大
        $dinfo = $this->imageInfo($dst);
        $winfo = $this->imageInfo($water);

        if($winfo['height'] > $dinfo['height'] || $winfo['width'] > $dinfo['width']) {
            return false;
        }

        // 两张图,读到画布上! 但是图片可能是png,可能是jpeg,用什么函数读?
        $dfunc = 'imagecreatefrom' . $dinfo['ext'];
        $wfunc = 'imagecreatefrom' . $winfo['ext'];

        if(!function_exists($dfunc) || !function_exists($wfunc)) {
            return false;
        }


        // 动态加载函数来创建画布
        $dim = $dfunc($dst);  // 创建待操作的画布
        $wim = $wfunc($water);  // 创建水印画布


        // 根据水印的位置 计算粘贴的坐标
        switch($pos) {
            case 0: // 左上角
            $posx = 0;
            $posy = 0;
            break;

            case 1: // 右上角
            $posx = $dinfo['width'] - $winfo['width'];
            $posy = 0;
            break;

            case 3: // 左下角
            $posx = 0;
            $posy = $dinfo['height'] - $winfo['height'];
            break;

            default:
            $posx = $dinfo['width'] - $winfo['width'];
            $posy = $dinfo['height'] - $winfo['height'];
        }


        // 加水印
        imagecopymerge ($dim,$wim, $posx , $posy , 0 , 0 , $winfo['width'] , $winfo['height'] , $alpha);

        // 保存
        if(!$save) {
            $save = $dst;
            unlink($dst); // 删除原图
        }

        $createfunc = 'image' . $dinfo['ext'];
        $createfunc($dim,$save);

        imagedestroy($dim);
        imagedestroy($wim);

        return true;
    }


    /**
        thumb 生成缩略图
        $arr = ['height'=>0,'width'=>0]
    **/
    public function thumb($dst, $save = NULL,$arr = NULL)
    {
        // 首先判断待处理的图片存不存在
        $dinfo = $this->imageInfo($dst);
        if($dinfo == false) {
            return false;
        }
        $height = isset($arr['height'])?$arr['height']:0;
        $width = isset($arr['width'])?$arr['width']:0;

        if($height==0){
            $height = (int)($dinfo['height']*$width/$dinfo['width']);
        }

        if($width==0){
            $width = (int)($dinfo['width']*$height/$dinfo['height']);
        }
        // 计算缩放比例
        $calc = min($width/$dinfo['width'], $height/$dinfo['height']);

        if($calc==0){
            return false;
        }

        // 创建原始图的画布
        $dfunc = 'imagecreatefrom' . $dinfo['ext'];
        $dim = $dfunc($dst);

        // 创建缩略画布
        $tim = imagecreatetruecolor($width,$height);

        // 创建白色填充缩略画布
        $white = imagecolorallocate($tim,255,255,255);

        // 填充缩略画布
        imagefill($tim,0,0,$white);

        // 复制并缩略
        $dwidth = (int)($dinfo['width']*$calc);
        $dheight = (int)($dinfo['height']*$calc);

        $paddingx = (int)(($width - $dwidth) / 2);
        $paddingy = (int)(($height - $dheight) / 2);


        imagecopyresampled($tim,$dim,$paddingx,$paddingy,0,0,$dwidth,$dheight,$dinfo['width'],$dinfo['height']);

        // 保存图片
        if(!$save) {
            $save = $dst;
            unlink($dst);
        }

        $createfunc = 'image' . $dinfo['ext'];
        $createfunc($tim,$save);

        imagedestroy($dim);
        imagedestroy($tim);

        return true;

    }

    //写验证码
    /*
        author: dabao
    */
    public static function captcha($width=50,$height=25) {
            //造画布
            $image = imagecreatetruecolor($width,$height) ;

            //造背影色
            $gray = imagecolorallocate($image, 200, 200, 200);

            //填充背景
            imagefill($image, 0, 0, $gray);

            //造随机字体颜色
            $color = imagecolorallocate($image, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125)) ;
            //造随机线条颜色
            $color1 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));
            $color2 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));
            $color3 =imagecolorallocate($image, mt_rand(100, 125), mt_rand(100, 125), mt_rand(100, 125));

            //在画布上画线
            imageline($image, mt_rand(0, 50), mt_rand(0, 25), mt_rand(0, 50), mt_rand(0, 25), $color1) ;
            imageline($image, mt_rand(0, 50), mt_rand(0, 20), mt_rand(0, 50), mt_rand(0, 20), $color2) ;
            imageline($image, mt_rand(0, 50), mt_rand(0, 20), mt_rand(0, 50), mt_rand(0, 20), $color3) ;

            //在画布上写字
            $text = substr(str_shuffle('ABCDEFGHIJKMNPRSTUVWXYZabcdefghijkmnprstuvwxyz23456789'), 0,4) ;
            imagestring($image, 5, 7, 5, $text, $color) ;

            //显示、销毁
            header('content-type: image/jpeg');
            imagejpeg($image);
            imagedestroy($image);
    }


}