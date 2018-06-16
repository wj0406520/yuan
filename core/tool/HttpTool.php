<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  发起网络请求
+----------------------------------------------------------------------
*/
namespace core\tool;

class HttpTool
{
    private $url = '';
    private $data = [];
    private $header = [];
    private $err = '';
    private $info = [];
    // private $cookie_file = 'cookiefile.txt';
    private $cookie_file = '';
    /**
     * [setUrl 设置请求路径]
     * @param  [string] $url  [路径]
     * @return [Http]         [本身]
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
    /**
     * [setData 设置请求数据]
     * @param  [string] $data  [数据]
     * @return [Http]         [本身]
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    /**
     * [setHeader 设置请求头]
     * @param  [string] $header  [头]
     * @return [Http]         [本身]
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }
    /**
     * [setHeader 设置cookie文件]
     * @param  [string] $file  [cookie文件名]
     * @return [Http]         [本身]
     */
    public function setCookieFile($file)
    {
        $this->cookie_file = $file;
        return $this;
    }
    /**
     * [getErr 获取错误信息]
     * @return [string]         [错误信息]
     */
    public function getErr()
    {
        return $this->err;
    }
    /**
     * [getHeader 获取响应头]
     * @return [array]         [响应头]
     */
    public function getHeader()
    {
        return $this->info;
    }
    /**
     * [getHeader 获取响应状态码]
     * @return [string]         [状态码]
     */
    public function getCode()
    {
        return $this->info['http_code'];
    }
    /**
     * [post 发出post请求]
     * @return [string]         [请求结果]
     */
    public function post()
    {
       $re = self::http(2);
       return $re;
    }
    /**
     * [postjson 发出post请求]
     * @return [string]         [请求结果]
     */
    public function postJson()
    {
        $this->data = json_encode($this->data);
        $this->header[]='Content-Type:application/json';
        $re = self::http(2);
        return $re;
    }
    /**
     * [put 发出put请求]
     * @return [string]         [请求结果]
     */
    public function put()
    {
       $re = self::http(1,'PUT');
       return $re;
    }
    /**
     * [delete 发出delete请求]
     * @return [string]         [请求结果]
     */
    public function delete()
    {
       $re = self::http(1,'DELETE');
       return $re;
    }
    /**
     * [get 发出get请求]
     * @return [string]         [请求结果]
     */
    public function get()
    {
       $re = self::http();
       return $re;
    }
    protected function http($type=0, $method='')
    {
        $url = $this->url;
        $arr = $this->data;
        $header = $this->header;
        $file = $this->cookie_file;
        $this->check($url);
        $curl = curl_init();
        switch ($type) {
            case '0':
                    $str=http_build_query($arr);
                    $url=$str?$url.'?'.$str:$url;
                break;
            case '1':
                //设置发送方式：
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                //设置发送数据
                curl_setopt($curl, CURLOPT_POSTFIELDS, $arr);
                break;
            case '2':
                //设置发送方式：
                curl_setopt($curl, CURLOPT_POST, true);
                //设置发送数据
                curl_setopt($curl, CURLOPT_POSTFIELDS, $arr);
                break;
        }
        if($header){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if($file){
            $file .= TEMP_DIR;
            //设置cookie和带参cookie
            curl_setopt($curl,CURLOPT_COOKIEFILE,$file);
            curl_setopt($curl,CURLOPT_COOKIEJAR,$file);
        }
        curl_setopt($curl, CURLOPT_URL,$url);
        //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($curl, CURLOPT_HEADER,0);
         // 获取数据返回
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //强制协议为1.0
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        //关闭ssl
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        //强制使用IPV4协议解析域名
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        //存取数据
        $file_contents = curl_exec($curl);
        $err = curl_error($curl);
        $this->info = curl_getinfo($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);
        if ($err) {
            $this->err = "cURL Error #:" . $err;
            return false;
        }
        return $file_contents;
    }
    protected function check($url)
    {
        if (!extension_loaded("curl")) {
            echo 'curl error';
            exit;
        }
        if(!$url){
            echo 'url error';
            exit;
        }
    }
}