<?php
/*
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  生成app内容
+----------------------------------------------------------------------
*/
namespace bin\lib;

class App
{

// 1.生成public下面文件夹，文件等
// 2.生成app下面文件夹，文件等
// 3.如果相关文件存在则不生成
// mkdir(ROOT.PROJECT.'/aa');
	private $app_dir = '';
	private $public_dir = '';
	private $open_dir = OPEN_DIR;
	private $project_dir = ROOT.PROJECT.'/';
	private $is_web = 0;
	public function run($url)
	{
		if(!$url){
			return false;
		}
		foreach ($url as $key => $value) {
			$this->app_dir = $value['app'];
			$this->is_web = $value['is_web'];
			$this->public_dir = $key;
			$this->publicDir();
		}
		echo "success\n";
	}
	private function createDir($path)
	{
		if(!is_dir($path)){
			mkdir($path);
		}
	}
	private function createFile($file, $data)
	{
		if(is_file($file)){
			return false;
		}
		file_put_contents($file, $data);
	}

	private function publicDir()
	{
		$this->createDir($this->open_dir.$this->public_dir);
		$this->publicIndex();
	}
	private function publicIndex()
	{
		$data = <<<EXT
<?php

//定义文件访问
define('ACC',111);

//定义是否开启sql记录 (默认为开启)
//define('IS_SQL_LOG', false);

//是否开启报错 (默认为开启)
// define('DEBUG',false);

//引入核心文件
require('../../core/init.php');

EXT;

		$file = $this->open_dir.$this->public_dir.'/index.php';
		$this->createFile($file, $data);
		$this->publicHtaccess();
	}
	private function publicHtaccess()
	{
		$data = <<<EXT
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
EXT;

		$file = $this->open_dir.$this->public_dir.'/.htaccess';
		$this->createFile($file, $data);
		$this->appDir();
	}

