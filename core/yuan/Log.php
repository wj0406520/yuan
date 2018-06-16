<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  日志类
// +----------------------------------------------------------------------
namespace core\yuan;

class Log
{
	private static $dir = LOG_DIR;
	private static $max_size = 10;
	private static $log_file = '';
    private static $buffer = '';
    private static $data = [
                    'error'=>['file'=>'curr.log','buffer'=>''],
                    'sql'=>['file'=>'sql.log','buffer'=>''],
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
            $hander = 'URL  '.APP.'.'.lcfirst(URL_CONTROL).'.'.URL_MODEL.PHP_EOL;
            $hander .= "Time " . date(DATE_RFC1123,TIME) . PHP_EOL;
        }
        $sql = $hander.$sql.PHP_EOL;
        self::$data['sql']['buffer'] .= $sql;
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
        $cont .= PHP_EOL;
        // 判断是否备份
        $log = self::isBak(); // 计算出日志文件的地址

        $fh = fopen($log, 'ab');
        fwrite($fh, $cont);
        fclose($fh);
    }

    // 备份日志
    private static function bak()
    {
        // 就是把原来的日志文件,改个名,存储起来
        // 改成 年-月-日.bak这种形式
        $log = self::$dir  . self::$log_file;
        $bak = self::$dir  . date('ymd') . mt_rand(10000,99999) . '.bak';
        return rename($log, $bak);
    }

    // 读取并判断日志的大小
    private static function isBak()
    {
        $log = self::$dir  . self::$log_file;

        if (!file_exists($log)) { //如果文件不存在,则创建该文件
            touch($log);    // touch在linux也有此命令,是快速的建立一个文件
            return $log;
        }

        // 要是存在,则判断大小
        // 清除缓存
        // clearstatcache(true,$log);
        $size = filesize($log);
        if($size <= 1024 * 1024 * self::$max_size) { //大于1M
            return $log;
        }

        // 走到这一行,说明>1M
        if (!self::bak()) {
            return $log;
        } else {
            touch($log);
            return $log;
        }
    }

}