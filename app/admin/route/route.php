<?php

/*

格式

控制器 => 模型
	title 标题
	is_web  是否为web为 1是web页面 0为api 默认为1
	is_check 是否开启验证 1是验证 0为不验证 默认为1
	layout 布局界面 默认layout.html
 */


return [
	'index'=>[
		'index' => [
			'title' =>'首页',
			// 'handle' => ['name','card','phone','is_used'],
			// 'is_check'=>1,
			'form'=>'index.php',
		],
		'out' => [
			'title' =>'后台退出',
			// 'is_check'=>1,
		],
		'password' => [
			'title' =>'修改密码',
			// 'is_check'=>1,
		],
		'ppost'=>[
			'title'=>'修改密码接口',
			'is_web' => 0,
		],
		'upload'=>[
			'title'=>'图片上传接口',
			'is_web' => 0,
			'is_check'=>0,
		],
	],
	'address'=>[
		'index'=>[
			'title'=>'地址列表',
		],
		'info'=>[
			'title'=>'地址详情',
		],
		'post'=>[
			'title'=>'登录接口',
			'is_web' => 0,
		],
	],
	'login'=>[

		'index'=>[
			'title'=>'登录',
			'is_check'=>0,
			'layout'=>0
		],
		'post'=>[
			'title'=>'登录接口',
			'is_web' => 0,
			'is_check'=>0,
		],

	],

	'shop'=>[
		'index' => [
			'title' =>'商家管理',
		],
		'add' => [
			'title' =>'新增商家',
		],

		'post' => [
			'title' =>'商家修改新增接口',
			'is_web' => 0,
		],
		'frozen'=>[
			'title' =>'快速冻结账户接口',
			'handle'=>['id', 'data'],
			'is_web' => 0,
		],

		'text' => [
			'title' =>'测试账号',
			'handle'=>['id','page'],
		],
		'run' => [
			'title' =>'测试账号接口',
			'handle'=>['id','page','text_money','bank_pay_type'],
			'is_web' => 0,
		],

	],

	'pay'=>[
		'index' => [
			'title' =>'支付宝账户管理',
		],
		'add' => [
			'title' =>'新增支付宝账户',
		],


		'post' => [
			'title' =>'支付宝账户修改新增接口',
			'is_web' => 0,
			// 'is_check'=>1,
		],
		'change'=>[
			'title' =>'快速修改优先级接口',
			'handle'=>['id', 'data'],
			'is_web' => 0,
		],
		'used'=>[
			'title' =>'快速修改是否可用',
			'handle'=>['id', 'data'],
			'is_web' => 0,
		],
		'text'=>[
			'title' =>'快速修改是否正常',
			'handle'=>['id', 'data'],
			'is_web' => 0,
		],
		'clear'=>[
			'title' =>'重置优先级',
			'is_web' => 0,
		],

	],

	'agent'=>[
		'index' => [
			'title' =>'银行卡管理',
			'handle'=>['page','pagesize'],
		],
	],

	'money'=>[
		'index' => [
			'title' =>'提现管理',
			'handle'=>['page','pagesize','start_time','end_time','search_user_id'],
			// 'is_check'=>1,
		],
		'info' => [
			'title' =>'提现详情',
			'handle'=>['id'],
			'layout'=>"nomenu.html"
			// 'is_check'=>1,
		],
		'post' => [
			'title' =>'提现完成',
			'handle'=>['id','user_bank_state'],
			'is_web' => 0,
			// 'is_check'=>1,
		],
	],

	'order'=>[
		'index' => [
			'title' =>'订单管理',
			// 'is_check'=>1,
		],
		'run'=>[
			'title' =>'补单',
			'is_web' => 0,
		],


	],

	'source'=>[
		'user' => [
			'title' =>'用户日结',
			'handle'=>['page','pagesize','search_user_id','search_day'],
			// 'is_check'=>1,
		],
		'bank' => [
			'title' =>'账号日结',
			'handle'=>['page','pagesize','search_bank_id','search_day'],
			// 'is_check'=>1,
		],
		'money' => [
			'title' =>'收益日结',
			'handle'=>['page','pagesize','search_day'],
			// 'is_check'=>1,
		],

	],

	'ali'=>[
		'index' => [
			'title' =>'支付宝账户管理',
			'handle'=>['page','pagesize'],
			// 'is_check'=>1,
		],
		'add' => [
			'title' =>'新增支付宝账户',
			'handle'=>['id','page'],
			// 'is_check'=>1,
		],
		'used'=>[
			'title' =>'快速修改是否可用',
			'handle'=>['id', 'data'],
			'is_web' => 0,
		],
		'post' => [
			'title' =>'支付宝账户修改新增接口',
			'is_web' => 0,
			'handle' => ['bank_ali_url','remark','is_used','id','bank_pay_name','bank_no','bank_no_type']
			// 'is_check'=>1,
		],

	],

	'text'=>[
		'index'=>[
			'title' =>'快速修改是否可用',
			'is_web' => 0,
		],

	],

];