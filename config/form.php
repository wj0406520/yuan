<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-27
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  form表单
+----------------------------------------------------------------------
*/

/*
key 是form表单中的名称
key.form 是form的类型[input|select|file|textarea|extend]
			extend 为扩展类型
key.type 是当form为input时的二级类型[text|password|hidden|date|checkbox|radio]
key.name 是表单的中文名称，会在出错时返回
key.placeholder 是欲填写内容
key.target 是前端报错的内容
key.handle 是前端提交信息时验证的类型[require|phone|file|int|email] 更多需要前端扩展
key.sql_type 是form类型为[select] 或者 type类型为[checkbox|radio]时使用
			 为config/type.php的 key 值

key.** 其他字段可以扩展使用

此文件为app..views/Views.ph 使用
*/

return [

	'name'=>['form'=>'input','type'=>'text','name'=>'姓名','placeholder'=>'姓名','target'=>'haha','handle'=>'require'],
	'password'=>['form'=>'input','type'=>'password','name'=>'密码','placeholder'=>'密码','target'=>'密码','handle'=>'require'],
	'hidden'=>['form'=>'input','type'=>'hidden','target'=>'隐藏','handle'=>'require'],
	'date'=>['form'=>'input','type'=>'date','target'=>'日期','handle'=>'require'],

	'select'=>['form'=>'select','sql_type'=>'is_used','name'=>'是否使用'],

	'file_auto'=>['form'=>'file','name'=>'上传图片'],
	'file_must'=>['form'=>'file','name'=>'上传图片','handle'=>'file'],

	'textarea'=>['form'=>'textarea','name'=>'上传图片','placeholder'=>'haha'],

	'checkbox'=>['form'=>'input','type'=>'checkbox','sql_type'=>'position','name'=>'多选','placeholder'=>'haha'],
	'id_used'=>['form'=>'input','type'=>'radio','sql_type'=>'is_used','name'=>'是否使用','placeholder'=>'是否使用'],

	'extend'=>['form'=>'extend','type'=>'myextend','haha'=>'omg','name'=>'omg'],
	// 'select2'=>['form'=>'select'],
];
