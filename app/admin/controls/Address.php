<?php

namespace app\admin\controls;

class Address extends All{


    public $handle = [
        'index'=>['pagesize','page'],
        'info'=>['id','page'],
        'post'=>['id','longitude','latitude','sort','is_show','page']
    ];


	public function indexAction(){

        $this->form();
        $count = $this->dao->getList(1);
        $this->page($count);
        $data['data'] = $this->dao->getList();

        $this->setValue($data);
		$this->display();

	}

	public function infoAction()
	{
		$data = $this->dao->info();

        $this->setValue(['info'=>$data]);

		$this->display();
	}

	public function postAction()
	{
		$post = $this->request();
		$page = array_pop($post);

		$this->dao->postAddress($post);

        // $this->redirect('index',['page'=>$page]);
		$this->display();
		// $this->redirect('address/index');
		// $this->display();
		// $this->redirect('/address/info?id='.($post['id']+1).'&page='.$page);
	}

}
?>
