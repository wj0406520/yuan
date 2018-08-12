<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-08-12
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  图片处理类 精准操作图片
| //本函数同时需要 GD 库和 » FreeType 库。.
+----------------------------------------------------------------------
*/
namespace core\tool;
/*
$logo = './logo.jpg';
$logo1 = './log.png';
$background = "./background.jpg";

$text = "wangjie";
$text2 = "wangjie2";


$l = ImageManager::make($logo);
$l1 = ImageManager::make($logo1);

$l->widen(200)->circle()->top(200)->left(100);

$l1->widen(100)->top(10)->left(10);

$b = ImageManager::make($background)->heighten(500);

$b->text($text,20,100,function($font){
    $font->file(2);
    $font->size(40);
    $font->color('ffffff');
});
$l->text($text2,20,40,function($font){
    $font->file(1);
    $font->size(24);
    $font->color('fdf6e3');
});

$b->insert($l);
$l->insert($l1);

$b->save('aa.jpg');
$b->save('aa.png');
$b->stream('jpg');
$b->stream('png');
 */

class ImageManager
{
	private static $init = NULL;
	private $path = NULL;
	private $widen = 0;
	private $width = 0;
	private $height = 0;
	private $heighten = 0;
	private $top = 0;
	private $left = 0;
	private $insert = [];
	private $circle = 0;
	private $position = "";
	private $font = [];
	private $text = [];
	private $img = NULL;
	private $parms = NULL;
	private $type = "";

	private function __construct()
	{
	}

	// 构建一个Image对象
	public static function make($path)
	{
		if(self::$init){
			// return self::$init;
		}
		self::$init = new static;
		self::$init->path = $path;
		self::$init->getImageParms();
		return self::$init;
	}
	private function getImageParms()
	{
		$arr = getimagesize($this->path);
		$type = explode('/',$arr['mime']);
		$this->type = $type[1];
		$this->parms = $arr;
	}
	// 获取插入图片的上部距离
	public function getParms($parms)
	{
		return $this->$parms;
	}

	// 以宽度为主缩小或放大图片
	public function widen($width)
	{
		$this->widen = $width;
		return $this;
	}
	// 返回当前图片宽度
	public function width()
	{
		return $this->parms[0];
	}
	// 返回当前图片高度
	public function height()
	{
		return $this->parms[1];
	}
	// 以高度为主缩小或放大图片
	public function heighten($height)
	{
		$this->heighten = $height;
		return $this;
	}


	// 设置要插入图片的上部距离
	public function top($top)
	{
		$this->top = $top;
		return $this;
	}
	// 设置要插入图片的左部距离
	public function left($left)
	{
		$this->left = $left;
		return $this;
	}

	// 设置是否把图片设置为圆形
	public function circle()
	{
		$this->circle = 1;
		return $this;
	}
	// 把图片插入进来
	public function insert($obj)
	{
		$this->insert[] = $obj;
		return $this;
	}

	// top-left (default)
	// top-right
	// center
	// bottom-left
	// bottom-right
	// 设置距离要插入图片的位置
	// 暂时不用，等以后有需要使用
	public function position($position = "top-left")
	{
		$this->position = $position;
		return $this;
	}


	// 图片中加入文字
	public function text($str,$x = 0,$y = 0,$func = NULL)
	{
		if($func){
			$func($this);
		}
		$this->font['str'] = $str;
		$this->font['x'] = $x;
		$this->font['y'] = $y;
		$this->text[] = $this->font;
		$this->font = [];
		return $this;
	}
	// 图片中加入文字的ttf
	public function file($file)
	{
		$this->font['file'] = $file;
	}
	// 图片中加入文字的大小
	// 没有font不能改变字体大小
	public function size($size)
	{
		$this->font['size'] = $size;
	}
	// 图片中加入文字的颜色
	public function color($color)
	{
		$this->font['color'] = $color;
	}

	// 保存文件到某个地方
	// *.png|*.jpg
	public function save($file_name)
	{
		$arr = explode('.',$file_name);
		$format = end($arr);
		$this->run($format, $file_name);
	}

