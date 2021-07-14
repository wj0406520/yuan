<?php
/*
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
setcookie('PHPSESSID', session_id(), time()+1800, '/', null, null, true);
// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 设置debug
defined('DEBUG')       || define('DEBUG', true);

// 设置sql存储模式
defined('IS_SQL_LOG')  || define('IS_SQL_LOG', true);

// 是否使用composer
defined('USE_COMPOSER')  || define('USE_COMPOSER', true);

// 设置访问模式
define('IS_CLI', php_sapi_name()=='cli');

// 定义访问类型
IS_CLI || define('IS_POST', $_SERVER['REQUEST_METHOD'] == 'POST');

// 设置根目录
define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');

// 设置模型名称
define('MODELS', 'models');
// 设置控制器名称
define('CONTROLS', 'controls');
// 设置视图名称
define('HTML', 'html');
// 设置视图控制名称
define('VIEWS', 'views');
// 设置布局文件名称
define('LAYOUT', 'layout');
// 设置dao名称
define('DAO', 'dao');
// 设置布局文件名称
define('ROUTE', 'route');

// 设置访问后缀
define('ACTION', 'Action');

// 访问时间
define('TIME', $_SERVER['REQUEST_TIME']);

// 访问全路径
IS_CLI ? define('URL_PATH', '') : define('URL_PATH', $_SERVER['HTTP_HOST']);

// 配置信息文件夹
define('CONFIG', ROOT . 'config/');

// 项目控制器模型等总目录
define('PROJECT', 'app');
// 数据目录
define('DATA', ROOT . 'storage/');
// 模版目录
define('MODELS_DIR', ROOT . 'service/models/');
// 数据目录
define('UPLOAD_DIR', DATA . 'data/');
// 日志目录
define('LOG_DIR', DATA . 'log/');
// 缓存目录
define('TEMP_DIR', DATA . 'temp/');
// 备份目录
define('BACK_DIR', DATA . 'back/');

// 核心目录
define('CORE', ROOT . 'core/');
// 工具目录
define('TOOL', CORE . 'tool/');
// 框架核心目录
define('YUAN', CORE . 'yuan/');

// composer目录
define('VENDOR', ROOT . 'vendor/');
// 外部访问的目录
define('OPEN_DIR', ROOT . 'public/');

// phpinfo();exit;
// 引入入口文件
require(YUAN . 'Application.php');
// 引入快速使用函数
require(YUAN . 'Fast.php');


// 初始化信息
core\yuan\Application::init();

if(!IS_CLI){
	// 运行
	core\yuan\Application::run();
}
