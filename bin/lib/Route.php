<?php

namespace bin\lib;

use core\yuan\Route as RouteData;

class Route
{
	private $app_dir = '';
	private $project_dir = ROOT.PROJECT.'/';
	private $is_web = 0;
	private $route = [];
	private $controls = '';
	private $action = '';

	public function run($url)
	{
		if(!$url){
			return false;
		}

		foreach ($url as $key => $value) {
			$this->app_dir = $value['app'];
			$this->is_web = $value['is_web'];
			$this->route = RouteData::data('',$value['app']);
			$this->controlsFile();
		}
		echo "success\n";
	}

	private function controlsFile()
	{
		if($this->is_web){
			$this->htmlDir();
		}
		foreach ($this->route as $key => $value) {
			$temp = explode('.', $key);
			if(!isset($temp[1])) continue;

			$this->controls = $temp[0];
			$this->action = $temp[1];
// 1文件是否存在
// 		不存在新增
// 2控制器是否存在
// 		获取所有控制器
// 		不存在新增
// 		存在跳过
			$file = $this->project_dir.$this->app_dir.'/'.DAO.'/'.ucfirst($this->controls).ucfirst(DAO).'.php';
			if(!is_file($file)){
				$this->daoFile($file);
			}
			if($this->is_web){
				$this->htmlViewsDir();
			}
			$file = $this->project_dir.$this->app_dir.'/'.CONTROLS.'/'.ucfirst($this->controls).'.php';
			if(!is_file($file)){
				$this->newFile($file);
			}

			$data = file_get_contents($file);
			$action = $this->action.ACTION;

			if(strstr($data, $action)) continue;

			$temp = str_split($data, strrpos($data, '}'));

			$str = PHP_EOL."    public function $action()".PHP_EOL."    {".PHP_EOL."    }".PHP_EOL."}";
			$data = $temp[0].$str;

			file_put_contents($file, $data);
		}

	}


	private function newFile($file)
	{
		$dir = $this->app_dir;
		$controls = ucfirst($this->controls);
		$action_name = $this->action;
		$action = ACTION;
		$data = <<<EXT
<?php
namespace app\\$dir\\controls;

class $controls extends All
{

	public function {$action_name}{$action}()
	{
	}
}
EXT;
		$this->createFile($file, $data);
	}

	private function daoFile($file)
	{
		$dir = $this->app_dir;
		$controls = ucfirst($this->controls);
		$action_name = $this->action;
		$d = opendir(MODELS_DIR);
		$arr = '';
		while ($f = readdir($d)) {
			if($f=='.'||$f=='..') continue;
			// echo $f;
			$temp = explode('.',$f);
			$arr .= '// use service\\models\\'.$temp[0]."\n";
		}
		$dao = DAO;
		$uc_dao = ucfirst($dao);
		$data = <<<EXT
<?php
namespace app\\$dir\\$dao;

$arr

class $controls{$uc_dao} extends AllDao
{

}
EXT;
		$this->createFile($file, $data);
	}
	private function htmlDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.HTML);
	}
	private function htmlViewsDir()
	{
		$this->createDir($this->project_dir.$this->app_dir.'/'.HTML.'/'.$this->controls);
		$this->htmlFile();
	}
	private function htmlFile()
	{
		$file = $this->project_dir.$this->app_dir.'/'.HTML.'/'.$this->controls.'/'.$this->action.'.html';
		$this->createFile($file, '');
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
}