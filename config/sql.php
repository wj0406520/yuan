<?php
/*
+----------------------------------------------------------------------
| time       2021-06-06
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  数据库内容信息文件（此表自动维护）
+----------------------------------------------------------------------
*/

return [
    'charset'=>'utf8mb4',
    'collate'=>'utf8mb4_0900_ai_ci',

    'pref'=>'j_',
    'main_key'=>'id',
    'create_at'=>'create_at',
    'update_at'=>'update_at',

    'table'=>[
        'admin'=>[
            'comment'=>'管理员',
            'engine'=>'InnoDB',
            'column'=>[
                'name'=>['comment'=>'用户名', 'type'=>'varchar','size'=>'10', 'unsign'=>'0'],
                'accounts'=>['comment'=>'账号', 'type'=>'varchar','size'=>'10', 'unsign'=>'0'],
                'password'=>['comment'=>'密码', 'type'=>'varchar','size'=>'100', 'unsign'=>'0'],
                'type'=>['comment'=>'类型', 'type'=>'tinyint','size'=>'0', 'unsign'=>'1']
            ],
            'index'=>[]
        ],
        'evolve'=>[
            'comment'=>'计划阶段表',
            'engine'=>'InnoDB',
            'column'=>[
                'plan_id'=>['comment'=>'计划ID', 'type'=>'int','size'=>'0', 'unsign'=>'1'],
                'type'=>['comment'=>'类型阶段1:20%;2:40%;3:60%;4:80%;5:100%', 'type'=>'tinyint','size'=>'0', 'unsign'=>'1']
            ],
            'index'=>[]
        ],
        'plan'=>[
            'comment'=>'计划表',
            'engine'=>'InnoDB',
            'column'=>[
                'name'=>['comment'=>'计划名称', 'type'=>'varchar','size'=>'10', 'unsign'=>'0'],
                'type_id'=>['comment'=>'类型ID', 'type'=>'int','size'=>'0', 'unsign'=>'1'],
                'user_id'=>['comment'=>'用户ID', 'type'=>'int','size'=>'0', 'unsign'=>'1'],
                'evolve_end'=>['comment'=>'当前阶段', 'type'=>'tinyint','size'=>'0', 'unsign'=>'1'],
                'evolve_at'=>['comment'=>'当前阶段时间', 'type'=>'int','size'=>'0', 'unsign'=>'0']
            ],
            'index'=>[]
        ],
        'report'=>[
            'comment'=>'打卡表',
            'engine'=>'InnoDB',
            'column'=>[
                'user_id'=>['comment'=>'用户表', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'sight_id'=>['comment'=>'景点表', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'longitude'=>['comment'=>'经度', 'type'=>'decimal','size'=>'10,6', 'unsign'=>'0'],
                'latitude'=>['comment'=>'纬度', 'type'=>'decimal','size'=>'10,6', 'unsign'=>'0']
            ],
            'index'=>[]
        ],
        'sight'=>[
            'comment'=>'景点表',
            'engine'=>'InnoDB',
            'column'=>[
                'name'=>['comment'=>'名称', 'type'=>'varchar','size'=>'50', 'unsign'=>'0'],
                'longitude'=>['comment'=>'经度', 'type'=>'decimal','size'=>'10,6', 'unsign'=>'0'],
                'latitude'=>['comment'=>'纬度', 'type'=>'decimal','size'=>'10,6', 'unsign'=>'0'],
                'level'=>['comment'=>'等级', 'type'=>'tinyint','size'=>'0', 'unsign'=>'0'],
                'sort'=>['comment'=>'排序', 'type'=>'tinyint','size'=>'0', 'unsign'=>'0'],
                'province'=>['comment'=>'省', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'province_id'=>['comment'=>'省Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'city'=>['comment'=>'市', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'city_id'=>['comment'=>'市Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'district'=>['comment'=>'区', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'district_id'=>['comment'=>'区Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'evaluate'=>['comment'=>'评定年份', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'country_num'=>['comment'=>'国家编号', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'is_show'=>['comment'=>'0显示1不显示', 'type'=>'tinyint','size'=>'0', 'unsign'=>'0']
            ],
            'index'=>[]
        ],
        'district'=>[
            'comment'=>'身份证信息',
            'engine'=>'InnoDB',
            'column'=>[
                'level'=>['comment'=>'等级', 'type'=>'tinyint','size'=>'0', 'unsign'=>'0'],
                'province'=>['comment'=>'省', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'province_id'=>['comment'=>'省Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'city'=>['comment'=>'市', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'city_id'=>['comment'=>'市Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
                'district'=>['comment'=>'区', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'district_id'=>['comment'=>'区Id', 'type'=>'int','size'=>'0', 'unsign'=>'0'],
            ],
            'index'=>[]
        ],
        'system'=>[
            'comment'=>'系统表',
            'engine'=>'InnoDB',
            'column'=>[
                'name'=>['comment'=>'名称', 'type'=>'varchar','size'=>'10', 'unsign'=>'0'],
                'key'=>['comment'=>'键值', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'value'=>['comment'=>'内容', 'type'=>'varchar','size'=>'200', 'unsign'=>'0']
            ],
            'index'=>[]
        ],
        'type'=>[
            'comment'=>'类型表',
            'engine'=>'InnoDB',
            'column'=>[
                'name'=>['comment'=>'类型名称', 'type'=>'varchar','size'=>'10', 'unsign'=>'0'],
                'ico_url'=>['comment'=>'ico地址', 'type'=>'varchar','size'=>'19', 'unsign'=>'0']
            ],
            'index'=>[]
        ],
        'user'=>[
            'comment'=>'用户表',
            'engine'=>'InnoDB',
            'column'=>[
                'user_name'=>['comment'=>'用户名', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'token'=>['comment'=>'用户网站唯一标示', 'type'=>'varchar','size'=>'50', 'unsign'=>'0'],
                'openid'=>['comment'=>'微信openid', 'type'=>'varchar','size'=>'50', 'unsign'=>'0'],
                'session_key'=>['comment'=>'会话密钥', 'type'=>'varchar','size'=>'50', 'unsign'=>'0'],
                'avatar_url'=>['comment'=>'微信头像', 'type'=>'varchar','size'=>'300', 'unsign'=>'0'],
                'country'=>['comment'=>'国家', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'province'=>['comment'=>'省份', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'city'=>['comment'=>'城市', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'language'=>['comment'=>'语言', 'type'=>'varchar','size'=>'20', 'unsign'=>'0'],
                'gender'=>['comment'=>'0未知1男2女', 'type'=>'tinyint','size'=>'0', 'unsign'=>'1']
            ],
            'index'=>[]
        ],
    ]
];