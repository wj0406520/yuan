<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-06-09
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  数据库表关联文件（此表手动维护）
+----------------------------------------------------------------------
*/

/*
key 是table名称
key.array.column 当前表中的字段
key.array.table  当前表中的字段的表 对应该表的id字段(默认)
key.array.table_key  对应该表的字段(修改默认id)
*/

return [
	'evolve'=>[
		['column'=>'plan_id','table'=>'plan']
	],
	'plan'=>[
		['column'=>'type_id','table'=>'type'],
		['column'=>'user_id','table'=>'user']
	],
	'report'=>[
		['column'=>'sight_id','table'=>'sight'],
		['column'=>'user_id','table'=>'user']
	],
	'sight'=>[
		['column'=>'city_id','table'=>'address'],
		['column'=>'province_id','table'=>'address'],
		['column'=>'district_id','table'=>'address','table_key'=>'province_id'],
	],
];