	private function appDir()
	{
		$this->createDir($this->project_dir.$this->app_dir);
		$this->controlsDir();
	}
	private function controlsDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.CONTROLS);
		$this->controlsFile();
	}
	private function controlsFile()
	{
		$dir = $this->app_dir;
		$data = <<<EXT
<?php

namespace app\\$dir\controls;

use core\yuan\Controls;
use core\yuan\Dao;
use core\yuan\Route;
// use app\\$dir\dao\LoginDao;

class All extends Controls
{
	/**
	 * [before 所以页面在执行action之前执行的函数，用于检测登录状态，设置项目一些配置]
	 */
	public function before()
	{
		// if(Route::getCheck() && !\$this->getSession('aa')){
  			// \$this->redirect('/login/index');
  		// }
	}
	/**
	 * [checkToken 接口中检测]
	 * @return [type] [description]
	 */
	public function checkToken()
    {
        \$arr = IS_POST ? \$_POST : \$_GET;
        \$token = isset(\$arr['token'])?\$arr['token']:'';
        if(!\$token){
	      \$this->errorMsg('error_token');
        }
	    // 检测登录 返回用户id
	    \$a = new LoginDao();
	    \$a = \$a->checkToken(\$token);

	    if (\$a) {
	      Dao::\$user_id = \$a;
	    } else {
	      \$this->errorMsg('token');
	    }
    }

}

EXT;
		$file = $this->project_dir.$this->app_dir.'/'.CONTROLS.'/All.php';
		$this->createFile($file, $data);
		$this->daoDir();
	}
	private function daoDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.DAO);
		$this->daoFile();
	}
	private function daoFile()
	{
		$dir = $this->app_dir;
		$data = <<<EXT
<?php

namespace app\\$dir\dao;

use core\yuan\Dao;

class AllDao extends Dao
{
	public function __construct()
	{

	}
}
EXT;
		$file = $this->project_dir.$this->app_dir.'/'.DAO.'/AllDao.php';
		$this->createFile($file, $data);
		if($this->is_web){
			$this->layoutDir();
		}else{
			$this->routeDir();
		}
	}
	// private function htmlDir()
	// {
	// }
	private function layoutDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.LAYOUT);
		$this->layoutFile();
	}
	private function layoutFile()
	{
		$data = <<<EXT
<!DOCTYPE html>
<html>
<head>
	<title><?php echo \$this->route('title');?></title>
</head>
<body>
	<?php include \$file;?>
</body>
</html>

EXT;
		$file = $this->project_dir.$this->app_dir.'/'.LAYOUT.'/layout.html';
		$this->createFile($file, $data);
		$this->routeDir();
	}
	private function routeDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.ROUTE);
		$this->routeFile();
	}
	private function routeFile()
	{
		$data = <<<EXT
<?php
/*
格式
控制器.模型
	title 标题
	handle 验证参数(数据为config.handle中的数据)
	is_web  是否为web为 1是web页面 0为api 默认为1
			可以通过Route::setDefaultWeb(0)设置默认值
	is_check 是否开启验证 1是验证 0为不验证 默认为1
 */
return [
	'index.index' => [
		'title'=>'首页',
		// 'handle' => ['name','card','phone','is_used'],
		// 'handle' => [],
		// 'is_web' => '0',
		'is_check'=>0
	],
];
EXT;
		$file = $this->project_dir.$this->app_dir.'/'.ROUTE.'/route.php';
		$this->createFile($file, $data);
		if($this->is_web){
			$this->viewsDir();
		}
	}
	private function viewsDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.VIEWS);
		$this->viewsFile();
	}
	private function viewsFile()
	{
		$dir = $this->app_dir;
		$data = <<<EXT
<?php

namespace app\\$dir\\views;

use core\yuan\ViewsAbstract;

class Views extends ViewsAbstract
{
	public function checkbox()
	{
		\$html = '<label class="choose"><input type="checkbox" value=":value" :choose name=":form_name" ><span>:desc</span></label>';
        return \$html;
	}
	public function radio()
	{
		\$html = '<label class="choose"><input type="radio" value=":value" :choose name=":form_name" ><span>:desc</span></label>';
        return \$html;
	}

	public function hidden()
	{
		\$html = '<input type="hidden" handle=":handle" target=":target" value=":value" name=":form_name" />';
        return \$html;
	}
	public function password()
	{
		\$html = '<input type="password" handle=":handle" target=":target" name=":form_name" placeholder=":placeholder"/>';
        return \$html;
	}
	public function text()
	{
		\$html = '<input type="text" handle=":handle" target=":target" name=":form_name" value=":value" placeholder=":placeholder"/>';
        return \$html;
	}
	public function date()
	{
		\$html = '<input handle=":handle" target=":target" type="text" value=":value" name=":form_name" class="select_time1" placeholder=":placeholder" />';
        return \$html;
	}

	public function file()
	{
		\$html = '<a class="btn_addPic" href="javascript:void(0);">
					<span><i class="icon-font">&#xe026;</i>:name</span>
                        <input class="filePrew" type="file" size="3" name=":form_name" handle=":handle" target=":target" />
                    </a>
                <span class="label badge-color0 file-name"></span>';
        return \$html;
	}
	public function textarea()
	{
		\$html = '<textarea name=":form_name" handle=":handle" target=":target" placeholder=":placeholder">:value</textarea>';
        return \$html;
	}

	public function option()
	{
		\$html = '<option value=":value" :choose>:desc</option>';
		return \$html;
	}
	public function select()
	{
		\$html = '<select name=":form_name"><option>全部</option>:option</select>';
		return \$html;
	}
	public function myextend()
	{
		\$html = '<input name=":form_name" haha=":haha" />';
		return \$html;
	}

	public function page()
	{
		\$html = '<span>总共:total 条 每页:size条 :page/:count 页</span>
                	<ul class="pagination">
	                    <li><a href=":first"><<</a></li>
	                    <li><a href=":previous"><</a></li>
	                    <li class="active"><a href="#">:page</a></li>
	                    <li><a href=":next">></a></li>
	                    <li><a href=":last">>></a></li>
	                </ul>';
		return \$html;
	}
}
EXT;
		$file = $this->project_dir.$this->app_dir.'/'.VIEWS.'/Views.php';
		$this->createFile($file, $data);
	}
}