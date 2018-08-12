<?php
namespace bin\lib;

use \core\yuan\Config;

class MysqliData
{

	public $database = null;
	public $table = '';

	public function __construct()
	{
		$config = Config::get('database');
		$this->table = $config['db'];
		Config::set('database',['db'=>'']);
		$this->database = new Database($config);
	}

	public function renew()
	{
		$this->database->renew();
	}
	public function back()
	{
		$table = $this->table;
		$this->database->back($table);
	}

	public function service()
	{
		// echo 11;
		$table = $this->table;
		$file = Config::getFileName('sql');
		$file = CONFIG.$file;
		$this->database->write($file)->service($table);

	}

	public function local()
	{
		// echo 7.1/0.0;
		// echo 111;
		$local = Config::get('sql');

		$this->database->local($local);
		// echo $sql['schema'];
	}

	public function app()
	{
		$url = Config::get('url');
		$temp = new App();
		$temp->run($url);
		// echo ROOT.PROJECT;
		// exit;
	}
	public function route()
	{
		$url = Config::get('url');
		$temp = new Route();
		$temp->run($url);
	}


	public function models()
	{
		$sql = Config::get('sql');
		$link = Config::get('link');

		if(!$sql){
			echo "sql error\n";
			exit;
		}
		if(!$link){
			echo "link error\n";
			exit;
		}

		$dir = opendir(MODELS_DIR);
		$file = [];
		while ($f = readdir($dir)) {
			if($f=='.'||$f=='..'){
				continue;
			}
			$arr = explode('.', $f);
			if(!isset($arr[1])||$arr[1]!='php'){
				unlink(MODELS_DIR.$f);
				continue;
			}
			$file[] = $arr[0];
		}
		closedir($dir);

		foreach ($sql['table'] as $key => $value) {
			$temp = explode('_', $key);
			$big_name = '';
			foreach ($temp as $v) {
				$big_name .= ucfirst($v);
			}

			if(($num = array_search($big_name, $file))!==false){
				unset($file[$num]);
			}
			$this->writeFile($key, $value);
			echo ".";
		}

		foreach ($file as $key => $value) {
			echo ".";
			unlink(MODELS_DIR.$value.'.php');
		}
		echo "\nsuccess\n";
	}

	private function writeFile($name, $data)
	{
		$link = Config::get('link');
		$temp = [];
		if(isset($link[$name])){
			foreach ($link[$name] as $key => $value) {
				$models = ucfirst($value['table']);
				if(isset($value['table_key'])){
					$temp[] = "{$models}::class=>['join'=>'{$value['column']}', 'link'=>'{$value['table_key']}']";
				}else{
					$temp[] = "{$models}::class=>'{$value['column']}'";
				}
			}
		}
		$link_table = implode(",\n        ", $temp);
		$link_table = $link_table?"\n        ".$link_table."\n    ":"";
		$temp = explode('_', $name);
		$big_name = '';
		foreach ($temp as $value) {
			$big_name .= ucfirst($value);
		}
		$file = MODELS_DIR.$big_name.'.php';

		$temp = [];
		foreach ($data['column'] as $key => $value) {
			$temp[] = "'{$key}'=>'{$value['comment']}'";
		}
		$column = implode(",\n        ", $temp);
		$temp = [];
		foreach ($data['index'] as $key => $value) {
			$temp[] = "'{$key}'=>'{$value['column']}'";
		}
		$index = implode(",\n        ", $temp);
		$index = $index?"\n        ".$index."\n    ":"";
		$str = <<<EXT
<?php

namespace service\models;
use core\yuan\Models;
class {$big_name} extends Models
{
    public \$table = '{$name}';
    public \$comment = '{$data['comment']}';
    public \$column = [
        {$column}
    ];
    public \$index = [{$index}];
    protected \$link_table = [{$link_table}];

}
EXT;
		file_put_contents($file, $str);

	}

}