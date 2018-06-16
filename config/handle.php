<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-27
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  请求参数信息
+----------------------------------------------------------------------
*/
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

return [

	'aa'=>['name'=>'翻页','handle'=>'fill'],
	'index.index.name' =>['name'=>'用户名','handle'=>['length','length612','6,12']],
	'bb'=>['name'=>'姓名','handle'=>'search'],

	'name' =>['name'=>'用户名','handle'=>['length','length612','6,12']],
	'password' =>['name'=>'密码','handle'=>['length','length612','6,12']],
	'token' =>['name'=>'token','handle'=>['require','miss']],

	'page'=>['name'=>'翻页','handle'=>'page'],
	'pagesize'=>['name'=>'翻页总数','handle'=>'pagesize'],
	'phone'=>['name'=>'电话号码','handle'=>'phone'],
	'file'=>['name'=>'上传文件文件','handle'=>'file'],

	'card'=>['name'=>'身份证号码','handle'=>'card'],

	'index.index.is_used'=>['name'=>'是否可用','handle'=>['in','error_type','user.is_used']],
	'is_used'=>['name'=>'是否可用','handle'=>['in','error_type']],

];

