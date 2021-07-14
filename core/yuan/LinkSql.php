<?php
/*
+----------------------------------------------------------------------
| time       2018-05-07
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  拼接数据库语句，并且发送到数据库
+----------------------------------------------------------------------
*/
namespace core\yuan;

class LinkSql
{
    private $sql = '';                  // 数据库语句
    private $diy = '';                  // 数据库diy语句
    private $table = NULL;              // 是model所控制的表名
    private $db = NULL;                 // 是引入的mysql对象
    private $pref = NULL;               // 是表前缀
    private $main_key = 'id';           // 是主键
    private $id = 0;                    // create或者save的id值

    private $db_name = NULL;            // 是db_name 操作的字段

    private $union_str = [];            // union数据库语句
    private $data = [];                 // 是data 操作的字段

    private $where = NULL;              // 是where 操作的字段
    private $for_update = NULL;         // 行锁字段
    private $field = '*';               // 是field 操作的字段
    private $order = NULL;              // 是order 操作的字段
    private $limit = NULL;              // 是page limit 操作的字段
    private $group = NULL;              // 是group 操作的字段
    private $join = NULL;               // 是join 操作的字段
    private $having = NULL;             // 是having 操作的字段
    private $comment = NULL;            // 是comment 操作的字段
    private $yield = NULL;              // 是yield 操作的字段
    private $join_models = NULL;
    private $where_left = 0;

    private $bind_param = [
        'where'=>[],
        'insert'=>[],
        'update'=>[],
        'diy'=>[]
    ];


    //初始化函数
    public function __construct()
    {
        //引入mysql对象    进行操作
        $this->db = Mysql::getIns();

        $config = $this->db->getConf();
        //获取当前数据库的前缀
        $this->pref = $config['pref'];

        $this->main_key = $config['main_key'];
    }

    public function setJoinModels($models)
    {
        $this->join_models = $models;
        return $this;
    }

    private function getJoinModels()
    {
        return $this->join_models;
    }

    public function setYield()
    {
        $this->yield = 1;
        return $this;
    }

    public function getPref()
    {
        return $this->pref;
    }
    public function haveMianKey()
    {
        return array_key_exists($this->main_key, $this->data) && $this->data[$this->main_key];
    }
    /*
    条件筛选

    多次的数组条件表达式会最终合并，但字符串条件则只支持一次。

    parms $m 1级数组 条件拼接$this->where(['b'=>1,'legs'=>['>', 100]])
                        like LIKE    模糊查询
                        noin NOT IN    （不在）IN 查询
                        in    IN 查询
                        finset   find_in_set 查询
                        mod  MOD  取余数 ['mod',总数,余数]
    return bool
    */
    public function where($m = '', $aoo = 'AND')
    {
        $arr = [];
        if (empty($m)) {
            return $this;
        }
        if (!is_array($m)) {
            $this->error('where parms error');
        }
        foreach ($m as $key => $value) {
            $boolean = true;
            $key = $this->db_name . $key;
            // $this->checkField($key, '10002');
            if (is_array($value)) {
                $str = isset($value[1])?$value[1]:'';
                switch ($value[0]) {
                    case 'field':
                        $arr[] =$key . ' = '.$this->db_name.$str;
                        break;
                    case 'null':
                        $arr[] ='isNull('.$key.')';
                        break;
                    case 'like':
                        $arr[] = $key . ' LIKE ?';
                        $str = "%".$str."%";
                        break;
                    case 'noin':
                        $boolean = false;
                        $v = $this->linkString($str);
                        $arr[] = $key . ' NOT IN (' . $v . ')';
                        break;
                    case 'in':
                        $boolean = false;
                        $v = $this->linkString($str);
                        $arr[] = $key . ' IN (' . $v . ')';
                        break;
                    case 'finset':
                        $arr[] = ' FIND_IN_SET ( ?, ' . $key . ')';
                        break;
                    case 'mod':
                        $arr[] = ' MOD ('.$key .', ?)='.$value[2];
                    // <> 不等于
                    default:
                        $arr[] = $key .' '. $value[0] . ' ? ' ;
                }
            }else{
                $str = $value;
                $arr[] = $key . ' = ?';
            }
            $boolean && $this->bind_param['where'][] = $str;
        }

        $this->linkWhere($arr, $aoo);
        return $this;
    }

