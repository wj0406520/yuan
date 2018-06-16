<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  产生数据库语句
+----------------------------------------------------------------------
*/
namespace bin\lib;

class Produce
{
    private static $columns=[];
    private static $table = [];
    private static $sql = '';
    private static $indexs = [];
    private static $pref = '';
    private static $local = [];
    private static $status = 0; //-1 不存在,0 不相同,1 相同
    private static $config = [];
    private static $equal = [];
    private static $table_name = '';
    private static $sqls = [];
    private static $time = [
        'create_at'=>'创建时间',
        'update_at'=>'更新时间',
    ];


    public static function config($config)
    {
        self::$config = $config;
    }
    public static function schema($db_name)
    {
        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8 COLLATE utf8_bin;\nuse `$db_name`;\n\n";
        return $sql;
    }

    public static function schemaContrast($schema)
    {
        $local = self::$local;
        $sql = '';
        $status = -1;
        $db_name = $local['schema'];
        foreach ($schema as $key => $value) {
            if($value['table']==$db_name){
                $status = 0;
                if($value['character']==$local['charset'] && $value['collation']==$local['collate']){
                    $status = 1;
                }
            }
        }
        if($status == -1){
            return self::schema($db_name);
        }
        if($status == 0){
            $sql = "ALTER DATABASE `$db_name` CHARACTER SET utf8 COLLATE utf8_bin;\nuse `$db_name`;\n\n";
            return $sql;
        }
        return "use `$db_name`;\n";
    }

    public static function tableCreate($table)
    {
        $local = self::$local;
        $config = self::$config;
        $table_name = $local['pref'].$table;
        $table = self::$local['table'][$table];
        $indexs = $table['index'];

        // $table['column']['']
        $arr = [];
        $arr[] = "`".$config['main_key']."` int(11) NOT NULL AUTO_INCREMENT";

        foreach ($table['column'] as $key => $value) {
            # code...
            $begin ="`".$key."` ".$value['type'];

            $null = ' NOT NULL ';
            $comment = '';
            $size = '';
            if($value['size']!=0){
                $size = '('.$value['size'].')';
            }
            $extra = '';
            if($value['unsign']==1){
                $extra = ' unsigned ';
            }

            if($value['comment']){
                $comment = "COMMENT '".$value['comment']."'";
            }

            $arr[]=$begin.$size.$extra.$null.$comment;

        }
        $time = self::$time;
        $arr[] = "`".$config['update_at']."` int(11) NOT NULL COMMENT '{$time['update_at']}'";
        $arr[] = "`".$config['create_at']."` int(11) NOT NULL COMMENT '{$time['create_at']}'";

        $arr[] = "PRIMARY KEY (`".$config['main_key']."`)";

        foreach ($indexs as $key => $value) {
            $type = '';
            switch ($value['type']) {
                case 'FULLTEXT':
                    $type = 'FULLTEXT KEY ';
                    break;
                case 'KEY':
                    $type = 'KEY ';
                    break;
                case 'UNIQUE':
                    $type = 'UNIQUE KEY ';
                    break;
                default:
                    # code...
                    break;
            }
            if(!$type){
                continue;
            }

            $column = str_replace(',','`,`',$value['column']);
            $arr[] = $type . "`".$key."` (`" .$column."`)";
        }

        $str = implode(",\n\t", $arr);
        $sql = "CREATE TABLE `".$table_name."` (\n";
        $sql .= "\t".$str;
        $sql .= "\n) ENGINE=".$table['engine']." DEFAULT CHARSET=".$config['charset']." COLLATE=".$config['collate']." COMMENT '".$table['comment']."';\n\n";
            // print_r($sql);
        return $sql;
    }

