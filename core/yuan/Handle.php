<?php
/*
+----------------------------------------------------------------------
| time       2018-04-29
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  请求数据处理
+----------------------------------------------------------------------
*/
namespace core\yuan;

use core\tool\Card;

class Handle
{

  	private static $name = 'handle';
  	private static $check = [];
    private static $data_array = [];         //用户传的数据
    private static $error = ['key'=>'','name'=>'','message'=>''];
    private static $handle_array = [];
    private static $config = [];
    private static $change_name = [];
    private static $check_data = [];

  	private static function init()
  	{
        // self::$config = Config::get(self::$name);
        self::$config = Config::get('form');
        // self::$config = Config::get('form');
        // print_r(self::$config);
        // exit;
    		$d = self::$check_data;
    		$check = [];
        if(!$d){
          return false;
        }
    		array_walk($d,function($value) use (&$check){
    			// echo $value;
          $temp = self::config($value);
          if(isset($temp['sql_type'])){
            $temp['verificate'] = ['type','error_type',$temp['sql_type']];
          }

          if(!isset($temp['verificate'])){
            self::error($value.' no hava form verificate');
          }
          if(!isset($temp['name'])){
            self::error($value.' no hava form name');
          }
          $arr = [];
          $arr['name'] = $temp['name'];
          $arr['handle'] = $temp['verificate'];
          $check[$value] = $arr;
    			// $check[$value] = self::config($value);
    		});
    		self::$check = $check;
    		// self::$method = $d['method']!='api';
    		self::handle();
    		// print_r(self::$handle_array);
    		// echo 2;
    		return self::$handle_array;
  	}

    private static function config($value)
    {
        $str = strtolower(P('URL_CONTROL').'.'.P('URL_MODEL'));
        $str .= '.'.$value;
        $config = self::$config;
        $data = [];
        if(!array_key_exists($str,$config)){
            $str = $value;
        }
        if(!array_key_exists($str,$config)){
            self::error('route no handle parms '.$str);
        }

        return $config[$str];
    }
    private static function setConfig($arr)
    {
        self::$handle_array = [];
        self::$change_name = [];
        self::$check_data = $arr;
    }
    private static function setData($arr)
    {
        self::$data_array = $arr;
    }

    public static function changeName($arr)
    {
        self::$change_name = $arr;
    }

    private static function cname(&$arr)
    {
        if(!self::$change_name){
            return false;
        }
        foreach (self::$change_name as $key => $value) {
            if(isset($arr[$key])){
                $arr[$value] = $arr[$key];
                unset($arr[$key]);
            }
        }
    }

    public static function request($key='')
    {
        $re = self::$handle_array;

        if(!$key){
            self::cname($re);
            return $re;
        }
        if(is_array($key)){
            $key = array_flip($key);
            $re = array_intersect_key($re, $key);
            self::cname($re);
        }else{
            $re = isset($re[$key])?$re[$key]:'';
        }
        return $re;
    }

    public static function setRequest($arr)
    {
        self::$handle_array = array_merge(self::$handle_array, $arr);
    }

    /*
        $config = ['id','password'];
        $data = ['id'=>'1','password'=>2111111];
        用$config的数组，验证$data数据，需要在form.php中预先定义$config
     */

  	public static function run($config, $data)
  	{
          self::setConfig($config);
          self::setData($data);
    		// if(!self::$handle_array){
    			self::init();
    		// }
  	}
      // $arr = self::handle([
      //     'password'=>['length','password','6,16'],
      //     'phone'=>['phone','phone'],
      //     'name'=>['search','true',''],
      //     'sex'=>['search','false',''],
      //     'age'=>['fill','int',8],
      //     'time'=>['fill','time'],
      //     'double'=>['fill','double',8.88],
      //     'string'=>['fill','string','asdfas'],
      //     'id'=>['arr','int'],
      //     'im'=>['arr','string'],
      // ]);

    /**
     * [handle 数据处理]
     * @param  [array] $array [多层处理]
     * @return [array]        [处理结果]
     */
    public static function handle()
    {
    	  $array = self::$check;
        // var_dump($array);
        if (empty($array)) {
            return true;
        }

        // self::$data_array = IS_POST ? $_POST : $_GET;

        foreach ($array as $key => $value) {
            self::$error['key'] = $key;
            self::$error['name'] = $value['name'];
            $value = $value[self::$name];
            if(is_string($value)){
              $value = self::diyHandle($value);
            }
  	        switch ($value[0]) {
  	            case 'search':
  	              self::searchData($key,$value[1],$value[2]);
  	              break;
  	            case 'fill':
  	              $v=isset($value[2])?$value[2]:'';
  	              self::fillData($key,$value[1],$v);
  	              break;
  	            case 'arr':
  	              self::arrData($key,$value[1]);
  	              break;
                case 'in':
                    $v=isset($value[2])?$value[2]:'';
                    self::typeData($key,$value[1],$v,1);
                  break;
                case 'type':
                    $v=isset($value[2])?$value[2]:'';
                    self::typeData($key,$value[1],$v);
                  break;

  	            default:
  	              $v=isset($value[2])?$value[2]:'';
  	              self::valData($key,$value[0],$value[1],$v);
  	              break;
  	        }
        }
        // $arr = self::$handle_array;
        // self::$handle_array=[];
        // return $arr;
    }

