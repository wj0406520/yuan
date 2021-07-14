<?php
/*
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
	private static $env = [];
	private static $env_file = '.env';
	private static $re = null;

	private function __construct()
	{
	}

	public static function get($config)
	{
		$data = self::init($config);
		// var_dump($data);exit;
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

		if(isset(self::$data[$file])){
			return self::$data[$file];
		}
		if(!isset(self::$config[$file])){
			self::error($file.' is not config');
		}
		return self::requireData($file);
	}

	private static function config()
	{
		if(!self::$env){
			self::$env = parse_ini_file(ROOT.self::$env_file,true);
			self::$data += self::$env;
		}
		if(!self::$config){
			self::$config = self::$env['config'];
		}
	}

	private static function requireData($file)
	{
		self::$data[$file] = require CONFIG . $file . '.php';
		return self::$data[$file];
	}
}