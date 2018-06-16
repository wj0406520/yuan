<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-04-24
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  数据库内容信息文件
+----------------------------------------------------------------------
*/

return [
	'schema'=>,
	'comment'=>,
	'charset'=>'utf8',
	'collate'=>'utf8_bin',

	'pref'=>'j_',
	'main_key'=>'id',
	'create_at'=>'create_at',
	'update_at'=>'update_at',

	'table'=>[
        'text'=>[
            'comment'=>'测试表20',
            'engine'=>'InnoDB',// MyISAM
            'column'=>[
                'user_name'=>['comment'=>'用户名', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'age'=>['comment'=>'年龄', 'type'=>'tinyint','size'=>'3', 'unsign'=>'0'],
                'image'=>['comment'=>'头像', 'type'=>'int','size'=>'200', 'unsign'=>'0'],
                'money'=>['comment'=>'金额', 'type'=>'decimal','size'=>'15,2', 'unsign'=>'1'],
                'remarks'=>['comment'=>'备注1', 'type'=>'text','size'=>'0', 'unsign'=>'0'],
                'token'=>['comment'=>'token', 'type'=>'int','size'=>'11', 'unsign'=>'1'],
                'gender'=>['comment'=>'0未知1男2女', 'type'=>'tinyint','size'=>'3', 'unsign'=>'1']
            ],
            'index'=>[
                'token'=>['type'=>'UNIQUE', 'column'=>'token'],
                'remarks'=>['type'=>'FULLTEXT', 'column'=>'remarks'],
                'name'=>['type'=>'KEY', 'column'=>'user_name'],
                'haha'=>['type'=>'KEY', 'column'=>'user_name,token'],
            ]
        ]
	],
];


/*
comment 是备注名

table.key 是table名称
table.key.comment 是table的备注
table.key.engine 是table存储引擎
table.key.colume.key 是字段名称
table.key.colume.key.type 是字段类型[int|tinyint|decimal|char|varchar|text]
table.key.colume.key.size 字段长度[char|varchar|decimal]时候使用(int[,int])
table.key.colume.key.is_unsign 是否为无符号[int|tinyint|decimal]时候使用(true|false)


table.key.index.key 是索引名称
table.key.index.key.type 是索引类型
table.key.index.key.column 索引字段默认主键索引不显示,(column[,colume,...])
*/

