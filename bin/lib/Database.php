<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  获取数据库信息
+----------------------------------------------------------------------
*/
namespace bin\lib;


class Database
{
	public $mysql = '';
	public $table = '';
	public $schema = '';
	public $table_list = [];
	public $column_list = [];
	public $schema_list = [];
	public $schema_all = [];
	public $file = '';
	public $config = [];
	public $write = '';

	public function __construct($config)
	{
		$this->mysql = \core\yuan\Mysql::getIns();
		$this->config = $config;
		Produce::config($config);
	}
	public function local($local)
	{
		// 1.获取
		// 2.对比
		// 3.一样跳过，不一样修改，不存在新增

		// 存在则对比，对比一样则跳过，不一样修改
		// 不存在则新增
		Produce::setLocal($local);

		// 1.数据库是否存在
		$this->getSchemaChar();

		$sql = Produce::schemaContrast($this->schema_all);
		// var_dump($sql);
		$this->querySql($sql);
		$this->schema = $local['schema'];
		$re = $this->getTable();

		Produce::table($re);

		foreach ($local['table'] as $key => $value) {

			$table = $local['pref'].$key;
			if(!in_array($table, $re)){
				// echo 111;
				$sql = Produce::tableCreate($key);
			}else{
				$this->setValue($table);
				$sql = Produce::tableContrast();
			}
			// print_r($sql);
			$this->querySql($sql);
			// exit;
		}

		$write = "---".date('H:i:s')."---\n".$this->write;
		$file = BACK_DIR.date('Y_m_d_',TIME).$local['schema'].'_local.sql';
		file_put_contents($file,$write,FILE_APPEND);
		echo "\nsuccess\n";

	}

	public function querySql($sql)
	{
		if(!$sql){
			return false;
		}
		$sqls = explode(";\n",$sql);
		foreach ($sqls as $key => $value) {
			if(trim($value)){
				$this->write .= $value."\n";
				echo '.';
				$this->mysql->query($value);
			}
		}
	}
	public function write($file)
	{
		$this->file = $file;
		return $this;
	}
	public function service($schema)
	{
		$re = $this->use($schema)->getTable();
		Produce::pref($re);
		$str = '';
		$str .= Produce::start($schema);
		foreach ($re as $key => $value) {
			$this->setValue($value);

			$str .= Produce::service();
		}
		$str .= Produce::end();

		if(!$this->file){
			echo "not have file\n";
			exit;
		}
		file_put_contents($this->file, $str);
		echo "success\n";

	}

	public function back($schema)
	{
		$re = $this->use($schema)->getTable();
		$str = '';
		$str .= Produce::schema($schema);
		$file = BACK_DIR.date('Y_m_d_',TIME).$schema.'.sql';
		foreach ($re as $key => $value) {
			$this->setValue($value);
			// print_r($table);
			$str .= Produce::sql();
			$data = $this->tableData();
			$str .= Produce::data($data);
			// exit;
		}
		file_put_contents($file, $str);
		echo "success\n";
		// print_r($str);
		exit;
	}

	public function setValue($value)
	{
		$this->table($value);
		$column = $this->tableColumn();
		$table = $this->tableState();
		$indexs = $this->tableIndex();
		// print_r($indexs);
		// exit;
		Produce::table($table);
		Produce::columns($column);
		Produce::indexs($indexs);
	}

	public function renew()
	{
		$re = opendir(BACK_DIR);

		$time = 0;
		$nd = '';
		while ($file = readdir($re)) {
			if($file=="." || $file==".."){
				continue;
			}
			$file = BACK_DIR.$file;
			$t = filemtime($file);
			if($t>$time){
				$time = $t;
				$nd = $file;
			}
		}
		closedir($re);
		if(!$nd){
			return false;
		}
		$content = file_get_contents($nd);

		$sqls = explode(";\n",$content);

		foreach ($sqls as $value) {
			$sql = $value;
			if(trim($sql)){
				$bool = $this->mysql->query($sql);
				if(!$bool){
					echo $sql;
					exit;
				}
				echo ".";
			}
		}
		echo "\nsuccess\n";
	}


	public function clearTable()
	{
		$table = $this->table;
		$this->mysql->query("TRUNCATE TABLE {$table}");
	}

    public function getSchema()
    {
        $list = $this->query('SHOW DATABASES;');
        $re = array_column($list, "Database");
        $this->schema_list = $re;
        return $this;
    }
    public function getSchemaChar()
    {
        $list = $this->query('select * from information_schema.schemata');
        $re = [];
        foreach ($list as $key => $value) {
		    $re[$key]['character'] = $value["DEFAULT_CHARACTER_SET_NAME"];
		    $re[$key]['collation'] = $value["DEFAULT_COLLATION_NAME"];
		    $re[$key]['table'] = $value["SCHEMA_NAME"];
        }
        $this->schema_all = $re;
        return $this;
    }

    public function getTable()
    {
        $this->checkTable();
        $list = $this->query('SHOW TABLES');
        $re = array_column($list, "Tables_in_".$this->schema);
        return $re;
    }

	public function tableIndex()
	{
		$table = $this->table;
		$list = $this->query("SHOW keys FROM {$table}");
		return $list;
	}
    public function tableState()
    {
    	$table = $this->table;
        $this->checkTable();
        $list = $this->query("SHOW TABLE STATUS LIKE '{$table}';");
        return $list[0];
    }

    public function tableColumn()
    {
    	$table = $this->table;
        $this->checkTable();
        $list = $this->query("SHOW FULL COLUMNS FROM {$table};");
        return $list;
    }

    public function tableData()
    {
    	$table = $this->table;
        $this->checkTable();
        $list = $this->query("select * from $table");

        return $list;
        // print_r($list);
    }

    public function query($sql)
    {
    	$data = $this->mysql->getAll($sql);
    	return $data;
    }

    public function table($table)
    {
    	$this->table = $table;
    	return $this;
    }

    public function use($schema)
    {
        $this->schema = $schema;
        // mysqli_query($this->db,"use ".$table);
        $this->mysql->query("use ".$schema);
        return $this;
    }

    private function checkTable()
    {
        if(!$this->schema){
            echo 'no use schema';
            exit;
        }
    }
}