<?php
/*
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  获取数据库信息(废弃)
+----------------------------------------------------------------------
*/
namespace bin\lib;

class MysqlLink
{
	public $mysql = '';
	public $table = '';
	public $schema = '';
	public $table_list = [];
	public $column_list = [];
	public $schema_list = [];
	public $schema_all = [];
	public $write = '';

	public function __construct()
	{
		$this->mysql = \core\yuan\Mysql::getIns();
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