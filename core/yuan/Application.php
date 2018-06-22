<?php

/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-29
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  程序运行函数
|			 因为需要被不同的版本运用所以分为init和run
|			 run专门为cgi模式，init为cgi和cli同时使用的
+----------------------------------------------------------------------
*/
namespace core\yuan;


class Application
{

	private function __construct()
	{
	}

	public static function init()
	{
	    if(USE_COMPOSER && is_file($file = VENDOR.'autoload.php')){
	    	require($file);
	    }
    	// error_reporting(E_ALL);
    	// ini_set("display_errors", "On");
		// 注册自动加载机制
		self::splAutoload();
		// 注册处理错误函数
		Error::init();
		// $arr = get_defined_constants(1);
		// print_r($arr);
		// echo 11;
	}

	public static function run()
	{
		// 路由处理
		Prourl::parseUrl();
		// $config = new Config();
		// $config2 = new Config();
		// print_r($config);
		// print_r(Config::get('route.api'));
		// print_r(Config::get(''));
		Prourl::run();
		// $arr = get_defined_constants(1);
		// print_r($arr['user']);
// exit;
		// 结束运行
		self::end();
	}

	private static function end()
	{
		Log::writeFile();
	}

	private static function splAutoload()
	{
		// 注册自动加载函数
		spl_autoload_register(__NAMESPACE__.'\Application::autoload');
	}

	//自动加载函数
	private static function autoload($class)
	{
	    $class = str_replace('\\', '/', $class);
	    $class .= '.php';
	    $file = ROOT . $class;
	    // echo $class;
	    // echo '<br>';
	    if (is_file($file)) {
	        require($file);
	    } else {
	        // debug('not found class ' . $class);
	    }
	}

}