    public static function tableEqual()
    {
        $table = self::$table;
        $equal = self::$equal;
        $config = self::$config;
        if($table['Engine']!=$equal['engine'] || $table['Collation']!=$config['collate'] || $table['Comment']!=$equal['comment']){
            self::$sqls[] = "ALTER TABLE `{$table['Name']}` ENGINE={$equal['engine']} DEFAULT COLLATE={$config['collate']} COMMENT='{$equal['comment']}'";
        }
    }
    public static function columnEqual()
    {
        $table = self::$table;
        $equal = self::$equal;
        $config = self::$config;
        $column = self::$columns;
        $co = array_column($column, 'Field');
        $after = $config['main_key'];
        $arr = [];
        $time = self::$time;
        $equal['column'][$config['update_at']] = ['comment'=>$time['update_at'], 'type'=>'int','size'=>'11', 'unsign'=>'0'];
        $equal['column'][$config['create_at']] = ['comment'=>$time['create_at'], 'type'=>'int','size'=>'11', 'unsign'=>'0'];
        foreach ($equal['column'] as $key => $value) {
            $status = 1;
            if(in_array($key,$co)){
                $num = array_search($key, $co);
                $temp = $column[$num];
                unset($co[$num]);

                $type = $temp['Type'];
                $type = explode(" ", $type);
                $unsign = isset($type[1])?1:0;
                preg_match_all("/([^\(]+)\(([^\)]+)\)/", $type[0], $arr);
                $type = $arr[0]?$arr[1][0]:$type[0];
                $size = $arr[0]?$arr[2][0]:0;

                if($value['comment']!=$temp['Comment']
                    ||($temp['Collation']!=$config['collate']&&!$config['collate'])
                    ||$temp['Default']!=''
                    ||$temp['Null']!='NO'
                    ||$value['type']!=$type
                    ||$value['size']!=$size
                    ||$value['unsign']!=$unsign){
                    $status = 0;
                }
            }else{
                $status = -1;
            }
            $now = $after;
            $after = $key;

            if($status==1){
                continue;
            }
            $str = $status==0?'MODIFY':'ADD';
            $size = $value['size']?'('.$value['size'].')':'';
            $unsign = $value['unsign']?'unsigned ':'';
            self::$sqls[] = "ALTER TABLE `{$table['Name']}` {$str} COLUMN `{$key}` {$value['type']}{$size} {$unsign}NOT NULL COMMENT '{$value['comment']}' AFTER `{$now}`";

        }
        $num = array_search($config['main_key'],$co);
        unset($co[$num]);

        foreach ($co as $value) {
            self::$sqls[] = "ALTER TABLE `{$table['Name']}` DROP COLUMN `{$value}`";
        }

    }
    public static function indexEqual()
    {
        $table = self::$table;
        $equal = self::$equal;
        $config = self::$config;
        $index = self::getIndex(1);

        foreach ($equal['index'] as $key => $value) {
            $status = 1;
            if(array_key_exists($key, $index)){
                $type = explode(' ',$index[$key]['type']);
                $type = $type[0];
                $data = explode(',',$value['column']);
                if($value['type']!=$type||$data!=$index[$key]['data']){
                    $status = 0;
                }
            }else{
                $status = -1;
            }
            unset($index[$key]);
            if($status==-1 || $status==0){
                $column = str_replace(',','`,`',$value['column']);
                $type = $value['type']=='KEY'?$value['type']:$value['type'].' KEY';
                self::$sqls[] = "ALTER TABLE `{$table['Name']}` ADD {$type} `{$key}` (`$column`)";
            }
            if($status==0){
                self::$sqls[] = "ALTER TABLE `{$table['Name']}` DROP INDEX `{$key}`";
            }
        }

        foreach ($index as $key => $value) {
            self::$sqls[] = "ALTER TABLE `{$table['Name']}` DROP INDEX `{$key}`";
        }

    }

    public static function tableContrast()
    {
        self::$sqls = [];
        $local = self::$local;
        $local_table = $local['table'];
        $table = self::$table;

        $table_name = str_replace($local['pref'],'',$table['Name']);
        self::$equal = $local_table[$table_name];
        self::$table_name = $table_name;

        self::tableEqual();
        self::columnEqual();
        self::indexEqual();

        $re = implode(";\n",self::$sqls);

        return $re;
    }

    public static function setLocal($local)
    {
        self::$local = $local;
    }

