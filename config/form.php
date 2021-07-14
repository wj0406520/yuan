<?php
/*
+----------------------------------------------------------------------
| time       2018-04-27
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  form表单
+----------------------------------------------------------------------

fm => form
nm => name
pd => placeholder
hd => handle
vf => verificate
st => sql_type

*/

return [
	'password'=>[
		'form'=>'password',
		'name'=>'密码','placeholder'=>'密码',
		'handle'=>'require',
		'verificate'=>'length612'
	],
	'accounts'=>[
		'form'=>'text',
		'name'=>'用户名','placeholder'=>'用户名',
		'handle'=>'require',
		'verificate'=>'length612'
	],
	'new_password'=>[
		'form'=>'password',
		'name'=>'新密码','placeholder'=>'新密码',
		'handle'=>'require',
		'verificate'=>'length612'
	],

	're_password'=>[
		'form'=>'password',
		'name'=>'确认密码','placeholder'=>'确认密码',
		'handle'=>'require',
		'verificate'=>'length612'
	],


	'phone'=>[
		'form'=>'text',
		'name'=>'手机号码','placeholder'=>'手机号码',
		'verificate'=>'search'
	],
	'id'=>['name'=>'id','verificate'=>'fill'],
	'data'=>['name'=>'data','verificate'=>'search'],
	'order_no'=>['name'=>'order_no','verificate'=>'fills'],
	'page'=>['name'=>'page','verificate'=>'page'],
	'token'=>['name'=>'token','verificate'=>'fills'],
	'time'=>['name'=>'time','verificate'=>'fills'],

];


/*
key 是form表单中的名称
key.form 是form的类型[text|password|hidden|date|checkbox|radio|select|file|textarea|extend]
			extend 为扩展类型
key.name 是表单的中文名称，会在出错时返回
key.placeholder 是欲填写内容
key.target 是前端报错的内容
key.handle 是前端提交信息时验证的类型[require|phone|file|int|email] 更多需要前端扩展
key.sql_type 是form类型为[select] 或者 type类型为[checkbox|radio]时使用
			 为config/type.php的 key 值 当存在时 [verification]不用填写

key.verification 请求参数信息
// $arr = self::handle([
//     'password'=>['length','password','6,16'],
//     'password'=>['in','password','6,16'],
//     'password'=>['between','password','6,16'],
//     'phone'=>['phone','phone'],
//     'name'=>['search','true',''],
//     'sex'=>['search','false',''],
//     'age'=>['fill','int',8],
//     'time'=>['fill','time'],
//     'double'=>['fill','double',8.88],
//     'string'=>['fill','string','asdfas'],
//     'card'=>'card',
//     'file'=>'file',
//     'email'=>'email',
//     'id'=>['arr','int'],
//     'in'=>['arr','string'],
// ]);

key.** 其他字段可以扩展使用

此文件为app..views/Views.ph 使用
*/
