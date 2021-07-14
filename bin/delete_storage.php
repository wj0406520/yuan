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

system('rm -rf '.UPLOAD_DIR);
system('rm -rf '.LOG_DIR);
system('rm -rf '.TEMP_DIR);
system('rm -rf '.BACK_DIR);

system('mkdir '.UPLOAD_DIR);
system('mkdir '.LOG_DIR);
system('mkdir '.TEMP_DIR);
system('mkdir '.BACK_DIR);

