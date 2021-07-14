<?php
namespace app\admin\dao;

use core\yuan\Handle;

use service\models\Admin;
use service\models\User;
use service\models\DayLogBank;
use service\models\PayLogMsg;

class IndexDao extends AllDao
{

	private $file_list = [];

	public function __construct()
	{
		$this->admin = Admin::init()->models();
	}

	public function ichart()
	{
		return DayLogBank::init()->models()
		->field('create_data,sum(money) as money')
		->group('create_data')
		->orderAsc('create_data')
		->limit(30)
		->select();
	}

	public function changePassword()
	{
		$data = $this->request();
		if($data['new_password']!=$data['re_password']){
			$this->errorMsg('re_password');
		}
		$where = [
			'id'=>self::$user_id,
			'password'=>$this->password($data['password'])
		];
		$data = [
			'password'=>$this->password($data['new_password'])
		];

		$re = $this->admin->data($data)->where($where)->save();
		if(!$re || !$this->admin->affectedRows()){
			$this->errorMsg('error_password');
		}
		return $re;
	}

	public function getUserAllInfo()
	{
		$user = User::init()->models();
		$data = $user->field('
				sum(money) as money,
				sum(all_money) as all_money,
				sum(bank_money) as bank_money,
				sum(all_num) as all_num
			')->where(['type'=>1])->getOne();

		return $data;
	}

	public function getProportsMoney()
	{
		$models = PayLogMsg::init()->models();
		$re = $models->field("sum(proports_money) as pay_money")
		->where(['out_transaction_no'=>['<>','']])
		->getOne();
		return $re;
	}

	public function getUserTodayInfo()
	{
		$user = User::init()->models();

		$data = $user->field('
				sum(today_money) as today_money,
				sum(today_num) as today_num
			')->where([
				'last_time'=>['>',strtotime(date('Y-m-d'))]
			])->where(['type'=>1])->getOne();

		return $data;
	}

}