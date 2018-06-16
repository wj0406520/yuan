<?php

namespace app\admin\dao;

use service\models\Plan;
use service\models\User;


class IndexDao extends AllDao
{

	private $user = null;
	private $plan = null;

	public function __construct()
	{
		$this->user = User::init()->models();
		$this->plan = Plan::init()->models();
	}

	public function getUser()
	{
		$data = $this->user->getOne();
		return $data;
	}
	public function getPlan()
	{
		$data = $this->plan->select();
		return $data;
	}

	public function create()
	{
		$data = [
			'type_id'=>1,
			'user_id'=>1,
			'evolve_at'=>TIME
		];

		$this->plan->data($data)->create();
		print_r($this->plan->insertId());
	}
	public function upda()
	{
		$data = [
			'type_id'=>2,
			'user_id'=>2,
			'evolve_at'=>TIME
		];
		$where = [
			'evolve_end'=>255
		];
		$num = $this->plan->data($data)->where($where)->save();

		print_r($num);
	}

	public function getUserPlan()
	{
		$where = [
			// 'id'=>27
			// 'user_name'=>['like','11']
		];
		$where1 = [
			// 'id'=>27
			'id'=>['<',3]
		];
		$data = $this->user
		// ->table('admin')
		->field(['id as user_id','token','user_name',"'sade' as zz"])
		->where($where)
		->fetchSql()
		->where($where1)
		->join(Plan::class)
		->where($where1)
		->field(['id as plan_id','create_at','type_id'])
		->orderDesc('id')
		->select();

		// print_r($this->user->getSql());
		return $data;
	}
}