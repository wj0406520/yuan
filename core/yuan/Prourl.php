<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  路由 实例
+----------------------------------------------------------------------
*/
namespace core\yuan;

class Prourl
{

	public static $data = [
		'url_model'=>'',
		'url_control'=>'',
		'app'=>'',
		'path'=>'',
	];

	public static function parseUrl()
	{

		$m = $a = $app = '';

		if (isset($_SERVER['REQUEST_URI'])) {

			$url = $_SERVER["REQUEST_URI"];

			$pathinfo = explode('?', trim($url, "/"));

			$pathinfo = explode('/', $pathinfo[0]);

			$pathinfo = array_filter($pathinfo);

   			$app = array_shift($pathinfo); //将数组开头的单元移出数组

   			$m = array_shift($pathinfo);

   			$a = array_shift($pathinfo);

		}

		$app = (!empty($app) ? $app : 'index');   //默认是index项目

		$m = ucfirst(!empty($m) ? $m : 'index');    //默认是index模块

		$a = (!empty($a) ? $a : 'index');   //默认是index动作

	    //控制器中的方法名
		self::$data['url_model'] = $a;
	    //控制器名称
		self::$data['url_control'] = $m;

	    $config = Config::get('url.'.$app);

	    if($config['is_web']==0){
			Route::setDefaultWeb(0);
	    }
	    // APP
		self::$data['app'] = $config['app'];

		self::$data['path'] = '/'.$app.'/';

	}

	public static function get($key)
	{
		if($key=='VIEWS_DIR'){
			return ROOT.PROJECT.'/'.self::$data['app'].'/'.HTML.'/';
		}
		$key = strtolower($key);
		return isset(self::$data[$key])?self::$data[$key]:'';
	}
	public static function set($data)
	{
		self::$data = $data;
	}

	public static function run()
	{
	    $m = PROJECT. '\\' . self::get('APP') . '\\' . CONTROLS . '\\'  . self::get('URL_CONTROL');

	    if (!class_exists($m)) {
	    	$message = 'not found class ' . $m;
	    	Error::setMessage($message);
	    }
	    $a =  self::get('URL_MODEL'). ACTION;

	    $ontrol = new $m();
	    if (method_exists($ontrol,$a)) {
	        call_user_func([$ontrol,$a]);
	    } else {
	    	$message = 'not found function ' .$m.'\\' . $a;
	    	Error::setMessage($message);
	    }
	    // call_user_func($name .$m.'->'.$a);
	    // call_user_func(array($name.$m, $a));
	}
}