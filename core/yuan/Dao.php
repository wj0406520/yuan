<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  中间层 实例
+----------------------------------------------------------------------
*/
namespace core\yuan;

class Dao
{
    use Common;
	private $error = '';
    public static $user_id = 0;
    private $change_name = [];


    public function getUser($key = null)
    {
        return $key?Models::$user[$key]:Models::$user;
    }

}