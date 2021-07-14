<?php

/**
 * shop模版
 */


namespace app\api\dao;

use core\tool\HttpTool;



class LoginDao extends AllDao
{
	public $table = 'user';

	public function getOpenid($code)
	{
		$where = ['token'=>$code];
		$user = M(User::class)->field('token')->where($where)->getOne();
		if($user){
			return '';
		}
		$url='https://api.weixin.qq.com/sns/jscode2session';
		$arr=[
			'appid'=>'wxc5bd3a931bdeaeb5',
			'secret'=>'8b728e24de6822723b314fb6a70897ce',
			'js_code'=>$code,
			'grant_type'=>'authorization_code'
		];
		$http = new HttpTool();

		$re = $http->setData($arr)->setUrl($url)->get();
		$re = json_decode($re,true);
		if(isset($re['errcode'])){
			// $this->errorMsg('wechat');
			return '';
		}
		unset($re['expires_in']);
		$re['token'] = $code;

		$this->create($re);
		// return ['token'=>$code];
	}
}