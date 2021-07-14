<?php
/*
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
    // 使用self::$user_id
    public static $user = [];        //用户相关信息

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
    private $main_key = '';  // 主键

    public function __construct()
    {
        $config = Config::get('sql');
        $this->update_at = $config['update_at'];
        $this->create_at = $config['create_at'];
        $this->main_key = $config['main_key'];
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
    public static function imodel()
    {
        return self::init()->models();
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
        if(!$this->link_table || !isset($this->link_table[$link])){
            return false;
            // $this->error('link table error');
        }
        return $this->link_table[$link];
    }

    /**
    条件筛选 and
    多次的数组条件表达式会最终合并，但字符串条件则只支持一次。
    parms $m 1[2]级数组 条件拼接$this->where(['b'=>1,'legs'=>['>', 100]])
                        like LIKE    模糊查询
                        noin NOT IN    （不在）IN 查询
                        in    IN 查询
                        finset   find_in_set 查询
    return bool
    */
    public function where($m = '')
    {
        $this->models->where($m);
        return $this;
    }
    public function diyWhere($str, $aoo = 'AND')
    {
        $this->models->diyWhere($str, $aoo);
        return $this;
    }
    /*添加where左括弧*/
    public function whereLeft()
    {
        $this->models->whereLeft();
        return $this;
    }
    /*添加where右括弧*/
    public function whereRight()
    {
        $this->models->whereRight();
        return $this;
    }
    /**
    条件筛选 or
    */
    public function whereOr($m = '')
    {
        $this->models->where($m,'OR');
        return $this;
    }
    // 去除表名
    public function setNoDbName()
    {
        $this->models->setNoDbName();
        return $this;
    }
    /*
    设置当前要操作的数据对象的值
    parms $data 1级数组 连续拼接$this->data(['b'=>1])
    $this->data(['b'=>['SUB',1]])
    $this->data(['b'=>['ADD',1]])
    */
    public function data($data)
    {
        $this->models->data($data);
        return $this;
    }

    /*
    主要目的是标识要返回或者操作的字段，可以用于查询和写入操作
    parms $field  操作的字段数组 $this->field(['id as user_id','count(id)','title','content'])
    */
    public function field($field)
    {
        $this->models->field($field);
        return $this;
    }

    /*
    用于对操作的结果排序
    逆序排列
    parms $order  $this->orderDesc('id')               order by id desc
    */
    public function orderDesc($order = '')
    {
        $order = $order?$order:$this->main_key;
        $this->models->orderDesc($order);
        return $this;
    }

    /*
    用于对操作的结果排序
    顺序排列
    parms $order  $this->orderAsc('id')               order by id asc
    parms $order  $this->orderDesc('id')->orderAsc('name')               order by id desc,name asc
    */
    public function orderAsc($order = '')
    {
        $order = $order?$order:$this->main_key;
        $this->models->orderAsc($order);
        return $this;
    }

    /*
    主要用于指定查询和操作的数量
    parms $limit  操作的数量 $this->limit(10)       limit 10
    parms $limit  操作的数量 $this->limit(10,25)  limit 10,25
    */
    public function limit($limit,$end = 0)
    {
        $this->models->limit($limit,$end);
        return $this;
    }

    /*
    分页查询
    parms $page  $this->page(['page'=>1,'pagesize'=>15])           limit 0,10
    */
    public function page($page)
    {
        if(!$page){
            return $this;
        }
        if(!isset($page['page'])){
            return $this;
        }
        if(!isset($page['pagesize'])){
            $page['pagesize'] = 15;
        }
        $this->models->page($page['page'],$page['pagesize']);
        return $this;
    }

    /*
    结合合计函数,根据一个或多个列对结果集进行分组
    parms $group   $this->group('user_id')              GROUP BY user_id
    parms $group   $this->group('user_id,test_time')    GROUP BY user_id,test_time
    */
    public function group($group)
    {
        $this->models->group($group);
        return $this;
    }

    /*
    配合group方法完成从分组的结果中筛选
    having方法只有一个参数，并且只能使用字符串
    parms $having  操作的字段 $this->having('count(test_time)>3')     HAVING count(test_time)>3

    $this->field('username,max(score)')->group('user_id')->having('count(test_time)>3')->select();
    SELECT username,max(score) FROM think_score GROUP BY user_id HAVING count(test_time)>3
    */
    public function having($having)
    {
        $this->models->having($having);
        return $this;
    }
    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型左连接
     */
    public function leftJoin($models)
    {
        $this->models->leftJoin($models);
        return $this;
    }
    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型右连接
     */
    public function rightJoin($models)
    {

        $this->models->rightJoin($models);
        return $this;
    }
    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型全连接
     */
    public function join($models)
    {
        $this->models->join($models);
        return $this;
    }

    /*
    系统根据数据源是否包含主键数据来自动判断，
    如果存在主键数据(报错)
    如果不存在主键数据新增数据
    独立使用不支持连贯操作
    $data
    return bool|create_id
    */
    public function create()
    {
        $this->autoTime();
        return $this->models->create();
    }
    /**
     * 主动获取create之后数据的id
     */
    public function insertId()
    {
        return $this->models->insertId();
    }
    // 返回影响行数的函数
    public function affectedRows()
    {
        return $this->models->affectedRows();
    }
    private function autoTime($flage = 0)
    {
        if($this->auto_time){
            if($this->models->haveMianKey() || $flage){
                $data[$this->update_at] = TIME;
            }else{
                $data[$this->create_at] = TIME;
            }
            $this->data($data);
        }
    }
    /*
    更新操作包括更新数据和更新字段方法
    支持where连贯操作
    $data
    return bool
    */
    public function save()
    {
        $this->autoTime(1);
        return $this->models->save();
    }

    /*
    parms $num 如果0返回查询的语句，如果是其他的返回查询的总数
    查询数据
    return bool|count
    */
    public function select($num = 0)
    {
        return $this->models->select($num);
    }
    /*
    查询单个数据
    */
    public function getOne()
    {
        return $this->models->getOne();
    }
    /*
    查询单个数据根据主键
    */
    public function find($id)
    {
        return $this->models->find($id);
    }
    /*
    查询数据总量
    */
    public function count()
    {
        return $this->models->count();
    }


    /*
    自定义查询（独立，不影响其他）
        $sql = 'select * from j_admin where id>?';
        $parms = [30];
        $id = $models->setParam($parms)->setSql($sql)->diySelect();
    尽量使用链式操作，diy少用
    */
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

    /*
    连接语句
        $where = [
            'id'=>['>',30]
        ];
        $where1 = [
            'id'=>['<',5]
        ];
        $id = $models
        ->table('admin')->field('id,name')->where($where)
        ->union()
        ->field('id,name')->where($where1)->union()->orderDesc('id')->limit(5)
        ->unionSelect();
    */
    public function union()
    {
        $this->models->union();
        return $this;
    }
    /*
    查询查询数据
    return array
    */
    public function unionSelect()
    {
        return $this->models->unionSelect();
    }

    /**
     * [setYeild 当返回数据很多的时候开启]
     */
    public function setYield()
    {
        $this->models->setYield();
        return $this;
    }
    /**
     * [autoCommit 开启事务]
     * @param  boolean $bool [真假值 真为开启自动提交 假为关闭自动提交]
     */
    public function autoCommit($bool = false)
    {
        $this->models->autoCommit($bool);
    }
    /**
     * [commit 提交事务]自动回滚和提交
     */
    public function commit()
    {
        $this->models->commit();
    }

    /**
     * [forUpdate 事务行锁]查询行锁
     * 必须要有事务 autoCommit  commit
     * where   字段（必须是索引）
     */
    public function forUpdate()
    {
        $this->models->forUpdate();
        return $this;
    }

    /*
    直接执行sql语句
    */
    public function query($sql)
    {
        return $this->models->query($sql);
    }
    /*
    用于在生成的SQL语句中添加注释内容
    parms $comment  字段名 $this->comment('查询考试前十名分数')   LIMIT 10 // 查询考试前十名分数
    */
    public function comment($comment)
    {
        $this->models->comment($comment);
        return $this;
    }
    /*
    返回写入数据库的sql
    return sql
    */
    public function fetchSql($num = 1)
    {
        $this->models->fetchSql($num);
        return $this;
    }
    /**
     * 获取当前执行的所有sql语句，需要fetchSql先执行
     */
    public function getSql()
    {
        return $this->models->getSql();
    }
    public function fetch($func)
    {
        $this->models->fetch($func);
        return $this;
    }

    private function error($message)
    {
        Error::setMessage($message);
    }
}