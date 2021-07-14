<?php
/*
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  数据库底层 实例
+----------------------------------------------------------------------
*/
namespace core\yuan;

class Mysql{

    //自身类
    private static $ins = NULL;

    //mysqli类
    private $mysqli = NULL;

    //配置参数
    private $conf = [];

    //拦截因子
    public $num = 0;

    //事务判断
    public $boolean = 1;

    private $sql = [];

    private $bind_param = [];

    private $affected_rows = 0;

    private $func = null;

    private $error = null;
    /**
     * [__construct 实例化]
     */
    protected function __construct()
    {
        //获取配置参数
        $this->conf = Config::get('database');
        //连接数据库
        $this->connect();
        //设置字符集
        // $this->setChar();

    }

    /**
     * [__destruct 关闭数据库执行事务]
     */
   public function __destruct()
   {
        if(!$this->mysqli->connect_errno){
            $this->mysqli->close();
        }
   }

    //实例化自身
    public static function getIns()
    {
        if (!(self::$ins instanceof self)) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    //连接数据库
    public function connect()
    {
    	$conf = $this->conf;
        $this->mysqli = new \mysqli($conf['host'], $conf['user'], $conf['pwd'], $conf['db'],$conf['ports']);
        if ($this->mysqli->connect_error) {
            $this->error("Connect failed: " . $this->mysqli->connect_error);
        }
        //设置字符集
        $char = $this->mysqli->get_charset();
        $char = $char->charset;
        if($char!=$conf['char']){
        	$this->mysqli->set_charset($conf['char']);
        }
    }

    /**
     * [query 发送sql语句到mysqli]
     * @param  [string]   $sql [sql语句]
     * @return [resource]      [资源类型]
     */
    public function query($sql)
    {
        //如果拦截因子存在输出sql语句
        $str = $this->bind_param ? ' --> '.implode(' , ', $this->bind_param):'';
        if ($this->num) {
        	$this->sql[] = $sql.$str;
        }

        (IS_SQL_LOG) && $this->log($sql.$str);

        //发送sql语句
        if($this->bind_param){
        	$rs = $this->prepare($sql);
        }else{
        	$rs = $this->mysqli->query($sql);
        }
        //如果sql失败  写入log文件
        $this->boolean = $rs && $this->boolean;
        if(!$rs){
            print_r($sql);
            print_r($this->error);
            $this->error('sql error,check sql');
        }

        return $rs;
    }

    private function log($sql)
    {
    	MoreLog::sql($sql);
    }

    public function setParam($arr)
    {
    	$this->bind_param = $arr;
    }

    private function prepare($sql)
    {
		$stmt = $this->mysqli->prepare($sql);
		if(!$stmt){
            $this->error('prepare error:'.$this->mysqli->error);
		}
		$str = '';
		$refs = [];
		$count = $stmt->param_count;
		array_walk($this->bind_param,function(&$value) use(&$str,&$refs,&$count){
			// var_dump($value);
			if($count==0){
				return false;
			}
			$count--;
			switch (gettype($value)) {
				case 'integer':
					$str .= 'i';
					break;
				case 'double':
					$str .= 'd';
					break;
				default:
					$str .= 's';
					break;
			}
			// 解决bind_param函数不能引用传递的问题
			// 这里有一个问题，在赋值之后$this->bind_param发生了变化，按理论不应该有的
			$refs[] = &$value;
		});
		array_unshift($refs,$str);
        // var_dump($this->bind_param);
        // var_dump($sql);
        // var_dump($refs);
        // exit;
		$str && call_user_func_array([$stmt,'bind_param'],$refs);
		$re = $stmt->execute();
        $this->affected_rows = $stmt->affected_rows;
		$result = $stmt->get_result();
        $this->error = $stmt->error;
		$result = $result?$result:$re;
		return $result;
    }

    public function getConf()
    {
        return $this->conf;
    }

    //获取数据库前缀
    public function getPref()
    {
        return $this->conf['pref'];
    }

    public function getSql()
    {
    	return $this->sql;
    }

    //显示当前数据库下的表
    public function showTables()
    {
        $sql = 'show tables';
        $arr = $this->getAll($sql);
        foreach ($arr as &$value) {
            $value = $value['Tables_in_' . $this->conf['db']];
        }
        return $arr;
    }

    /**
     * [descTables 显示当前表中所有字段，并获取主见名称]
     * @param  [string] $table [表名]
     * @return [array]         [所有字段]
     */
    public function descTables($table)
    {
        $sql = 'desc ' . $table;
        $arr = $this->getAll($sql);
        foreach ($arr as &$value) {
            $value = $value['Field'];
        }
        return $arr;
    }

    public function fetch($func)
    {
        $this->func = $func;
    }

    /**
     * [getAll 获取所有数据]
     * @param  [string] $sql [sql语句]
     * @return [array]       [sql之后的所有数据]
     */
    public function getAll($sql)
    {
        $rs = $this->query($sql);

        $list = [];
        $func = $this->func;
        while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {
            if($func){
               $func($row);
            }
            $list[] = $row;
        }
        $this->func = null;
        return $list;
    }
    /**
     * [getYieldAll 获取所有数据(当取出的数据量大的情况下使用)]
     * @param  [string] $sql [sql语句]
     * @return [object]      [可以循环的对象]
     */
    public function getYieldAll($sql)
    {
        $rs = $this->query($sql);

        // $list = [];
        $func = $this->func;
        while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {
            if($func){
               $func($row);
            }
            yield $row;
            // $list[] = $row;
        }
        $this->func = null;
        // return $list;
    }
    /**
     * [getOne 获取第一条数据]
     * @param  [string] $sql [sql语句]
     * @return [array]       [sql之后的第一条数据]
     */
    public function getOne($sql)
    {
        $rs = $this->query($sql);
        $row = $rs->fetch_assoc();
        return $row;
    }

    // 返回影响行数的函数
    public function affectedRows()
    {
        return $this->affected_rows;
    }

    // 返回最新的auto_increment列的自增长的值
    public function insertId()
    {
        return $this->mysqli->insert_id;
    }

    /**
     * [autoCommit 开启事务]
     * @param  boolean $bool [真假值 真为开启自动提交 假为关闭自动提交]
     */
    public function autoCommit($bool = false)
    {
        $this->mysqli->autocommit($bool);
    }

    /**
     * [commit 提交事务]
     * @param  [boolean] $boolean [真则提交  假则回滚]
     */
    public function commit()
    {
        if($this->boolean){
            $this->mysqli->commit();
        }else{
            $this->mysqli->rollback();
        }
        return $this->boolean;
    }

    private function error($message)
    {
		Error::setMessage($message);
    }

}