    /**
     * [diyHandle 优化handle]
     * @param  [type] $v [handle名称]
     * @return [type]    [handle内容]
     */
    private static function diyHandle($v)
    {
        $arr = [
          'length612'=>['length','length','6,12'],
          'fills'=>['fill','string',''],
          'filld'=>['fill','double',0.00],
          'fill'=>['fill','int','0'],
          'page'=>['fill','int','1'],
          'pagesize'=>['fill','int','30'],
          'phone'=>['phone','error_phone'],
          'search'=>['search',true,''],
          'file'=>['file','error_service',''],
          'email'=>['email','error'],
          'card'=>['card','error']
        ];
        $key = array_keys($arr);
        if(!in_array($v,$key)){
           // self::errorMsg('handleError');
        }
        return $arr[$v];
    }


    private static function typeData($name,$error,$type,$t = 0)
    {
        $arr = self::$data_array;
        $a = isset($arr[$name]) ? $arr[$name] : '';

        if($t==0){
            $type = Config::getMore('type.'.$type);
            $b = !array_key_exists($a,$type);
        }else{
            $type = explode(',',$type);
            $b = !in_array($a,$type);
        }

        if ($a=='' || $b) {
            self::$error['message'] = $error;
            self::errorMsg();
        }

        self::$handle_array[$name]=$a;
    }
    /**
     * [valData 自动验证数据]
     * @param  [string] $name  [名称]
     * @param  [string] $rule  [规则]
     * @param  [string] $error [错误名称]
     * @param  [string] $parm  [参数]
     */
    private static function valData($name,$rule,$error,$parm)
    {

        $arr = self::$data_array;

        $a = isset($arr[$name]) ? $arr[$name] : null;

        if ($a==null || !self::contrast($a, $rule, $parm)) {
            self::$error['message'] = $error;
            self::errorMsg();
        }

        self::$handle_array[$name] = $a;

    }

    /**
     * [arrData 数组数据]
     * @param  [string] $name [参数]
     * @param  [string] $type [类型]
     */
    private static function arrData($name,$type)
    {

        $check = ($type=='int') ? 'is_numeric' : 'is_string';

        $data = isset(self::$data_array[$name]) ? self::$data_array[$name] : '';
        if (is_array($data)) {
          foreach ($data as $value) {
            if (!$check($value)) {
              self::errorMsg('paramError');
            }
          }
        }else{
          if (!$check($data)) {
              self::errorMsg('paramError');
          }
        }
        self::$handle_array[$name]=$data;
    }


    /**
     * [fillData 填充fill数据]
     * @param  [string] $name [参数]
     * @param  [string] $type [类型]
     * @param  [string] $val  [填充数据]
     */
    private static function fillData($name,$type,$val)
    {

        $arr = self::$data_array;
        $temp = isset($arr[$name])?$arr[$name]:'';
        $temp = trim($temp);
        $a = '';

        switch ($type) {
          case 'int':
            $a = $temp && intval($temp) ? intval($temp) : $val;
            break;
          case 'double':
            $a = $temp && floatval($arr[$name]) ? floatval($arr[$name]) : $val;
            break;
          case 'string':
            $a = $temp ? $temp : $val;
            break;
          case 'time':
            $a = $temp && strtotime($temp) ? strtotime($temp) : '';
            break;

          default:
            # code...
            break;
        }

        self::$handle_array[$name]=$a;
    }

    /**
     * [searchData 检索search数据]
     * @param  [string] $name [参数]
     * @param  [string] $exit [true存在 false不存在]
     * @param  [string] $val  [检测数据]
     */
    private static function searchData($name,$exit,$val)
    {

        $arr = self::$data_array;

        $a = isset($arr[$name]) ? (is_string($arr[$name]) ? trim($arr[$name]) : $arr[$name]) : '';

        if (in_array($a, explode(',', $val)) || $a == ''){
          if ($exit=='false') {
            return true;
          }
        }

        self::$handle_array[$name] = $a;
    }


    /**
     * [contrast 匹配数据]
     * @param  [type] $value [匹配内容]
     * @param  string $rule  [匹配规则]
     * @param  string $parm  [匹配带参]
     * @return [boolen]      [匹配结果]
     */
    private static function contrast($value, $rule = '', $parm = '')
    {
        switch ($rule) {
            case 'require':
                return !empty($value);
            case 'number':
                return is_numeric($value);
            case 'time':
                return strlen($value) >= 4 && strtotime($value);
            case 'in':
                if (!$parm) {
                  self::error('IN lose parm');
                }
                $temp = explode(',', $parm);
                return in_array($value, $temp);
            case 'between':
            	$parm = explode(',', $parm);
                if (count($parm)!=2) {
                  self::error('BETWEEN lose parm');
                }
                list($min,$max) = $parm;
                self::$error['name'] .= ','.$min."，".$max;
                return $value >= $min && $value <= $max;
            case 'length':
            	$parm = explode(',', $parm);
                if (count($parm)!=2) {
                  self::error('LENGTH lose parm');
                }
                list($min,$max) = $parm;
                $len = mb_strlen($value, "utf-8");
                self::$error['name'] .= ','.$min."，".$max;
                return $len >= $min && $len <= $max;
            case 'phone':
                return preg_match("/^1[3456789]{1}\d{9}$/", $value);
            case 'ip':
                return preg_match("/\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b/", $value);
            case 'card':
              return Card::checkIdCard($value);
              break;
            case 'email':
                return (filter_var($value,FILTER_VALIDATE_EMAIL) !== false);
              break;
            case 'file':
                if($parm==$value){
                  return true;
                }
                return is_file(DATA.$value);
              break;
            default:
                return false;
        }
    }

    private static function error($message)
    {
        Error::setMessage($message);
    }

    /**
     * [errorMsg 输出错误信息]
     * @param  string $message [错误带的参数]
     */
    private static function errorMsg($message = '')
    {
      	if($message){
          	self::$error['message'] = $message;
      	}
        WebError::getError(self::$error);
    }


}