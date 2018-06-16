<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  控制器 实例
+----------------------------------------------------------------------
*/
namespace core\yuan;


class Controls
{

    public $dao = NULL;              //模型对象
    public $dao_name = NULL;            //模型名称
    public $controls_name = NULL;       //控制器名称
    protected $route = [];
    protected $handle = [];        //返回数据
    public $user_id = '';                //用户Id
    public $layout = 'layout.html';     //layout是布局文件
    private $error = '';
    private $view_data = [];                 //返回到视图上面的数据
    private $form_data = [];
    private $temp_data = [];
    private $page = [];
    // public $web = 0;                    //web为1是web页面
    // public $check = 1;                  //登录限制
    // public $error = '';                 //错误信息

    /**
     * 1.控制器实例化的时候，检测模版的未知
     * 2.加载模型
     * 3.执行before方法
     */
    public function __construct()
    {
        // $class = Handle::class;
        // $a = new $class;
        // print_r($a->data());
        // exit;
        $this->route = Route::data();
        $this->controls_name = str_replace('/', '\\',  PROJECT.'\\'.APP.'\\'.CONTROLS.'/'.URL_CONTROL);
        $this->dao_name=str_replace(CONTROLS, DAO, $this->controls_name);
        $this->dao();
        $this->before();
        $this->handle = Handle::data();
    }

    /**
     * [__destruct 程序结束后运行after方法]
     */
   public function __destruct()
   {
      $this->after();
   }

    /**
     * [dao 加载模型]
     * @param  string $dao [模型名称]
     * @param  string $path   [模型空间]
     */
    public function dao($dao = '0', $path = '0')
    {

        $str='\\'.DAO.'\\';

        if ($dao === '0') {
            $dao = $this->dao_name;
        } else {
            if ($path === '0') {
                $dao = PROJECT.'\\'.APP.$str.$dao;
            } else {
                $dao = PROJECT .'\\'. $path . $str .$dao;
            }
        }
        $dao .= ucfirst(DAO);

        // print_r($models);exit;
        $file = str_replace('\\', '/',ROOT.$dao.'.php');

        if (is_file($file)) {
          $this->dao = new $dao();
        } else {
          // $this->dao = new Models();
        }
        // $this->dao->web = $this->web;
        return $this->dao;
    }

    /**
     * [display 实例化界面]
     * @param  string $name [界面名称]
     * @param  array  $arr  [参数]
     *
     * 1.载入数据
     * 2.判断界面位置
     * 3.判断布局文件位置
     * 4.加载布局文件
     * 5.从布局文件加载内容界面
     */
    public function display($name = '0')
    {
        // $conf = Conf::getIns();
        // define('IMG_URL', $conf->img_url);
      // print_r(Route::getWeb());exit;
        if(Route::getWeb()){
            if(isset($this->view_data['redirect'])){
                $this->location($this->view_data['redirect']);
            }
            $this->view($name);
        }else{
            WebError::success($this->view_data);
        }
    }

    public function chooseData($choose_data)
    {
        $this->temp_data['choose_data'] = $choose_data;
        return $this;
    }
    public function defaultData($default_data)
    {
        $this->temp_data['default_data'] = $default_data;
        return $this;
    }
    public function nameData($name_data)
    {
        $this->form_data['name_data'] = $name_data;
        return $this;
    }

    public function form($input)
    {
        if(!is_array($input)){
            return false;
        }
        $temp_data = [];
        $choose_data = array_key_exists('choose_data',$this->temp_data)?$this->temp_data['choose_data']:[];
        $name_data = array_key_exists('name_data',$this->temp_data)?$this->temp_data['name_data']:'';
        $data = array_key_exists('default_data',$this->temp_data)?$this->temp_data['default_data']:[];
        foreach($input as $val){
            $temp_data[$val]['value'] = array_key_exists($val, $data)?$data[$val]:'';
            $temp_data[$val]['data'] = array_key_exists($val, $choose_data)?$choose_data[$val]:'';
        }
        $this->temp_data = $temp_data;
        $this->mergeData();
        return $this;
    }
    private function mergeData()
    {
        if(array_key_exists('name_data',$this->form_data)){
            $this->form_data[$this->form_data['name_data']] = $this->temp_data;
        }else{
            $this->form_data[] = $this->temp_data;
        }
    }

