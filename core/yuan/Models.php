<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  模型 实例 生成对应表的实例
|            对进入数据库的数据进行验证，和修改
+----------------------------------------------------------------------
*/
namespace core\yuan;

class Models
{
    public static $user_id = 0;           //用户id

    protected $column = [];

    protected $index = [];

    protected $table = '';

    protected $comment = '';

    protected $error = '';

    protected $models = NULL;

    protected $link_table = [];

    protected $check = [];

    protected $auto_time = 1;  // 是否开启自动更新时间和创建时间的操作，默认开启

    private static $init = NULL;

    private $update_at = ''; // 是更新 操作的字段
    private $create_at = ''; // 是创建 操作的字段

    public function __construct()
    {
        $config = Config::get('sql');
        $this->update_at = $config['update_at'];
        $this->create_at = $config['create_at'];
    }

    public function setAutoTime($bool = 0)
    {
        $this->auto_time = $bool;
    }

    public static function init()
    {
        // $cla = new static;
        $name = static::class;
        // var_dump(isset(self::$init[$name]));
    	if(!isset(self::$init[$name]) || !(self::$init[$name] instanceof self)){
	    	self::$init[$name] = new static;
    	}
        return self::$init[$name];
    }

    public function models()
    {
    	$models = NULL;
    	if($this->models){
    		$models = $this->models;
    	}else{
			$models = new LinkSql();
    	}
		// $this->models->setModels($this);
		// print_r();
		$models
		->setJoinModels(get_class($this))
		->table($this->table);

		if(!$this->models){
			$this->models = $models;
		}
        return $this;
		// return $this->models;
    }

    public function getLink($link)
    {
    	if(!isset($this->link_table[$link])){
    		$this->error('link table error');
    	}
    	return $this->link_table[$link];
    }

    public function where($m = '')
    {
        $this->models->where($m);
        return $this;
    }
    public function whereOr($m = '')
    {
        $this->models->where($m,'OR');
        return $this;
    }
    public function data($data)
    {
        $this->models->data($data);
        return $this;
    }

    public function field($field)
    {
        $this->models->field($field);
        return $this;
    }

    public function orderDesc($order)
    {
        $this->models->orderDesc($order);
        return $this;
    }
    public function orderAsc($order)
    {
        $this->models->orderAsc($order);
        return $this;
    }

    public function limit($limit,$end = 0)
    {
        $this->models->limit($limit,$end);
        return $this;
    }
    public function page($page, $page2)
    {
        $this->models->page($page,$page2);
        return $this;
    }

    public function group($group)
    {
        $this->models->group($group);
        return $this;
    }
    public function having($having)
    {
        $this->models->having($having);
        return $this;
    }

    public function leftJoin($models)
    {
        $this->models->leftJoin($models);
        return $this;
    }
    public function rightJoin($models)
    {

        $this->models->rightJoin($models);
        return $this;
    }
    public function join($models)
    {
        $this->models->join($models);
        return $this;
    }

    public function create()
    {
        if($this->auto_time){
            $data[$this->create_at] = TIME;
            $this->data($data);
        }
        return $this->models->create();
    }
    public function insertId()
    {
        return $this->models->insertId();
    }
    public function save()
    {
        if($this->auto_time){
            $data[$this->update_at] = TIME;
            $this->data($data);
        }
        return $this->models->save();
    }

    public function select($num = 0)
    {
        return $this->models->select($num);
    }
    public function getOne()
    {
        return $this->models->getOne();
    }
    public function find($id)
    {
        return $this->models->find($id);
    }
    public function count()
    {
        return $this->models->count();
    }


    public function setSql($sql)
    {
        $this->models->setSql($sql);
        return $this;
    }
    public function setParam($arr)
    {
        $this->models->setParam($arr);
        return $this;
    }
    public function diySelect()
    {
        return $this->models->diySelect();
    }


    public function union()
    {
        $this->models->union();
        return $this;
    }
    public function unionSelect()
    {
        return $this->models->unionSelect();
    }

    public function autoCommit($bool = false)
    {
        $this->models->autoCommit($bool);
    }
    public function commit()
    {
        $this->models->commit();
    }

    public function query($sql)
    {
        return $this->models->query($sql);
    }
    public function fetchSql($num = 1)
    {
        $this->models->fetchSql($num);
        return $this;
    }
    public function getSql()
    {
        return $this->models->getSql();
    }

    private function error($message)
    {
        Error::setMessage($message);
    }



}