<?php
namespace app\admin\controls;

class Login extends All
{

	public $form = [
		'index'=>['accounts','password']
	];

	public $handle = [
		'post'=>['form'=>'index'],
	];
	public function indexAction()
	{
		$this->form();
		$this->display();
	}
	public function postAction()
	{
		if(!$this->getSession('admin_id')){
			$arr = $this->dao->post();
			$this->setSession($arr);
		}
		$this->redirect('address/index');
		$this->display();
	}

}