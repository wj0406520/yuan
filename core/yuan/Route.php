<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  获取路由信息
+----------------------------------------------------------------------
*/
namespace core\yuan;


class Route
{
	private static $data = [];
	private static $file = 'route.php';
	private static $default = 1;

	private function __construct()
	{
	}

	public static function setDefaultWeb($default)
	{
		self::$default = $default;
	}
	public static function getWeb()
	{
		return self::get('is_web', self::$default);
	}
	public static function getCheck()
	{
		return self::get('is_check');
	}
	private static function get($config, $default=1)
	{
		$config = self::data($config);
		if($config === ''){
			$config = $default;
		}
		return $config;
	}

	public static function data($key = '', $app = APP)
	{

		if(!isset(self::$data[$app])){
			self::getFileDate($app);
		}
		$data = self::$data[$app];
		if(!$data || !is_array($data)){
			return '';
		}
		if($key){
			$data = isset($data[$key])?$data[$key]:'';
		}
      	return $data;
	}

	private static function getFileDate($app)
	{
		$file = ROOT.PROJECT.'/'.$app.'/'.ROUTE.'/'.self::$file;
		if(!file_exists($file)){
			return ;
		}
        $data = include $file;
        if(defined('URL_CONTROL')){
	        $str = strtolower(URL_CONTROL.'.'.URL_MODEL);
	        self::$data[$app] = isset($data[$str])?$data[$str]:'';
        }else{
        	self::$data[$app] = $data;
        }
	}
}
