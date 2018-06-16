<?php

/*

格式

控制器.模型
	title 标题
	handle 验证参数
	is_web  是否为web为 1是web页面 0为api 默认为1
	is_check 是否开启验证 1是验证 0为不验证 默认为1
 */


return [
	'index.index' => [
		'title' =>'首页',
		// 'handle' => ['name','card','phone','is_used'],
		'handle' => ['name','is_used'],
		// 'handle' => [],
		// 'is_web' => '0',
		'is_check'=>0,
		'form'=>'index.php'
	],
	'login.index'=>[
		'title'=>'登录',
		'handle'=>['aa'],
		// 'is_web' => '0',
		'is_check'=>0,
		'layout'=>''
	],

];