    public function diyWhere($str, $aoo = 'AND')
    {
        $arr[] = $str;
        $this->linkWhere($arr, $aoo);
        return $this;
    }
    /*
    指定操作的数据表
    parms $table  数据库表名
    */
    public function table($table)
    {
        $this->table = (strpos($table, $this->pref) === false)?$this->pref.$table:$table;
        $this->dbName($this->table);
        // $this->checkTable();
        return $this;
    }

    /*
    设置当前数据表的别名
    便于使用其他的连贯操作例如join方法
    parms $a  数据表的别名
    */
    public function dbName($db_name)
    {
        $this->db_name = $db_name.'.';
        return $this;
    }


    /*
    设置当前要操作的数据对象的值
    parms $data 1级数组 连续拼接$this->data(array('b'=>1))
    */
    public function data($data)
    {
        // foreach ($data as $key => $value) {
            // $this->checkField($key, '10004');
            // $this->data[$key] = $value;
        // }
        $this->data = $this->data?array_merge($this->data,$data):$data;
        return $this;
    }

    /*
    主要目的是标识要返回或者操作的字段，可以用于查询和写入操作
    parms $field  操作的字段数组 $this->field(['id','title','content'])
    */
    public function field($field)
    {
        $field = $this->fieldToStr($field);
        if($this->field=='*'){
            $this->field = $field;
        }else{
            $this->field .= ','.$field;
        }
        return $this;
    }

    public function setNoDbName()
    {
        $this->db_name = '';
        return $this;
    }

    /*
    用于对操作的结果排序
    逆序排列
    parms $order  $this->orderDesc('id')               order by id desc
    */
    public function orderDesc($order)
    {
        $this->order($order,'DESC');
        return $this;
    }

    /*
    用于对操作的结果排序
    顺序排列
    parms $order  $this->orderAsc('id')               order by id asc
    parms $order  $this->orderDesc('id')->orderAsc('name')               order by id desc,name asc
    */
    public function orderAsc($order)
    {
        $this->order($order,'ASC');
        return $this;
    }

    /*
    主要用于指定查询和操作的数量
    parms $limit  操作的数量 $this->limit(10)       limit 10
    parms $limit  操作的数量 $this->limit(10,25)  limit 10,25
    */
    public function limit($limit,$end = 0)
    {
        // var_dump(is_integer($limit));
        // exit;
        $str = '';
        if (!is_numeric($limit)) {
            $this->error('limit parms error');
        }

        if($end && is_numeric($end)){
            $str = ', '.$end;
        }

        $this->limit = ' LIMIT ' . $limit.$str;
        return $this;
    }


    /*
    分页查询
    parms $page  $this->page(1,10)           limit 0,10
    */
    public function page($page, $page2)
    {
        if (!is_numeric($page)) {
            $this->error('page parms error');
        }
        $page = $page > 1 ? $page : 1;
        $this->limit = ' LIMIT ' . ($page-1) * $page2 . ', ' . $page2;
        return $this;
    }


    /*
    结合合计函数,根据一个或多个列对结果集进行分组
    parms $group   $this->group('user_id')              GROUP BY user_id
    parms $group   $this->group('user_id,test_time')    GROUP BY user_id,test_time
    */
    public function group($group)
    {
        $this->group = ' GROUP BY ' .$this->db_name .$group;
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
        $this->having = ' HAVING ' . $this->fieldToStr($having);
        return $this;
    }

    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型左连接
     */
    public function leftJoin($models)
    {
        $this->joinLink($models,'LEFT');
        return $this;
    }
    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型右连接
     */
    public function rightJoin($models)
    {
        $this->joinLink($models,'RIGHT');
        return $this;
    }
    /**
     * parms $models 模型名称（User::class）
     * 当前模型和参数模型全连接
     */
    public function join($models)
    {
        $this->joinLink($models);
        return $this;
    }

