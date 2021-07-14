<?php

namespace app\admin\controls;
use service\package\Tool;
use core\yuan\Handle;

class Index extends All
{

    public $form = [
        'password'=>['password','new_password','re_password'],
    ];

    public $handle = [
        'ppost'=>['form'=>'password'],
        'flist'=>['pagesize','page']
    ];

	public function indexAction()
	{
		$ichart = $this->dao->ichart();
		$data = $this->dao->getUserAllInfo();
		$temp = $this->dao->getUserTodayInfo();
		$m = $this->dao->getProportsMoney();
		$arr = array_merge($data,$temp,$m);
		// $this->dao->text();
        $this->setValue(['data'=>$arr,'ichart'=>$ichart]);
		$this->display();
	}

	public function outAction()
	{
		$this->clearSession();
		$this->redirect('login/index');
		$this->display();
	}


    public function passwordAction()
    {
		$this->form();
		$this->display();
    }
    public function ppostAction()
    {
    	$this->dao->changePassword();
    	$this->redirect('password');
    	$this->display();
    }
    public function uploadAction()
    {
        $data['url'] = Tool::upload('img_url');
        $this->setValue($data);
        $this->display();
    }

    public function textAction()
    {
        $data = ['password'=>'1000000','new_password'=>'1000000'];
        $config = [
            'password','new_password'
        ];
        Handle::run($config, $data);
        $re = $this->request();
        var_dump($re);
        echo 11;
    }

}