	// 输出到浏览器
	// png|jpg
	public function stream($format = "png")
	{
		header('Content-Type: image/'.$format);
		$this->run($format);
	}

	private function run($format,$file_name = null)
	{
		if($format == "png"){
			$format = "png";
		}elseif ($format=="jpg") {
			$format = "jpeg";
		}else{
			echo $format." is not png|jpg";
			exit;
		}
		$this->createCanvas();
		if($this->circle==1){
			$this->createCycle();
		}
		$this->insetImage();
		$this->textImage();

		if($format=="png"){
			imagepng($this->img,$file_name);
		}else{
			imagejpeg($this->img,$file_name);
		}
		imagedestroy($this->img);
		return $format;
	}
	public function createCanvas()
	{
		$this->getXY();
		$x = $this->width;
		$y = $this->height;
		$im = imagecreatetruecolor($x,$y);
		imagesavealpha($im, true);
		$bg = imagecolorallocatealpha($im, 255, 255, 255, 127);
		imagefill($im, 0, 0, $bg);

		if($this->type=="jpeg"){
			$img = imagecreatefromjpeg($this->path);
		} else if($this->type=="png"){
			$img = imagecreatefrompng($this->path);
		}else{
			echo "image type error";
			exit;
		}
		//copy部分图像并调整
		imagecopyresized($im, $img,0, 0,0, 0,$x, $y, $this->width(), $this->height());
		imagedestroy($img);
		$this->img = $im;
	}
	public function createCycle()
	{
		$src_img = $this->img;

		$w   = min($this->width, $this->height);
		$h   = $w;
		$img = imagecreatetruecolor($w, $h);
		//这一句一定要有
		imagesavealpha($img, true);
		//拾取一个完全透明的颜色,最后一个参数127为全透明
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		$r   = $w / 2; //圆半径
		$y_x = $r; //圆心X坐标
		$y_y = $r; //圆心Y坐标
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$rgbColor = imagecolorat($src_img, $x, $y);
				if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}
			}
		}
		imagedestroy($src_img);
		$this->img = $img;
	}
	private function getXY()
	{
		$widen = $this->getParms('widen');
		$heighten = $this->getParms('heighten');
		$width = $this->width();
		$height = $this->height();
		if($widen){
			$this->width = $widen;
			$this->height = intval($height*$widen/$width);
		}
		if($heighten){
			$this->width = intval($width*$heighten/$height);
			$this->height = $heighten;
		}
	}

	public function insetImage()
	{
		if(!$this->insert){
			return false;
		}
		$arr = $this->insert;
		foreach ($arr as $key => $value) {
			$value->createCanvas();
			$value->textImage();
			$value->insetImage();
			$width = $value->getParms('width');
			$height = $value->getParms('height');
			if($value->getParms('circle')==1){
				$value->createCycle();
				$width = min($width,$height);
				$height = $width;
			}
			imagecopyresampled($this->img,
				$value->getParms('img'),
				$value->getParms('left'),
				$value->getParms('top'),0,0,
				$width,
				$height,
				$width,
				$height);

		}
	}
	public function textImage()
	{
		if(!$this->text){
			return false;
		}
		$arr = $this->text;
		foreach ($arr as $key => $value) {
			$color = [255,255,255];
			$size = isset($value['size'])?$value['size']:12;
			$x = $value['x'];
			$y = $value['y'];
			$str = $value['str'];
			$no_font = 0;
			if(isset($value['file']) && file_exists($value['file'])){
				$file = $value['file'];
			}else{
				$file = isset($value['file'])?$value['file']:1;
				$no_font = 1;
			}
			if(isset($value['color'])){
				$color = str_split($value['color'],2);
				$color[0] = hexdec($color[0]);
				$color[1] = hexdec($color[1]);
				$color[2] = hexdec($color[2]);
			}

			$text_color = imagecolorallocate($this->img, $color[0], $color[1], $color[2]);
			if($no_font==1){
				imagestring($this->img, $file, $x, $y,  $str, $text_color);
			}else{
				imagettftext( $this->img , $size, 0 , $x , $y , $text_color , $file , $str );
			}
		}
	}
}