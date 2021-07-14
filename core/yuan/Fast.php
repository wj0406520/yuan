<?php

use core\yuan\MoreLog;
use core\yuan\Prourl;
use core\yuan\Error;
use core\yuan\Config;
use core\tool\HttpTool;
// $class = User::class
// 快速创建模型，不需要use

function M($class)
{
    $arr = explode('\\',$class);
    $re = end($arr);

    $models = str_replace('/','\\',str_replace(ROOT,'',MODELS_DIR)).$re;

    return $models::init()->models();
}

function Param($param)
{
    return Config::getMore('param.'.$param);
}

function Jump($url)
{
    header("location:".$url);
    exit;
}

// D(api\Index::class); = new \app\api\IndexDao()
function D($dao)
{
    $arr = explode('\\',$dao);
    if(count($arr)==1){
        Error::setMessage($dao.' dao不符合规则');
    }
    $dao = end($arr);
    $space = prev($arr);
    if(count($arr)==4){
        $space = prev($arr);
    }
    $dao = '\\'.PROJECT.'\\'.$space.'\\'.DAO.'\\'.$dao.ucfirst(DAO);

    return new $dao();
}
function H()
{
    static $http = '';
    if(!$http){
        $http = new HttpTool();
    }
    return $http;
}

// 快速获取日志函数，并设置文件名
function L($name)
{
    static $r = '';
    if(!$r){
        $r = new class
        {
            public function write($str) {MoreLog::w($this->name,$str); }
        };
        $r->name = $name;
    }
    return $r;
}

// 快速获取路由参数
function P($key)
{
    return Prourl::get($key);
}