    private function newModels($class)
    {
        $arr = explode('\\',$class);
        $re = end($arr);
        $models = str_replace('/','\\',str_replace(ROOT,'',MODELS_DIR)).$re;
        return $models;
    }

    private function joinLink($models,$in = 'INNER')
    {
        $pref = $this->getPref();
        $link = $this->getJoinModels();
        if(!$link){
            $this->error('no join models');
        }
        $models = $this->newModels($models);
        $models_str = $models;
        $models = $models::init();
        $join = $models->table;
        if(strpos($join, $pref) === false){
            $join = $pref . $join;
        }
        $l = 0;
        $field = $models->getLink($link);
        if(!$field){
            $temp = $link::init();
            $field = $temp->getLink($models_str);
            $l = 1;
            !$field && $this->error('link table error');
        }
        $jtemp = is_string($field)?$field:$field['join'];
        $ltemp = isset($field['link'])?$field['link']:$this->main_key;

        $join_field = $l?$ltemp:$jtemp;
        $link_field = $l?$jtemp:$ltemp;

        $this->dbName($join);

        $this->join .= ' ' . $in . ' JOIN ' . $join . ' ON ' . $join .'.'.$join_field.'='.$this->table.'.'.$link_field;

    }


    /*
    用于在生成的SQL语句中添加注释内容
    parms $comment  字段名 $this->comment('查询考试前十名分数')   LIMIT 10 // 查询考试前十名分数
    */
    public function comment($comment)
    {
        $this->comment = '/*'. $comment . '*/';
        return $this;
    }

    /*
    返回写入数据库的sql
    return sql
    */
    public function fetchSql($num = 1)
    {
        $this->db->num = $num;
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

        // $this->checkTable();
        $data = $this->data;
        if(array_key_exists($this->main_key,$data)){
            if($data[$this->main_key]){
                $rs = $this->save();
                return $rs;
            }else{
                unset($data[$this->main_key]);
            }
        }

        $rs = $this->autoExecute();
        $this->clear();
        $this->id = $rs?$this->db->insertId():0;
        return $this->id;
    }


    /*
    更新操作包括更新数据和更新字段方法
    支持where连贯操作
    $data
    return bool
    */
    public function save()
    {
        // $this->checkTable();
        $crea = 0;
        $data = $this->data;

        if(array_key_exists($this->main_key,$data)){
            $crea = $data[$this->main_key];
            $this->id = $crea;
        }

        if($crea){
            unset($this->data[$this->main_key]);
            $rs = $this->where([$this->main_key=>$crea])->autoExecute('update');
        }else{
            if ($this->where) {
                $rs = $this->autoExecute('update');
            } else {
                $this->error('save not have field where');
            }
        }

        $this->clear();
        return $rs ? true : false;
    }


