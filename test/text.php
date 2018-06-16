<?php
// 设置访问模式
define('IS_CLI', true);
use core\yuan\Mysql;
use core\yuan\LinkSql;
use app\api\models\Plan;
use app\api\models\User;
include '../core/init.php';

class App
{

	public static function run()
	{
		$mysql = new TextMysql();
		// $mysql->run();
		$link = new TextLinkSql();
		$link->run();
		// echo 111;
	}
}



// bcadd — 将两个高精度数字相加
// bccomp — 比较两个高精度数字，返回-1, 0, 1
// bcdiv — 将两个高精度数字相除
// bcmod — 求高精度数字余数
// bcmul — 将两个高精度数字相乘
// bcpow — 求高精度数字乘方
// bcpowmod — 求高精度数字乘方求模，数论里非常常用
// bcscale — 配置默认小数点位数，相当于就是Linux bc中的”scale=”
// bcsqrt — 求高精度数字平方根
// bcsub — 将两个高精度数字相减
class TextModel
{

    public function where($m = '', $aoo = 'AND')
    {
        $this->models->where($m,$aoo);
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
        $this->models->limit($page,$page2);
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
        return $this->models->create();
    }
    public function insertId()
    {
        return $this->models->insertId();
    }
    public function save()
    {
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

}

class TextMysql
{
	public $mysql = NULL;
	public function __construct()
	{
		$this->mysql = Mysql::getIns();
	}
	public function run()
	{
		$mysql = $this->mysql;
		$arr = $mysql->showTables();
		foreach ($arr as $key => $value) {
			$a = $mysql->descTables($value);
			// print_r($value);
			// print_r($a);
			// echo "<br/><br/><br/>";
		}
		$mysql->num = 1;
		print_r($arr);
		$sql = 'select * from j_admin';
		$arr = $mysql->getAll($sql);
		print_r($arr);
		$sql = 'select * from j_user where id = 2 or id = 1';
		$arr = $mysql->getAll($sql);
		print_r($arr);
		// print_r($mysql->getSql());
		$sql = 'select * from j_user where id = ? or token like ? or id in (?)';
		$sql = 'select * from j_user where id IN (?,?)';
		$sql = 'select * from j_admin where FIND_IN_SET(?,password)';
		$mysql->setParam(['211','%a%','1']);
		$arr = $mysql->getAll($sql);
		print_r($arr);

		print_r($mysql->getSql());

	}
}

class TextLinkSql
{
	public $link = NULL;
	public function __construct()
	{
		$this->link = new LinkSql;
	}

	public function run()
	{
		$models = $this->link;
		$u = new User;

		$data = [
			'name'=>'111',
			'accounts'=>'text',
			'password'=>'1234561',
			'create_at'=>TIME,
			// 'id'=>1
		];
		$where = [
			// 'id'=>27
			// 'name'=>['like','11']
		];
		$where1 = [
			// 'id'=>27
			'id'=>['>',10]
		];
		$order = 'id';
		// $models->autoCommit();
		// Auto_increment
		// SHOW TABLE STATUS FROM  库名 WHERE Name = "表名"
		// $id = $models->table('admin')->data($data)->create();
		// $id = $models->table('admin')->data(['name'=>'222'])->create();
		// $id = $models->table('admin')->data(['aaa'=>'333'])->create();
		// $models->commit();

		// 只要新增就会占用一个id 如果回滚id自动加1，而实际却并没有这条数据，所以会产生断层现象

		// $id = $models->insertId();
		// $id = $models->table('admin')->data($data)->fetchSql(1)->where($where)->save();
		$id = $models->table('admin')->where($where)->fetchSql(0)->where($where1,'or')->select();
		// $id = $models->table('admin')->alias('a')->field('id,accounts,count(id) as sdf')->group('accounts')->having('count(id)>2')->where($where)->fetchSql(1)->where($where1,'or')->select();
		print_r($id);
		exit;
		// $id = $models->table('admin')->field('id,accounts,count(id) as a')->group('accounts')->having('count(id)>2')->where($where)->fetchSql(0)->where($where1,'or')->select();
		// $id = $models
		// ->table('admin')
		// ->alias('a')
		// ->field('id,accounts')
		// ->where($where)
		// ->fetchSql(1)
		// ->where($where1,'or')
		// ->join('user','u')
		// ->joinLink('a.id=u.id')
		// ->select();
		$id = $u->models()
		// ->table('admin')
		->field('id as user_id,token')
		->where($where)
		->fetchSql(1)
		->where($where1,'or')
		// ->join('user','u')
		// ->setJoinModels(Plan::class)
		->join(Plan::class)
		->field('id as plan_id,create_at,type_id')
		->orderDesc('id')
		// ->where(['id'=>2])
		->select();

		// print_r($u->models()->str);
		// $id = $u->models()->select();

		$sql = 'select * from j_admin where id>?';
		// $sql = '';
		$parms = [30];

		$id = $models->setParam($parms)->setSql($sql)->diySelect();

		$where = [
			'id'=>['>',30]
		];
		$where1 = [
			'id'=>['<',5]
		];

		$id = $models
		->table('admin')
		->field('id,name')
		->where($where)
		->union()
		->field('id,name')
		->where($where1)
		->union()
		->orderDesc('id')
		->limit(5)
		->unionSelect();



		// print_r($models->getSql());
		print_r($id);
		exit;
// SELECT j_user.id as user_id,j_user.token,j_plan.id as plan_id,j_plan.create_at FROM j_user as j_user INNER JOIN j_plan ON j_plan.user_id=j_user.id WHERE (j_user.id < ? ) AND j_plan.id = ? ORDER BY j_plan.id DESC
		// print_r($models->getSql());
// select id,id+1 as aa from j_admin limit 1
		$data = [
			'name'=>'1',
			'type_id'=>'23231',
			'user_id'=>'1',
			'evolve_end'=>233,
			'evolve_at'=>TIME,
			'create_at'=>TIME,
			'id'=>1
		];
		$id = $p->models()
		->data($data)
		->save();
		// ->select();
		// Array ( [0] => SELECT a.id,a.accounts FROM j_admin as a INNER join j_user as u ON a.id=u.id WHERE (name LIKE ?) OR a.id < ?
		// print_r($models);
		print_r($models->getSql());
		// $id = $models->table('admin')->where($where)->count();
		// $id = $models->table('admin')->where($where)->orderDesc($order)->getOne();
		// $id = $models->table('admin')->where($where)->limit(10,10)->select();
		// $id = $models->table('admin')->where($where)->page(2,10)->select();
		// $id = $models->table('admin')->find(27);
		// print_r($id);
		print_r($id);
		// print_r($models);
		// var_dump(is_string('112aa'));
		// echo 111;
		// print_r($this->handle);
	}
}

App::run();