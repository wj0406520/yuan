<?php
/*
+----------------------------------------------------------------------
| time       2018-04-29
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  用户请求错误
+----------------------------------------------------------------------
*/
namespace core\yuan;

class WebError
{
	/**
     * [diyError 获取错误信息]
     * @param  string $type  [错误名称]
     * @return [array]       [错误数据]
     */
    private static function diyError($type)
    {
        $arr = Config::get('error.'.$type);
        return $arr;
    }

    /**
     * [getError 获取错误信息]
     * @param  [array]  $data  ['message'=>'','name'=>'']
     * @param  integer $type   [判断是不是错误信息]
     * @return [array]          [如果是错误直接输出，否则返回数组]
     */
    public static function getError($data, $func = NULL)
    {

        $re = $data;
    	if(is_string($data)){
    		$re['message'] = $data;
    		$re['name'] = '';
    	}

        $error = self::diyError($re['message']);
        $arr['code'] = $error[0];

        $temp[] = $error[1];
        $temp = array_merge($temp,explode(',',$re['name']));
        $arr['msg'] = call_user_func_array('sprintf',$temp);

        if(isset($_SERVER['REQUEST_URI']) && !IS_CLI){
            $data = IS_POST ? $_POST : $_GET;
            $data += [
                'host_url'=>$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                'is_post'=>IS_POST
            ];
            $arr['data'] = $data;
        }
        if($func){
            $func($arr['msg']);
        }

        self::renderForAjax($arr);
    }

    // ['redirect'=>,...]
    public static function success($data)
    {
        $redirect = '';
        if(isset($data['redirect'])){
            $redirect = $data['redirect'];
            unset($data['redirect']);
        }
        $error = self::diyError('success');
        $arr['code'] = $error[0];
        $arr['msg'] = $error[1];
        $arr['redirect'] = $redirect;
        $arr['data'] = $data;

        self::renderForAjax($arr);
    }

    /**
     * [renderForAjax 输出json数据]
     * @param  [array] $arr [输出的数据]
     */
    private static function renderForAjax($arr)
    {
        // header('Access-Control-Allow-Origin:*');
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        exit;
    }
}