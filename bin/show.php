<?php
/*
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  展示数据 -t 加表名 -i 加id -d 查看表结构 -dm查看所有表
+----------------------------------------------------------------------
*/
use bin\lib\MysqliData;

//定义文件访问
define('ACC',111);

//引入核心文件
require('../core/init.php');

$a = new MysqliData;

$a->show();