    /**
     * [autoExecute 自动合成sql]
     * @param  string $mode  [Insert新增 update更新]
     * @param  string $where [where数据]
     * @return [type]        [返回sql之后的数据]
     */
    private function autoExecute($mode = 'insert')
    {
        /*    insert into tbname (username,passwd,email) values ('',)
        /// 把所有的键名用','接起来
        */
        $arr = $this->data;
        if (!is_array($arr)) {
            return false;
        }
        if ($mode == 'update') {
            $sql = 'UPDATE ' . $this->table . ' SET ';
            foreach($arr as $k=>$v) {
                /*新增data的功能*/
                $str = '';
                if(is_array($v)){
                    $str = ($v[0]=='ADD')?' + ':' - ';
                    $str = $k.$str;
                    $v = $v[1];
                }
                $sql .= $k . " = ".$str."?, ";
                /*新增data的功能*/
                // $sql .= $k . "= ?, ";
                $this->bind_param['update'][] = $v;
            }
            $sql = substr($sql,0,-2);
            $sql .= $this->where;
            $parm = array_merge($this->bind_param['update'],$this->bind_param['where']);
            $this->db->setParam($parm);
        }else{
            $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',',array_keys($arr)) . ')';
            $sql .= ' VALUES (';
            $sql .= $this->linkInsert($arr);
            $sql .= ')';
            $this->db->setParam($this->bind_param['insert']);
        }
        return $this->db->query($sql);

    }

    private function linkInsert($data)
    {
        $this->bind_param['insert'] = array_values($data);
        $str = substr(str_repeat('?,',count($data)),0,-1);
        return $str;
    }

    /*
    查询数据
    return bool
    */
    public function select($num = 0)
    {
        if ($num) {
            return $this->count($num);
        }
        $this->linkSql();
        $arr = $this->getAll($this->sql);
        $this->clear();
        return $arr;
    }

    /*
    自定义查询
        $sql = 'select * from j_admin where id>?';
        $parms = [30];
        $id = $models->setParam($parms)->setSql($sql)->diySelect();
    尽量使用链式操作，diy少用
    */
    public function diySelect()
    {
        $this->db->setParam($this->bind_param['diy']);
        $arr = $this->getAll($this->diy);
        return $arr;
    }
    public function setSql($sql)
    {
        $this->diy = $sql;
        return $this;
    }
    public function setParam($arr)
    {
        $this->bind_param['diy'] = $arr;
        return $this;
    }

    /*
    查询单个数据
    $type = 1 总量
    */
    public function getOne($type = 0)
    {
        $this->limit(1);
        if($this->group && $type == 1){
            $this->limit = NULL;
        }
        $this->linkSql();
        if($this->group && $type == 1){
            $this->sql = 'SELECT count(*) as a FROM ('.$this->sql.') as t';
        }

        $arr=$this->db->getOne($this->sql);
        $this->clear();
        return $arr;
    }

    /*
    查询单个数据
    */
    public function find($id)
    {
        $this->where([$this->main_key => $id]);
        $arr = $this->getOne();
        return $arr;
    }

    /*
    查询数据总量
    */
    public function count()
    {
        if(!$this->group){
            $this->field = 'count(*) as a';
        }
        $arr = $this->getOne(1);
        $arr = $arr['a'];
        return $arr;
    }

    /*
    **连接数据库语句
    */
    private function linkSql()
    {
        $this->db->setParam($this->bind_param['where']);
        // $this->checkTable();
        $this->sql = 'SELECT '.
        $this->field. ' FROM ' .
        $this->table.
        ' as '.
        $this->table.
        $this->join.
        $this->where.
        $this->group.
        $this->having.
        $this->order.
        $this->limit.
        $this->for_update;
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
        $this->linkSql();
        $this->union_str[] = $this->sql;
        $this->clear();
        return $this;
    }
    /*
    查询数据
    return array
    */
    public function unionSelect()
    {
        $arr = [];
        if($this->union_str){
            foreach ($this->union_str as $value) {
                $arr[]='select * from ('.$value.') as a';
            }
        }else{
            $this->error('no union sql');
        }
        $this->sql = implode(' union ',$arr);
        $this->sql .= str_replace($this->db_name,'',$this->order);
        $this->sql .= $this->limit;
        $arr = $this->getAll($this->sql);
        $this->clear();
        return $arr;
    }
    /*
    **清除相关字段
    */
    private function clear()
    {
        $this->dbName($this->table);
        $this->bind_param = ['where'=>[],'insert'=>[],'update'=>[],'diy'=>[]];
        $this->where = NULL;              // 是where 操作的字段
        $this->field = '*';               // 是field 操作的字段
        $this->order = NULL;              // 是order 操作的字段
        $this->limit = NULL;              // 是page limit 操作的字段
        $this->group = NULL;              // 是 group 操作的字段
        $this->data = [];                 // 是data 操作的字段
        $this->join = NULL;               // 是join 操作的字段
        $this->having = NULL;             // 是having 操作的字段
        $this->comment = NULL;            // 是comment 操作的字段
        $this->for_update = NULL;            // 行锁字段
        $this->db->fetch(NULL);           // 清空循环函数
    }

    // 获取新增的id
    public function insertId()
    {
        return $this->id;
    }

    // 返回影响行数的函数
    public function affectedRows()
    {
        return $this->db->affectedRows();
    }

    /**
     * [autoCommit 开启事务]
     * @param  boolean $bool [真假值 真为开启自动提交 假为关闭自动提交]
     */
    public function autoCommit($bool = false)
    {
        return $this->db->autoCommit($bool);
    }
    /**
     * [commit 提交事务]
     */
    public function commit()
    {
        return $this->db->commit();
    }
    /**
     * [forUpdate 事务行锁]查询行锁
     * 必须要有事务 autoCommit  commit
     * where   字段（必须是索引）
     */
    public function forUpdate()
    {
        $this->for_update = ' FOR UPDATE ';
    }
    /*
    直接执行sql语句
    */
    public function query($sql)
    {
        $rs = $this->db->query($sql);

        return $rs?true:false;
    }

    // 判断array field是否正确
    private function fieldToStr($field)
    {
        $arr = $field;
        $arr = explode(',', $field);
        $array = [];
        $str = $this->db_name;
        foreach ($arr as $value) {
            if($value[0]=="'" || $value[0]=='"'){
                $array[] = $value;
                continue;
            }

            $left = strpos($value,'(');
            if($left===false){
                $array[] = $str . $value;
            }else{
                $right = strpos($value,')');
                $ex = explode('(',$value);
                $value = substr($value,$left+1,$right-$left-1);
                $array[] = $ex[0].'('.$str.$ex[1];
            }
        }

        return implode(',', $array);
    }

    public function fetch($func)
    {
        $this->db->fetch($func);
    }

    public function getSql()
    {
        return $this->db->getSql();
    }
    public function whereLeft()
    {
        $this->where_left = 1;
    }
    public function whereRight()
    {
        $this->where = $this->where.' )';
    }

    /*
    链接where语句
    */
    private function linkWhere($arr, $aoo)
    {
        $aoo = ($aoo=='AND')?'AND':'OR';
        $str=implode(' ' . $aoo . ' ', $arr);

        $left = '';
        if($this->where_left==1){
            $left = '(';
            $this->where_left = 0;
        }

        if (!$this->where) {
            $this->where = ' WHERE '. $left . $str . '';
        }else{
            $this->where .= ' ' . $aoo .$left. ' ' . $str;
        }
        // print_r($this->where);
        return $this->where;
    }

    /*
    连接字符语句
    */
    private function linkString($data)
    {
        if(is_array($data)){
            $arr = $data;
        }else{
            $arr = explode(',',$data);
        }
        $this->bind_param['where'] = array_merge($this->bind_param['where'],$arr);
        // $str=implode(',',$arr);
        $str = substr(str_repeat('?,',count($arr)),0,-1);
        return $str;
    }

    private function getAll($sql)
    {
        if($this->yield){
            $this->yield = NULL;
            return $this->db->getYieldAll($sql);
        }
        return $this->db->getAll($sql);
    }

    /*
    用于对操作的结果排序
    如果没有指定desc或者asc排序规则的话，默认为asc
    parms $order  $this->order('id desc,status')               order by id desc,status asc
    parms $order  $this->order('id desc')                      order by id desc
    */
    private function order($key,$status)
    {
        if(!$key){
            $this->error('order parms error');
        }
        $key = $this->db_name . $key;
        if($this->order){
            $this->order .= ', ' .$key.' '.$status;
        }else{
            $this->order = ' ORDER BY ' .$key.' '.$status;
        }
    }

    private function error($message)
    {
        Error::setMessage($message);
    }
}