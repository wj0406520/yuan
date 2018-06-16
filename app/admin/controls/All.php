<?php

namespace app\admin\controls;

use core\yuan\Controls;
use core\yuan\Route;
use app\admin\dao\LoginDao;

class All extends Controls
{

	public function before()
	{
		// if(Route::getCheck() && !$this->getSession('aa')){
  			// $this->redirect('/login/index');
  		// }
	}
	public function checkToken()
    {
        $arr = IS_POST ? $_POST : $_GET;
        $token = isset($arr['token'])?$arr['token']:'';
        if(!$token){
	      $this->errorMsg('error_token');
        }
	    $a = new LoginDao();
	    $a = $a->checkToken($token);
	    if ($a) {
	      Models::$user_id = $a;
	    } else {
	      $this->errorMsg('token');
	    }
    }


}