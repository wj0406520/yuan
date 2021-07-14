<?php
/*
+----------------------------------------------------------------------
| time       2016-11-01
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  日志管理类
+----------------------------------------------------------------------
*/
namespace core\yuan;

class MoreLog
{
	private static $log_file = '';
    private static $buffer = '';
    private static $data = [
                    'error'=>['file'=>'curr','buffer'=>''],
                    'sql'=>['file'=>'sql','buffer'=>''],
                   ];

	public static function write($message)
	{
        $message .= "Time " . date(DATE_RFC1123,TIME) . PHP_EOL;
        $message .= "URL " . $_SERVER['REQUEST_URI'] . PHP_EOL;
        $message .= "POST " . json_encode($_POST) . PHP_EOL;
		self::$data['error']['buffer'] .= $message;
	}

    public static function sql($sql)
    {
        $hander = '';
        if(!self::$data['sql']['buffer'] && !IS_CLI){
            $hander = 'URL  '.P('APP').'.'.lcfirst(P('URL_CONTROL')).'.'.P('URL_MODEL').PHP_EOL;
            $hander .= "Time " . date(DATE_RFC1123,TIME) . PHP_EOL;
        }
        $sql = $hander.$sql.PHP_EOL;
        self::$data['sql']['buffer'] .= $sql;
    }

    public static function w($log_file, $msg)
    {
        if(isset(self::$data[$log_file])){
            self::$data[$log_file]['buffer'] .= $msg.PHP_EOL;
        }else{
            self::$data[$log_file]['file'] = $log_file;
            self::$data[$log_file]['buffer'] = $msg.PHP_EOL;
        }
    }

    // 写日志的
    public static function writeFile()
    {
        array_walk(self::$data,function($values){
            self::$buffer = $values['buffer'];
            self::$log_file = $values['file'];
            self::file();
        });
    }

    private static function file()
    {
        $cont = self::$buffer;
        if(!$cont) return false;

        Log::init()->setDir(self::$log_file)->write($cont);
    }

}