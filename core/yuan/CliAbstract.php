<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  命令行 接口
+----------------------------------------------------------------------
*/
namespace core\yuan;

class CliAbstract
{
    // 睡眠时间（秒）
    public $sleep = 1;

    // 循环到一定次数，重启
    public $stop_num = 100;

    // 是否重启
    public $is_cycle = 1;

    // 当前已经循环了多少次
    private $num = 0;

    // 文件名称
    private $name = '';

    // 默认bin目录
    private $bin_dir = '';
    // log目录
    private $log_dir = '';


    public function __construct()
    {
        $temp = explode('/',$_SERVER['SCRIPT_NAME']);
        $name = end($temp);
        file_put_contents(LOG_DIR.$name.'.pid', getmypid());
        $this->bin_dir = ROOT.'service/bin/';
        $this->log_dir = LOG_DIR;
        $this->name = $name;

    }

    public function addNum()
    {
        $this->num++;
    }
    public function getLogDir()
    {
        return $this->log_dir;
    }

    public function run()
    {
        while (true) {

            $this->master();

            if($this->num>=$this->stop_num){
                echo time().'---'.getmypid()."----end\n";
                if($this->is_cycle){
                    $this->runName($this->name);
                }
                exit;
            }
            usleep(1000000*$this->sleep);
        }
    }

    public function master()
    {
    }

    public function runName($name)
    {
        file_put_contents($this->log_dir.'.start.log',$name.'---'.time()."\n",FILE_APPEND);
        // exec('ps aux | grep '.$name.'.php' ." | awk -F\" \" '{print $2}' | xargs kill -9 ");

        $cmd = 'php '.$this->bin_dir . $name;
        pclose(popen($cmd.' >> '.$this->log_dir.'.'.$name.'.log &', 'r'));
    }
}