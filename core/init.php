<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-24
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  框架初始化
|			 初始化当前的绝对路径
|			 换成正斜线是因为 win/linux都支持正斜线,而linux不支持反斜线
+----------------------------------------------------------------------
*/

// 开启session
session_start();
// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 设置debug
defined('DEBUG')       || define('DEBUG', true);
// 设置访问模式
defined('IS_CLI')      || define('IS_CLI', false);
// 设置sql存储模式
defined('IS_SQL_LOG')  || define('IS_SQL_LOG', true);

// 是否使用composer
defined('USE_COMPOSER')  || define('USE_COMPOSER', true);

// 定义访问类型
defined('IS_POST')     || IS_CLI || define('IS_POST', $_SERVER['REQUEST_METHOD'] == 'POST');

// 设置根目录
defined('ROOT')        || define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');

// 设置模型名称
defined('MODELS')      || define('MODELS', 'models');
// 设置控制器名称
defined('CONTROLS')    || define('CONTROLS', 'controls');
// 设置视图名称
defined('HTML')        || define('HTML', 'html');
// 设置视图控制名称
defined('VIEWS')       || define('VIEWS', 'views');
// 设置布局文件名称
defined('LAYOUT')      || define('LAYOUT', 'layout');
// 设置dao名称
defined('DAO')         || define('DAO', 'dao');
// 设置布局文件名称
defined('ROUTE')       || define('ROUTE', 'route');

// 设置访问后缀
defined('ACTION')      || define('ACTION', 'Action');

// 访问时间
defined('TIME')        || define('TIME', $_SERVER['REQUEST_TIME']);

// 访问全路径
defined('URL_PATH')    || IS_CLI || define('URL_PATH', $_SERVER['HTTP_HOST']);

// 配置信息文件夹
defined('CONFIG')      || define('CONFIG', ROOT . 'config/');

// 项目控制器模型等总目录
defined('PROJECT')     || define('PROJECT', 'app');
// 数据目录
defined('DATA')        || define('DATA', ROOT . 'storage/');
// 模版目录
defined('MODELS_DIR')  || define('MODELS_DIR', ROOT . 'service/models/');
// 数据目录
defined('UPLOAD_DIR')  || define('UPLOAD_DIR', DATA . 'data/');
// 日志目录
defined('LOG_DIR')     || define('LOG_DIR', DATA . 'log/');
// 缓存目录
defined('TEMP_DIR')    || define('TEMP_DIR', DATA . 'temp/');
// 备份目录
defined('BACK_DIR')    || define('BACK_DIR', DATA . 'back/');

// 核心目录
defined('CORE')    	   || define('CORE', ROOT . 'core/');
// 第三方函数目录
defined('THREE')       || define('THREE', CORE . 'three/');
// 工具目录
defined('TOOL')        || define('TOOL', CORE . 'tool/');
// 框架核心目录
defined('YUAN')        || define('YUAN', CORE . 'yuan/');

// composer目录
defined('VENDOR')      || define('VENDOR', ROOT . 'vendor/');
// 外部访问的目录
defined('OPEN_DIR')      || define('OPEN_DIR', ROOT . 'public/');

// phpinfo();exit;
// 引入入口文件
require(YUAN . 'Application.php');

// 初始化信息
core\yuan\Application::init();

if(!IS_CLI){
	// 运行
	core\yuan\Application::run();
}
