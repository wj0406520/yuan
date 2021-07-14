<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  controls 和 dao 的公共配置
+----------------------------------------------------------------------
*/
namespace core\yuan;

trait Common {
    private $error_func = NULL;

    public function configParam($a)
    {
        return Config::getMore('param.'.$a);
    }

    public function orderNo($type = 1)
    {
        $t = $type . TIME . mt_rand(11111, 99999);
        return $t.$this->getVerifyBit($t);
    }
    // $idcard_base = substr($order_no,0,-1);
    // if(substr($order_no,-1,1) != $this->dao->getVerifyBit($idcard_base)){
    //     echo "订单错误";
    //     exit;
    // }
    public function getVerifyBit($idcard_base)
    {
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', '9', '8', '7', '6', '5', '4','3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++)
        {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 10;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    public function password($password)
    {
        return hash('sha256',$password);
        // 60个字符串
        // return password_hash($password, PASSWORD_DEFAULT);
    }
    public function getType($name)
    {
        $name = 'type.'.$name;
        return Config::getMore($name);
    }

    public function short($str,$left_num = 2, $right_num = 4)
    {
        $s = $right_num?mb_substr($str,$right_num*-1):'';
        return mb_substr($str,0,$left_num).'**'.$s;
    }
    public function randString($length)
    {
       $str = '';
       $strPol = "ABCDEFGHJKLMNPQRSTUVWXY3456789abcdefghijkmnpqrstuvwxy";
       $max = strlen($strPol)-1;
       for($i = 0; $i < $length; $i++){
        $str .= $strPol[rand(0, $max)];
       }
       return $str;
    }

    /*
        $this->changeName(['id'=>'haha']);
        $this->setRequest(['xixi'=>11]);
        $a = $this->request();
     */
    public function changeName($arr)
    {
        Handle::changeName($arr);
        return $this;
    }

    public function request($key='')
    {
        return Handle::request($key);
    }
    public function setRequest($arr)
    {
        Handle::setRequest($arr);
    }
    public function handle($config, $data)
    {
        Handle::run($config, $data);
    }

    public function type($type_name, $val)
    {
        $type = Config::getMore('type.'.$type_name);
        echo $type[$val];
    }
    public function errorName($name)
    {
        $this->error = $name;
        return $this;
    }
    public function errorFunc($func)
    {
        $this->error_func = $func;
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
        WebError::getError($error,$this->error_func);
    }

    public function error($message)
    {
        Error::setMessage($message);
    }

    public function route($key)
    {
        return Route::data($key);
    }

    public function getSession($key = '')
    {
        $name = $this->gHost().P('APP');
        if($key){
            $re = isset($_SESSION[$name][$key])?$_SESSION[$name][$key]:'';
        }else{
            $re = isset($_SESSION[$name])?$_SESSION[$name]:[];
        }
        return $re;
    }
    public function setSession($arr, $k = '')
    {
        $name = $this->gHost().P('APP');
        if($k){
            $_SESSION[$name][$arr] = $k;
            return true;
        }
        array_walk($arr, function($value,$key) use($name){
            $_SESSION[$name][$key] = $value;
        });
    }
    public function clearSession($key = '')
    {
        $name = $this->gHost().P('APP');
        if($key){
            unset($_SESSION[$name][$key]);
        }else{
            unset($_SESSION[$name]);
        }
    }
    private function gHost()
    {
        return isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
    }

}