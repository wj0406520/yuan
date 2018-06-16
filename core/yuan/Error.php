<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-29
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  错误处理函数(程序报错)
|			 自己可以在上线之后依然可以捕获错误，并且不被用户知道
+----------------------------------------------------------------------
*/
namespace core\yuan;

class Error
{

	private static $debug = DEBUG;
	// private static $debug = 0;
	private static $is_cli = IS_CLI;
	private static $message = '';
	private static $file = '';
	private static $line = '';
	private static $type = '';

	private function __construct()
	{
		/*
	 	Fatal Error:致命错误（脚本终止运行）
	        E_ERROR         // 致命的运行错误，错误无法恢复，暂停执行脚本
	        E_CORE_ERROR    // PHP启动时初始化过程中的致命错误
	        E_COMPILE_ERROR // 编译时致命性错，就像由Zend脚本引擎生成了一个E_ERROR
	        E_USER_ERROR    // 自定义错误消息。像用PHP函数trigger_error（错误类型设置为：E_USER_ERROR）

	    Parse Error：编译时解析错误，语法错误（脚本终止运行）
	        E_PARSE  //编译时的语法解析错误

	    Warning Error：警告错误（仅给出提示信息，脚本不终止运行）
	        E_WARNING         // 运行时警告 (非致命错误)。
	        E_CORE_WARNING    // PHP初始化启动过程中发生的警告 (非致命错误) 。
	        E_COMPILE_WARNING // 编译警告
	        E_USER_WARNING    // 用户产生的警告信息

	    Notice Error：通知错误（仅给出通知信息，脚本不终止运行）
	        E_NOTICE      // 运行时通知。表示脚本遇到可能会表现为错误的情况.
	        E_USER_NOTICE // 用户产生的通知信息。
	    */
	}

	public static function init()
	{
		// self::debug();
		// 处理警告和通知
        set_error_handler(__NAMESPACE__.'\Error::errorHandler');
		// 处理致命错误
        set_exception_handler(__NAMESPACE__.'\Error::exceptionHandler');
        // 处理php不执行的错误
        register_shutdown_function(__NAMESPACE__.'\Error::shutdownHandler');
        // echo $a;
		// Log::write();
	}

	private static function debug()
	{
		// error_reporting(0);
		// 设置报错级别
		// if(self::$debug) {
		//     error_reporting(E_ALL);
		//     ini_set("display_errors", "On");
		// } else {
		//     error_reporting(0);
		// }
		// Log::write();
		if(self::$debug){
			self::backstrace();
		}else{
			$str = self::getString();
			// self::debug($arr);
			Log::write($str);
		}
	}

	public static function setMessage($message)
	{
		self::$message = $message;
		self::backstrace();
	}

	public static function errorHandler($type, $message, $file, $line)
	{
		self::$message = $message;
		self::$file = $file;
		self::$line = $line;
		self::$type = 'error';
		self::debug();
	}
	public static function exceptionHandler($e)
	{
		self::$message = $e->getMessage();
		self::$file = $e->getFile();
		self::$line = $e->getLine();
		self::$type = 'exception';
		self::debug();
	}
	public static function shutdownHandler()
	{
		$arr = error_get_last();
		if($arr){
			self::$message = $arr['message'];
			self::$file = $arr['file'];
			self::$line = $arr['line'];
			self::$type = 'shutdown';
			self::debug();
		}
	}

	private static function getString()
	{
		$str = '';
		$str .= "Message " . self::$message . "\n";
		$str .= "File " . self::$file . "\n";
		$str .= "Line " . self::$line . "\n";
		$str .= "type " . self::$type . "\n";
		return $str;
	}

	private static function backstrace()
	{
		$str = '';
		if(self::$is_cli){
			$str = self::getString();
        	echo $str;
			return false;
		}
        $str .= "<div style='text-align: center;'>";
        $str .= "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
        $str .= "<table style='width: 800px;'>";
        $str .= "<tr style='background-color:rgb(240,240,240);'><th style='width: 100px;'>Message</th><td>" . self::$message . "</td></tr>";
        $str .= "<tr style='background-color:rgb(240,240,240);'><th style='width: 100px;'>File</th><td>". self::$file ."</td></tr>";
        $str .= "<tr style='background-color:rgb(240,240,240);'><th style='width: 100px;'>Type</th><td>". self::$type ."</td></tr>";
        $str .= "<tr style='background-color:rgb(240,240,240);'><th style='width: 100px;'>Line</th><td style='color:red'>". self::$line ."</td></tr>";
        $str .= "</table></div>";
        echo $str;
        exit;
	}

	/*
	private static function errorName($type)
	{
		switch ($type) {
			case E_ERROR:
				$str = 'ERROR:致命的运行错误，错误无法恢复，暂停执行脚本';
				break;
			case E_CORE_ERROR:
				$str = 'ERROR:PHP启动时初始化过程中的致命错误';
				break;
			case E_COMPILE_ERROR:
				$str = 'ERROR:编译时致命性错';
				break;
			case E_USER_ERROR:
				$str = 'ERROR:自定义错误消息';
				break;
			case E_PARSE:
				$str = 'PARSE:编译时的语法解析错误';
				break;
			case E_WARNING:
				$str = 'WARNING:运行时警告';
				break;
			case E_CORE_WARNING:
				$str = 'WARNING:PHP初始化启动过程中发生的警告';
				break;
			case E_COMPILE_WARNING:
				$str = 'WARNING:编译警告';
				break;
			case E_USER_WARNING:
				$str = 'WARNING:用户产生的警告信息';
				break;
			case E_NOTICE:
				$str = 'NOTICE:运行时通知。表示脚本遇到可能会表现为错误的情况';
				break;
			case E_USER_NOTICE:
				$str = 'NOTICE:用户产生的通知信息';
				break;
			default:
				$str = 'UN:其他不知道的错误';
				break;
		}
		return $str;
	}*/

}