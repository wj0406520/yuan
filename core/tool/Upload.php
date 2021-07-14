<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  上传文件工具
+----------------------------------------------------------------------
*/
namespace core\tool;

class Upload
{
	private static $error = '';
	private static $ftype = array(
					'image'=>array('gif', 'jpg', 'jpeg', 'png', 'pneg', 'bmp'),
					'file'=>array('txt', 'doc', 'xls'),
					'flash'=>array('swf'),
					'music'=>array('mp3', 'wmv'),
					'video'=>array('mp4', 'avi', 'wmv', 'flv'),
					'zip_file'=>array('zip','rar'),
					'app'=>array('apk','ipa'),
					'all'=>array('gif', 'jpg', 'jpeg', 'png', 'pneg', 'bmp','txt', 'doc', 'xls','swf','mp3', 'wmv','mp4', 'avi', 'wmv', 'flv','zip','rar')
				);
	 //构造函数， 实例化时直接调用


	public static function connect($name, $uptype='image', $size=2)
	{

		//判断是不是有上传文件
		if(!isset($_FILES[$name]['tmp_name']) || !$_FILES[$name]['tmp_name'] || !is_uploaded_file($_FILES[$name]['tmp_name'])){
			return true;
		}
		//判断文件是否超出大小
		if($_FILES[$name]['size'] > $size * 1024 * 1024){
			self::$error='file_max';
			return false;
		}

		//截取文件后缀名
		$ext = substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.')+1);
		$a=self::$ftype;
		//判断上传文件类型
		if(!in_array(strtolower($ext), $a[$uptype])){
			self::$error='file_type';
			return false;

		}
		//上传文件夹
		$dir = $uptype.'/'.date('Ym/d').'/';
		// 上传路径
		$upload_dir = UPLOAD_DIR.$dir;

		//上传文件名
		$newname = TIME. mt_rand(100, 999). '.' .$ext;
		//判断文件夹是否存在
		if(!is_dir($upload_dir)){
			mkdir($upload_dir, 0777, true);
			chmod($upload_dir, 0777);
		}
		//移动临时文件到指定文件夹
		if(!move_uploaded_file($_FILES[$name]['tmp_name'], $upload_dir . $newname)){
			if(!copy($_FILES[$name]['tmp_name'], $upload_dir . $newname)){
				self::$error='file_fail';
				return false;
			}
		}

		$temp = str_replace(DATA, '/', UPLOAD_DIR);
		//上传文件路径
		return $temp.$dir.$newname;

  }

}
