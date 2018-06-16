<?php
/*
+----------------------------------------------------------------------
| author     王杰
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
	private $error = '';
    public static $user_id = 0;


    public function password($password)
    {
        return hash('sha256',$password);
        // 60个字符串
        // return password_hash($password, PASSWORD_DEFAULT);
    }
    // public function checkPassword($password, $hash)
    // {
        // return password_verify($password, $hash);
    // }

    public function errorName($name)
    {
        $this->error = $name;
        return $this;
    }
    /**
     * [errorMsg 输出错误信息]
     * @param  string $data [错误带的参数]
     */
    public function errorMsg($data = '')
    {
        $error = [
            'message'=>$data,
            'name'=>$this->error
        ];
        WebError::getError($error);
    }

}