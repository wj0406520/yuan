<?php
/*
+----------------------------------------------------------------------
| author     王杰
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

	public static function parseUrl()
	{

		$m = $a = $app = '';

		if (isset($_SERVER['REQUEST_URI'])) {

			$url = $_SERVER["REQUEST_URI"];

			$pathinfo = explode('?', trim($url, "/"));


			$pathinfo = explode('/', $pathinfo[0]);

   			$app = array_shift($pathinfo); //将数组开头的单元移出数组

   			$m = array_shift($pathinfo);

   			$a = array_shift($pathinfo);
		}

		$app = (!empty($app) ? $app : 'index');   //默认是index项目

		$m = (!empty($m) ? $m : 'index');    //默认是index模块

		$a = (!empty($a) ? $a : 'index');   //默认是index动作

		$m = ucfirst($m);
	    //控制器中的方法名
	    define('URL_MODEL',$a);
	    //控制器名称
	    define('URL_CONTROL',$m);
	    $config = Config::get('url.'.$app);

	    if($config['is_web']==0){
			Route::setDefaultWeb(0);
	    }
	    // APP
	    define('APP',$config['app']);

	    define('VIEWS_DIR', ROOT.PROJECT.'/'.APP.'/'.HTML.'/');

	    define('PATH', '/'.$app.'/');

	}


	public static function run()
	{
	    $m = PROJECT. '\\' . APP . '\\' . CONTROLS . '\\'  . URL_CONTROL;

	    if (!class_exists($m)) {
	    	$message = 'not found class ' . URL_CONTROL;
	    	Error::setMessage($message);
	    }
	    $a =  URL_MODEL. ACTION;

	    $ontrol = new $m();
	    if (method_exists($ontrol,$a)) {
	        call_user_func([$ontrol,$a]);
	    } else {
	    	$message = 'not found function ' . $a;
	    	Error::setMessage($message);
	    }
	    // call_user_func($name .$m.'->'.$a);
	    // call_user_func(array($name.$m, $a));
	}
}