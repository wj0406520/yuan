<?php

// 测试脚本

use core\yuan\CliAbstract;

//定义文件访问
define('ACC',111);


//引入核心文件
require('../../core/init.php');


class App extends CliAbstract
{
    public $sleep = 1;
    public $stop_num = 10000;
    // public $is_cycle = 1;

    public function master()
    {
        $this->runFile('t.php');
    }

    public function runFile($name)
    {
        $u = $this->getLogDir().$name.'.pid';
        if(!is_file($u)){
            $this->runName($name);
        }else{
            $pid = file_get_contents($u);
            $a = exec('ps aux | grep '.$pid ." | awk -F\" \" '{print $2}' ",$arr);
            if(!in_array($pid,$arr)){
                $this->runName($name);
                $this->addNum();
            }
        }
    }
}


$a = new App();
$a->run();
