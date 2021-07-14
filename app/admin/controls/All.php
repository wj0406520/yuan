<?php

namespace app\admin\controls;

use core\yuan\Controls;
use core\yuan\Route;
use core\yuan\Config;
use app\admin\dao\AllDao;

class All extends Controls
{

    public $handle = [];                // handle 验证参数

	public function before()
	{
		if(Route::getCheck() && !$this->getSession('admin_id')){
  			$this->redirect('login/index');
  			$this->display();
  		}
  		AllDao::$user_id = $this->getSession('admin_id');
  		$this->auth();
	}

	public function isAdmin()
	{
		return $this->getSession('type')==1;
	}

	public function auth()
	{
		if($this->isAdmin()){
			return true;
		}

		$arr = [
		];
		$str = strtolower(P('URL_CONTROL').'.'.P('URL_MODEL'));
		if(in_array($str, $arr)){
			$this->errorMsg('error_auth');
		}
	}

	public function arrayCname($arr,$cname)
	{
		$re = [];
		foreach ($cname as $key => $value) {
			$re[$value] = $arr[$key];
		}
		return $re;
	}

	public function date($time)
	{
		echo $time?date('y-m-d H:i',$time):'暂无';
	}

	public function createDate($val)
	{
		echo date('y-m-d H:i',$val[Config::getMore('database.create_at')]);
	}

	public function updateDate($val)
	{
		echo date('y-m-d H:i',$val[Config::getMore('database.update_at')]);
	}


	public function randString($length)
	{
	   $str = '';
	   $strPol = "ABCDEFGHJKLMNPQRSTUVWXY3456789abcdefghijkmnpqrstuvwxy";
	   $max = strlen($strPol)-1;

	   for($i=0;$i<$length;$i++){
	    $str.=$strPol[rand(0,$max)];
	   }
	   return $str;
	}
}