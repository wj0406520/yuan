<?php

namespace app\api\controls;

use core\yuan\Mysql;
use core\yuan\LinkSql;

use service\models\User;
use service\models\Plan;

class Index extends All
{

	public function indexAction()
	{

		print_r($this->handle);
		print_r($this->route);
		$u = new User;
		$p = new Plan;
		$id1 = User::init()->models();
		// $p = Plan::init()->models();

		// $id2 = User::init();

		// var_dump($id1===$id2);
		// var_dump($id1);
		$where1 = [
			// 'id'=>27
			'id'=>['<',10]
		];
		$data = [
			'user_name'=>'text',
			'token'=>'1234561',
			'create_at'=>TIME,
			// 'id'=>1
		];
		$field = ['id as iddd','token'];
		$id1->fetchSql(1);
		print_r($id1->field($field)->where($where1)->getOne());
		// $id = $id1->data($data)->create();
		// print_r($p->getOne());
		print_r($id1->getSql());
		// print_r($id);
		exit;
		// $p->models();
		// $u->models();

		// $str = '| id | user_name | token                            | openid                       | session_key              | avatar_url | country | province | city | language | gender | create_at |';

		// $arr = explode('|',$str);
		// foreach ($arr as $key => $value) {
		// 	echo '\''.trim($value).'\'=>[],';
		// 	echo '<br>';
		// }
		$where = [
			// 'id'=>27
			// 'name'=>['like','11']
		];
		$where1 = [
			// 'id'=>27
			'id'=>['<',10]
		];
		$id = $id1
		// ->table('admin')
		->field(['id as user_id','token'])
		->where($where)
		->fetchSql(1)
		->where($where1,'or')
		// ->join('user','u')
		// ->setJoinModels(Plan::class)
		->join(Plan::class)
		->field(['id as plan_id','create_at','type_id'])
		->orderDesc('id')
		// ->where(['id'=>['<',2]])
		// ->where(['id'=>2])
		->select();

		print_r($id);
		print_r($id1->getSql());
// exit;
	}

}