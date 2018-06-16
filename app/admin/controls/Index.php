<?php

namespace app\admin\controls;

class Index extends All
{
	public function indexAction()
	{
		// print_r($this->route);
		// print_r($this->dao);
		// $this->dao->create();
		// $this->dao->upda();
		// $client = new \Predis\Client();
		// $client->set('xixi',1111);
		// print_r($client);
		// $data = $this->dao->getUserPlan();
		$data = $this->dao->getUserPlan();
		// print_r($this->dao->user_id);
		print_r($data);
		// exit;
		return false;
		$this->page(1000, 1, 10);

		// $this->display();
		// print_r($this->page);

// 1.循环出内容
// 2.循环出标题
// 3.数据循环出的时候处理
// 4.新增，删除操作
// 5.翻页函数
// 6.可以扩展

// $list->data($data);
// $list->register(function($value){
// 		$value['date'] = strtotime('Y-m-d',$value['date']);
// });
// $list->run();


		// $this->chooseData(['select'=>[0=>'aa',2=>'bb'],'id_used'=>['22'=>'lalala','33'=>'iiii']]);
		// $user['checkbox'] = '1,2,3,4';
		// $this->defaultData($user);
		$this->nameData('wang')->form(['checkbox','id_used']);
		// print_r($this->handle);
		// echo 111;
		// $this->form();
		$arr = $this->route;
		// print_r($arr);
		$this->setValue($arr);
		$this->display();
	}
	public function hahaAction()
	{
		echo 22;
	}
}