    public static function data($data)
    {
        if(!$data){
            return "\n\n";
        }
        $table = self::$table['Name'];
        $sql = "INSERT INTO `$table` VALUES ";
        foreach ($data as $key => $value) {
            $sql .= '("';
            $sql .= implode('","',$value);
            $sql .= "\"),\n";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ";\n\n";
        return $sql;
    }

    public static function table($table)
    {
        self::$table = $table;
    }
    public static function columns($columns)
    {
        self::$columns = $columns;
    }
    public static function indexs($indexs)
    {
        self::$indexs = $indexs;
    }

    public static function service()
    {
        $table = self::$table;
        $columns = self::$columns;
        $indexs = self::$indexs;
        $config = self::$config;

        $temp = [];
        if(!$columns){
            return '';
        }
        foreach ($columns as $value) {
            if($value['Field']==$config['main_key'] || $value['Field']==$config['create_at'] || $value['Field']==$config['update_at']){
                continue;
            }
            $type = $value['Type'];
            $type = explode(" ", $type);
            $unsign = isset($type[1])?1:0;
            preg_match_all("/([^\(]+)\(([^\)]+)\)/", $type[0], $arr);
            $type = $arr[0]?$arr[1][0]:$type[0];
            $size = $arr[0]?$arr[2][0]:0;
            $temp[] = "'{$value['Field']}'=>['comment'=>'{$value['Comment']}', 'type'=>'{$type}','size'=>'{$size}', 'unsign'=>'$unsign']";
        }
        $space = self::nextLine(16);
        $str = implode(",\n".$space, $temp);
        // $str = $space . $str;

        $temp = [];
        foreach ($indexs as $v) {
            if($v['Key_name'] == 'PRIMARY'){
                continue;
            }
            $name = $v['Key_name'];
            $temp[$name]['name'] = $name;
            $temp[$name]['data'][] = $v['Column_name'];
            if($v['Index_type'] == 'FULLTEXT'){
                $temp[$name]['type'] = 'FULLTEXT';
            }else{
                if($v['Non_unique'] == 1){
                    $temp[$name]['type'] = 'KEY';
                }else{
                    $temp[$name]['type'] = 'UNIQUE';
                }
            }
        }
        $arr = [];
        foreach ($temp as $value) {
            $column = implode(',', $value['data']);
            $arr[] = "'{$value['name']}'=>['type'=>'{$value['type']}', 'column'=>'{$column}']";
            # code...
        }
        $index = implode(",\n".$space, $arr);
        $index = $index?"\n".$space.$index."\n".self::nextLine(12):'';
        // $index = $space . $index;
        $name = str_replace(self::$pref, '', $table['Name']);
$data = <<<EXT

        '{$name}'=>[
            'comment'=>'{$table['Comment']}',
            'engine'=>'{$table['Engine']}',
            'column'=>[
                {$str}
            ],
            'index'=>[{$index}]
        ],
EXT;
        return $data;
    }

    public static function pref($tables)
    {
        if(!isset($tables[1])){
            return false;
        }
        $pref = '';
        $len = strlen($tables[0]);
        $num = count($tables);
        for ($i=0; $i < $len; $i++) {
            if ($tables[0][$i] == $tables[1][$i] && $tables[0][$i] == $tables[$num-1][$i]) {
                $pref .= $tables[0][$i];
            }else{
                break;
            }
        }
        self::$pref = $pref;
    }
    public static function end()
    {
        $data = <<<EXT

    ]
];
EXT;
        return $data;
    }
    public static function start($db_name)
    {
        $time = date('Y-m-d',TIME);
        $data = '';
        $pref = self::$pref;
        $config = self::$config;
        $data = <<<EXT
<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       {$time}
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  数据库内容信息文件（此表自动维护）
+----------------------------------------------------------------------
*/

return [
    'schema'=>'{$db_name}',
    'charset'=>'{$config['charset']}',
    'collate'=>'{$config['collate']}',

    'pref'=>'{$pref}',
    'main_key'=>'{$config['main_key']}',
    'create_at'=>'{$config['create_at']}',
    'update_at'=>'{$config['update_at']}',

    'table'=>[
EXT;
return $data;
// echo $data;
// echo self::nextLine(10);

    }


    public static function nextLine($num)
    {
        $str = str_repeat(" ",$num);
        return $str;
    }

    public static function sql()
    {
        $table = self::$table;
        $columns = self::$columns;
        $config = self::$config;
        $key = '';
        $arr = [];
        // print_r(self::$table);
        // print_r(self::$columns);
        foreach ($columns as $value) {
            # code...
            $begin ="`".$value['Field']."` ".$value['Type'];
            if($value['Key']=='PRI'){
                // $arr[] = $begin." NOT NULL ".$value['Extra'];
                $key = $value['Field'];
            }

            $null = ' NULL';
            $default = '';
            $comment = '';
            $collation = '';
            $extra =$value['Extra'];
            if($value['Collation']){
                $collation = ' CHARACTER SET utf8 COLLATE '.$value['Collation'];
            }
            if($value['Null']=='NO'){
                $null = ' NOT NULL ';
            }
            if(!is_null($value['Default'])){
                $default = " DEFAULT '".$value['Default']."'";
            }
            if($value['Comment']){
                $comment = " COMMENT '".$value['Comment']."'";
            }
            if($value['Type']=='timestamp' && !$null){
                $null = ' NULL';
            }

            $arr[]=$begin.$collation.$null.$extra.$default.$comment;
        }

        $arr[] = "PRIMARY KEY (`".$key."`)";

        $keys = self::getIndex();
        $arr = array_merge($arr,$keys);

        $str = implode(",\n\t", $arr);

        $sql = "CREATE TABLE IF NOT EXISTS `".$table['Name']."` (\n";
        $sql .= "\t".$str;
        $sql .= "\n) ENGINE=".$table['Engine']." DEFAULT COLLATE=".$table['Collation']." COMMENT '".$table['Comment']."';\n\n";

        return $sql;
    }
    public static function getIndex($no_link = 0)
    {
        // BTREE, FULLTEXT, HASH, RTREE

        $arr = [];

        if(self::$indexs){
            $temp = [];
            foreach (self::$indexs as $v) {
                if($v['Key_name']=='PRIMARY'){
                    continue;
                }
                $name = $v['Key_name'];
                $temp[$name]['name'] = $name;
                $temp[$name]['data'][] = $v['Column_name'];
                if($v['Index_type'] == 'FULLTEXT'){
                    $temp[$name]['type'] = 'FULLTEXT KEY ';
                }else{
                    if($v['Non_unique'] == 1){
                        $temp[$name]['type'] = 'KEY ';
                    }else{
                        $temp[$name]['type'] = 'UNIQUE KEY ';
                    }
                }
            }

            foreach ($temp as $v) {
                $keys = implode('`,`',$v['data']);
                $arr[] = $v['type'] . "`".$v['name']."` (`" .$keys."`)";
            }
        }
        if($no_link!=0){
            $arr = $temp;
        }
        return $arr;
    }

}