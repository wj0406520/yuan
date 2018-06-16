<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  配置获取 实例
+----------------------------------------------------------------------
*/

namespace core\yuan;

class Config
{

	private static $data = [];
	private static $config = [];
	private static $config_file = 'config.php';
	private static $re = null;

	private function __construct()
	{
	}

	public static function get($config)
	{
		$data = self::init($config);
		// print_r($config);
		array_walk($config,function($key) use(&$data){
			if(!is_array($data) || !isset($data[$key])){
				self::error($key.' is not data key');
			}
			$data = $data[$key];
		});
		return $data;
		// print_r($data);
	}
	public static function set($config, $data)
	{
		$name = $config;
		$re = self::init($config);
		self::$data[$name] = array_merge($re, $data);
	}

	public static function getMore($config)
	{
		$data = self::init($config);

		$key = implode('.', $config);

		if(!is_array($data) || !isset($data[$key])){
			self::error($key.' is not data key');
		}
		return $data[$key];
	}

	public static function getFileName($config)
	{
		self::config();
		return self::$config[$config];
	}

	private static function error($message)
	{
		Error::setMessage($message);
	}

	private static function init(&$config)
	{
		self::config();
		$config = explode('.', $config);
		if(!$config){
			self::error('config is null');
		}
		$file = array_shift($config);
		if(!isset(self::$data[$file])){
			// echo 111;
			if(!isset(self::$config[$file])){
				self::error($file.' is not config');
			}
			$data = self::requireData($file);
		}else{
			$data = self::$data[$file];
		}
		return $data;
	}

	private static function config()
	{
		if(!self::$config){
			self::$config = require CONFIG.self::$config_file;
		}
	}

	private static function requireData($file)
	{
		self::$data[$file] = require CONFIG . $file . '.php';
		return self::$data[$file];
	}
}