<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  根据ip获取地址信息
+----------------------------------------------------------------------
*/
namespace core\tool;

class Ip
{

    public static function getAddress($ip)
    {
        $url = 'http://ip.taobao.com/service/getIpInfo.php';
        $arr =[
            'ip'=>$ip
        ];
        $re = HttpTool::get($url,$arr);

        $arr = json_decode($re,true);

        if($arr['code']==0){
            return $arr['data'];
        }
        return false;
    }
}