<?php

namespace app\admin\dao;

use core\yuan\Dao;
use service\models\System;
/**
*
*/
class AllDao extends Dao
{

	public function __construct()
	{

	}

	public function quick($quick, $name)
	{
		if(!$quick){
			$this->error('no hava quick model');
		}
		$id = $this->request('id');
		if(!$id){
			return true;
		}
		$data = [
			'id'=>$id,
			$name=>$this->request('data'),
		];

		$quick->data($data)->create();
	}
	public function isAdmin()
	{
		return $this->getSession('type')==1;
	}

    public function systemSetValue($key,$value)
    {
		System::init()->models()
		->where(['key'=>$key])
		->data(['value'=>$value])
		->save();
		$num = System::init()->affectedRows();
		if(!$num){
			$this->errorName('system修改'.$key)->errorMsg('error_service');
		}
    }
    public function systemGetValue($key)
    {
		$temp = System::init()->models()
		->where(['key'=>$key])
		->getOne();
		if(!$temp){
			$this->errorName('system获取'.$key)->errorMsg('error_service');
		}
		return $temp['value'];
    }

}