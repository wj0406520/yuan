<?php

namespace app\api\controls;



class Login extends all
{

	public $check=0;

	public function indexAction()
	{
		$post = $this->handle([
				'code'=>'search',
			]);
		$post=$this->models->getOpenid($post['code']);
		// $info = $this->models->getUser($post);
		// print_r($post);
		$this->success($post);

	}

}
?>