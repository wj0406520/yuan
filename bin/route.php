<?php
/*
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  自动生成项目控制器，dao，html
|			 config.url app..route.route.php支持
+----------------------------------------------------------------------
*/
use bin\lib\MysqliData;

//定义文件访问
define('ACC',111);

//引入核心文件
require('../core/init.php');

$a = new MysqliData;

$a->route();

//  需要一个脚本
//  同步数据库的内容到本地（表结构）
//  同步本地的内容到数据库（表结构）
//    要求
//    简化内容
//    备份上一次操作的内容
//    还原上次操作的内容