    private function view($name)
    {
        // instanceof
        // class_exists();
        $arr = $this->view_data;
        if ($arr) {
          foreach ($arr as $key => $value) {
            $$key = $value;
          }
        }
        $view_dir = VIEWS_DIR;
        if ($name === '0') {
            $file = $view_dir . strtolower(URL_CONTROL).'/'.URL_MODEL;
        } else {
            $file = $view_dir . $name;
        }
        $file .='.html';

        if (!is_file($file)) {
            $this->errorMsg('not view file ' . $file);
        }
        $layout = $view_dir.LAYOUT.'/'.$this->layout;

        $views = $this->setView();

        if (!is_file($layout)) {
            // debug('not view layout '. $layout);
            require($file);
        }else{
            require($layout);
        }
    }

    private function setView()
    {
        $views = '\\'.PROJECT.'\\'.APP.'\\'.VIEWS.'\\'.'Views';

        if(class_exists($views)){
            $views = new $views;
            if(!$views instanceof ViewsAbstract){
                $this->errorMsg('views not instanceof ViewsAbstract');
            }
            if(array_key_exists('name_data',$this->form_data)){
                unset($this->form_data['name_data']);
            }
            $views->data($this->form_data);
            if($this->page){
                $views->setPage($this->page);
            }
            $views->run();
            return $views;
        }

    }

    public function setValue($arr)
    {
        $data = $this->view_data;
        if($data){
            $data = array_merge($data,$arr);
        }else{
            $data = $arr;
        }
        $this->view_data = $data;
        return $this;
    }

    public function page($total, $page = 1, $pagesize = 10)
    {

        $cnt   = ceil($total/$pagesize);  // 得到总页数
        $page  = $page > $cnt ? $cnt : $page;

        $uri   = $_SERVER['REQUEST_URI'];

        $parse = parse_url($uri);

        $param = [];

        if(isset($parse['query'])) {
            parse_str($parse['query'],$param);
        }

        // 不管$param数组里,有没有page单元,都unset一下,确保没有page单元,
        // 即保存除page之外的所有单元
        unset($param['page']);

        $url = $parse['path'] . '?';
        if(!empty($param)) {
            $param = http_build_query($param);
            $url = $url . $param . '&';
        }
        $str = $url.'page=';
        $left = $page-1;
        $right = $page+1;
        $first = $str.'1';
        $previous = ($left < 1)?$first:$str.$left;
        $next = ($right > $cnt)?$str.$cnt:$str.$right;
        $last = $str.$cnt;

        // 第一页
        $this->page['first'] = $first;
        // 上一页
        $this->page['previous'] = $previous;
        // 下一页
        $this->page['next'] = $next;
        // 最后一页
        $this->page['last'] = $last;

        // 总条数
        $this->page['total'] = $total;
        // 当前页
        $this->page['page'] = $page;
        // 每页条数
        $this->page['size'] = $pagesize;
        // 总页数
        $this->page['count'] = $cnt;
    }
    /**
     * [redirect 跳转界面]
     * @param  [type] $path [跳转路径]
     * @param  array  $arr  [跳转带参]
     */
    public function redirect($path)
    {
        $str = '';
        $arr = Route::getWeb()?$this->view_data:[];
        if ($arr) {
          $str = http_build_query($arr);
          $str = '?' . $str;
        }
        $url = $path . $str;
        if(strpos($url, 'http://') === false && strpos($url, 'https://') === false){
            $str = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        }
        $str = $str . $url;
        // print_r($path . $str);exit;
        $this->view_data['redirect'] = $str;
    }

    public function location($str)
    {
        header('location:' . $str);
    }



    //控制器执行之前
    public function before()
    {

    }
    //控制器执行之后
    public function after()
    {

    }

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

    public function route($key)
    {
        return Route::data($key);
    }

    public function getSession($key)
    {
        $key = APP.'.'.$key;
        return isset($_SESSION[$key])?$_SESSION[$key]:'';
    }
    public function setSession($arr)
    {
        array_walk($arr, function($value,$key){
            $key = APP.'.'.$key;
            $_SESSION[$key] = $value;
        });
    }

}
