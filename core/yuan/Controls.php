<?php
/*
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
    use Common;

    public $dao = NULL;              //模型对象
    public $dao_name = NULL;            //模型名称
    public $controls_name = NULL;       //控制器名称
    protected $route = [];
    public $user_id = '';                //用户Id
    public $layout = 'layout.html';     //layout是布局文件
    public $handle = [];                // 验证的数据 'form'=>$form[]
    public $form = [];                  // form数组
    private $error = '';
    private $view_data = [];                 //返回到视图上面的数据
    private $type_data = [];
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
        $this->controls_name = str_replace('/', '\\',  PROJECT.'\\'.P('APP').'\\'.CONTROLS.'/'.P('URL_CONTROL'));
        $this->dao_name = str_replace(CONTROLS, DAO, $this->controls_name);
        $this->dao();
        $this->before();
        $this->handleData();
        $this->checkToken();
    }
    private function checkToken()
    {
        $data = IS_POST ? $_POST : $_GET;
        $token = $this->getSession('_token');
        if(isset($data['_token']) && $data['_token']!=$token){
            $this->errorMsg('error_token');
        }
        $this->clearSession('_token');
    }

    private function handleData()
    {
        // self::$data_array = IS_POST ? $_POST : $_GET;
        if(isset($this->handle[P('URL_MODEL')])){
            $config = $this->handleForm();
            $data = IS_POST ? $_POST : $_GET;
            Handle::run($config, $data);
        }
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
                $dao = PROJECT.'\\'.P('APP').$str.ucfirst($dao);
            } else {
                $dao = PROJECT .'\\'. $path . $str .ucfirst($dao);
            }
        }
        $dao .= ucfirst(DAO);

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
        if(!Route::getWeb()){
            WebError::success($this->view_data);
        }
        if(isset($this->view_data['redirect'])){
            $this->location($this->view_data['redirect']);
        }
        $this->view($name);
    }

    /*
        $this->chooseData(['select'=>[0=>'aa',2=>'bb'],'id_used'=>['22'=>'lalala','33'=>'iiii']]);
        $user['checkbox'] = '1,2,3,4';
        $this->defaultData($user);
        $this->nameData('wang')->form(['checkbox','id_used']);
     */

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

    public function getForm($name = '')
    {
        if(!$name){
            $name = P('URL_MODEL');
        }
        return isset($this->form[$name])?$this->form[$name]:[];
    }
    private function handleForm()
    {
        $config = $this->handle[P('URL_MODEL')];
        if(isset($config['form'])){
            $arr = $this->getForm($config['form']);
            $config = array_merge($arr,$config);
            unset($config['form']);
        }
        return $config;
    }

    public function form($input=[])
    {
        if(!is_array($input)){
            return false;
        }
        $input = array_merge($input,$this->getForm());
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
        $view_dir = P('VIEWS_DIR');

        $file = ($name === '0')?strtolower(P('URL_CONTROL')).'/'.P('URL_MODEL'):$name;

        $file = $view_dir.$file.'.html';

        if (!is_file($file)) {
            $this->errorMsg('not view file ' . $file);
        }

        $views = $this->setView();

        $route_layout = $this->route('layout');

        $layout = !$route_layout?$this->layout:$route_layout;
        $layout = ROOT.PROJECT.'/'.P('APP').'/'.LAYOUT.'/'.$layout;
        if($route_layout!==0 && is_file($layout)){
            require($layout);
        }else{
            require($file);
        }
    }

    private function setView()
    {
        $views = '\\'.PROJECT.'\\'.P('APP').'\\'.VIEWS.'\\'.'Views';

        if(!class_exists($views)){
            return null;
        }

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
    public function getValue($key)
    {
        $str = '';
        if(array_key_exists($key,$this->view_data)){
            $str = $this->view_data[$key];
        }
        return $str;
    }

    public function page($total)
    {

        $temp = $this->request();
        $page = $temp['page'];
        $pagesize = $temp['pagesize'];

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

        $this->page['first'] = $first;// 第一页
        $this->page['previous'] = $previous;// 上一页
        $this->page['next'] = $next;// 下一页
        $this->page['last'] = $last;// 最后一页

        $this->page['total'] = $total;// 总条数
        $this->page['page'] = $page;// 当前页
        $this->page['size'] = $pagesize;// 每页条数
        $this->page['count'] = $cnt;// 总页数
        return $this->page;
    }
    /**
     * [redirect 跳转界面]
     * @param  [type] $path [跳转路径]
     * @param  array  $arr  [跳转带参]
     */
    public function redirect($path, $http_arr = [])
    {
        $temp = count(explode('/', $path));
        $path = $temp>=2?$path:($temp==1?strtolower(P('URL_CONTROL')).'/'.$path:'');
        if(!$path){
            return false;
        }
        $str = '';
        $arr = Route::getWeb()?$this->view_data:[];
        $arr = $http_arr?$http_arr:$arr;
        if ($arr) {
          $str = http_build_query($arr);
          $str = '?' . $str;
        }
        $url = $path . $str;
        if(strpos($url, 'http://') === false && strpos($url, 'https://') === false){
            $str = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        }
        $str = $str .'/'. $url;

        $this->view_data['redirect'] = $str;
    }


    public function location($str)
    {
        header('location:' . $str);
        exit;
    }

    public function url($str, $data=[])
    {
        $url = $this->getUrl($str, $data);
        echo $url;
    }
    public function getUrl($str, $data=[])
    {
        $temp = explode('/', $str);
        $str = isset($temp[1])?$str:strtolower(P('URL_CONTROL')).'/'.$str;
        if($data){
            $str .= '?'.http_build_query($data);
        }
        return P('PATH').$str;
    }

    public function path()
    {
        echo P('PATH');
    }

    //控制器执行之前
    public function before()
    {

    }
    //控制器执行之后
    public function after()
    {

    }
    // 获取数组中指定元素
    public function arrayPart($all, $need)
    {
        return array_intersect_key($all, array_flip($need));
    }

}
