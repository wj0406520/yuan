<?php
namespace app\api\controls;
class Login extends All
{
	public function indexAction()
	{
		// var_dump($this->handle);exit;
		// $this->errorName('haha')->errorMsg('miss');
		// echo 111;
		$arr = $this->route;
		// print_r($arr);
		$this->setValue($arr);
		// $this->redirect('/index/index');
		$this->display();
	}
}