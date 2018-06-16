<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-06-8
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  修改数据库信息
+----------------------------------------------------------------------
*/
namespace bin\lib;
class AlterTable
{
    protected static $m_columns=[];
    protected static $s_columns=[];
    protected static $table = '';
    protected static $arr = [];


    public static function table($table)
    {
        self::$table = $table;
    }

    public static function columns($m_columns,$s_columns)
    {
        self::$s_columns = $s_columns;
        self::$m_columns = $m_columns;
    }

    public static function sql()
    {
        self::$arr=[];
        $s_columns = self::$s_columns;
        $m_columns = self::$m_columns;

        $m_arr = array_column($m_columns, 'Field');
        $s_arr = array_column($s_columns, 'Field');

        $up = '';

        foreach ($m_arr as $key=>$value) {
            # code...
            $index = array_search($value, $s_arr);
            if($index===false){
                self::addColumn($m_columns[$key],$up);
            }else{
                self::changeColumn($m_columns[$key],$s_columns[$key]);
            }
            $up = $value;
        }

        foreach ($s_arr as $key => $value) {
            $index = array_search($value, $m_arr);
            if($index===false){
                self::delColumm($s_columns[$key]);
            }
        }
        $arr = self::$arr;
        $sql = '';
        if($arr){
            $sql = "ALTER TABLE `".self::$table."` \n";
            $str = implode(",\n", $arr);
            $sql .= $str.";\n";
        }

        return $sql;
    }
    protected static function delColumm($value)
    {
        $str = "DROP COLUMN `".$value['Field']."`";
        self::$arr[] = $str;
    }

    protected static function changeColumn($m_colume,$s_column)
    {
        // $result=array_diff_assoc($m_colume,$s_column);
        $result = 0;
        foreach ($m_colume as $key => $value) {
            if($value!==$s_column[$key]){
                $result = 1;
            }
        }
            // print_r($s_column);
            // exit;

        if(!$result){
            return '';
        }

        $sql = self::linkSql($m_colume);
        $str = "CHANGE COLUMN `".$m_colume['Field']."` `".$m_colume['Field']."` ".$sql;

        self::$arr[] = $str;
    }


    protected static function addColumn($value,$location_str)
    {
        $location = ' FIRST';

        if($location_str){
            $location = " AFTER ".$location_str;
        }
        $sql = self::linkSql($value);
        $str = "ADD COLUMN `".$value['Field']."` ". $sql .$location;
        self::$arr[] = $str;

    }


    protected static function linkSql($value)
    {
        $null = ' NULL ';
        $default = '';
        $comment = '';
        $collation = '';
        $extra =$value['Extra'];
        if($value['Collation']){
            $collation = ' COLLATE '.$value['Collation'];
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
            $null = ' NULL ';
        }
        $sql = $value['Type'].$collation.$null.$extra.$default.$comment;
        return $sql;
    }
}


// ALTER TABLE `text`.`j_order` ADD COLUMN `ii` VARCHAR(45) NULL AFTER `kk`;
// ALTER TABLE `text`.`j_order` ADD COLUMN `ll` INT UNSIGNED NOT NULL DEFAULT 11 AFTER `ii`;
// ALTER TABLE `text`.`j_order` ADD COLUMN `mm` VARCHAR(45) NOT NULL COMMENT 'Ccaa' AFTER `ll`;
// ALTER TABLE `text`.`j_order` ADD COLUMN `nn` VARCHAR(45) CHARACTER SET 'utf8mb4' NOT NULL COMMENT 'aa' AFTER `mm`;

// ALTER TABLE `text2`.`j_shop` DROP COLUMN `is_used`;

// ALTER TABLE `text2`.`j_shop` CHANGE COLUMN `is_delete` `name` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '是否删除0不删除1删除' ;
// ALTER TABLE `text2`.`j_shop` CHANGE COLUMN `money1` `money1` INT(1) NOT NULL DEFAULT '0' COMMENT '商品价格（分）' ;
