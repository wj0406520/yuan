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
    public $stop_num = 100;
    // public $is_cycle = 1;

    public function master()
    {
        $this->addNum();
    }
}


$a = new App();
$a->run();