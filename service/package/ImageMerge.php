<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  图片操作
+----------------------------------------------------------------------
*/
namespace service\package;
use core\tool\ImageManager as Image;


class ImageMerge
{

    private $background = null;
    private $logo_img = null;
    private $qrcode = null;
    private $font = null;
    private $parms = null;

    private $circular_background_color = ['r'=>255,'g'=>255,'b'=>255];
    // 设置背景图像
    public function setBackground($file)
    {
        $this->background = $file;
        return $this;
    }

    // 设置logo文件
    public function setLogoImg($file)
    {
        $this->logo_img = $file;
        return $this;
    }

    // 设置qrcode文件
    public function setQrcode($file)
    {
        $this->qrcode = $file;
        return $this;
    }

    // 设置昵称内容
    public function setFont($str)
    {
        $this->font = $str;
        return $this;
    }

    // 设置参数，X轴Y轴和大小
    public function setParms($arr)
    {
        $this->parms = $arr;
        return $this;
    }

    public function getImage()
    {
        $imgg = $this->qrcode;
        $img = Image::make($this->background);

        $log = Image::make($this->logo_img)
        ->top($this->getParms('avatar_h'))
        ->left($this->getParms('avatar_x'))
        ->widen($this->getParms('avatar_w'))->circle();

        $qr = Image::make($imgg)
        ->top($this->getParms('qrcode_h'))
        ->left($this->getParms('qrcode_x'))
        ->widen($this->getParms('qrcode_w'));
        if($this->parms['qrcode_c']==0){
            $qr->circle();
        }
        // $log = Image::make($log)->widen($this->getParms('avatar_w'));
        $img->widen(750);
        $img->insert($qr);
        $img->insert($log);

        $img->text($this->font,
            $this->getParms('font_x'),
            $this->getParms('font_h')+$this->getParms('font_w'), function($font) {
            $file = DATA.'ttf/simsun.ttc';
            $font->file($file);
            $font->size($this->getParms('font_w'));
            $font->color($this->parms['color']);
        });

        $img->stream('jpg');
        exit;
        $imgg = $this->qrcode;
        if($this->parms['qrcode_c']==0){
            $imgg = ImageCycle::circular($this->qrcode);
        }
        $log = ImageCycle::circular($this->logo_img);

        $img = Image::make($this->background);
        $qr = Image::make($imgg)->widen($this->getParms('qrcode_w'));
        $log = Image::make($log)->widen($this->getParms('avatar_w'));
        $img->widen(750);
        $img->insert($qr,'top-left',
            $this->getParms('qrcode_x'),
            $this->getParms('qrcode_h'));
        $img->insert($log,'top-left',
            $this->getParms('avatar_x'),
            $this->getParms('avatar_h'));

        $img->text($this->font,
            $this->getParms('font_x'),
            $this->getParms('font_h')+$this->getParms('font_w'), function($font) {
            $file = DATA.'ttf/simsun.ttc';
            $font->file($file);
            $font->size($this->getParms('font_w'));
            $font->color('#'.$this->parms['color']);
        });
        $stream = $img->stream('png', 60);
        header("Content-type:image/png");
        echo $stream;
    }


    private function getParms($str)
    {
        return (intval(($this->parms[$str])*750/300));
    }

}
