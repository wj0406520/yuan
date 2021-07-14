<?php
namespace app\admin\dao;

use core\yuan\Handle;

// use service\models\Address;
use service\models\Admin;
// use service\models\Branch;
// use service\models\Classify;
// use service\models\Collage;
// use service\models\CutPrice;
// use service\models\Discount;
// use service\models\DiscountAppoint;
// use service\models\Genre;
// use service\models\GenreInfo;
// use service\models\Goods;
// use service\models\GoodsCar;
// use service\models\GoodsCollect;
// use service\models\GoodsGenre;
// use service\models\GoodsImg;
// use service\models\Order;
// use service\models\PhoneCode;
// use service\models\SecKill;
// use service\models\User;


class LoginDao extends AllDao
{

	public function __construct()
	{
		$this->admin = Admin::init()->models();
	}

	public function post()
	{
		$data = $this->request();
		$data['password'] = $this->password($data['password']);
		$re = $this->admin->field('id as admin_id, name, type')->where($data)->getOne();
		if(!$re){
			$this->errorMsg('error_password');
		}
		// $this->admin->data($data)->create();
		return $re;
	}
}