<?php

namespace app\api\dao;
use core\tool\Distence;


/**
*
*/
class IndexDao extends AllDao
{

	public function getList($arr)
	{
		// $this->table('sight');

		$m = M(Sight::class);
		$name = $arr['name'];
		$field = 'id,name,latitude,longitude,level,province';
		$where = [
		     'is_show'=>0
		];
		if($name){
			$where['name'] = ['like'=>$name];
			$field .=',"0" distance';
			$m->limit(20);
		}else{
			if($arr['latitude']==0){
				$field = 'id,name,level,province,"0" distance';
				$where = ['level'=>5];
				$m->limit(20);
			}else{
				$re = Distence::getLocation($arr['latitude'],$arr['longitude'],200000);
				$m->where(['latitude'=>['>',$re['min_lat']]]);
				$m->where(['latitude'=>['<',$re['max_lat']]]);
				$m->where(['longitude'=>['>',$re['min_lng']]]);
				$m->where(['longitude'=>['<',$re['max_lng']]]);
			}
		}

		$list = $m->field($field)
			->where($where)->orderDesc('level desc,sort ')->fetchSql(0)->select();

		if($arr['latitude']==0){
			$data['list'] = $list;
			return $data;
		}
		$data['min']=[];
		$data['mid']=[];
		$data['max']=[];
		$data['list']=[];
		foreach ($list as $key => $value) {
			$d= Distence::getDistance($value['latitude'],$value['longitude'],$arr['latitude'],$arr['longitude']);
			$d=intval($d/1000);
			unset($value['latitude']);
			unset($value['longitude']);
			$value['distance']=$d;
			if($name){
				$data['list'][]=$value;
			}
			if($d==0 && $arr['latitude']==0) continue;
			if($d<50){
				$data['min'][]=$value;
				continue;
			}
			if($d<100){
				$data['mid'][]=$value;
				continue;
			}
			if($d<200){
				$data['max'][]=$value;
				continue;
			}
		}
		$data['min'] = $this->arraySort($data['min'],'distance');
		$data['mid'] = $this->arraySort($data['mid'],'distance');
		//$data['max'] = $this->arraySort($data['max'],'distance');
		$data['list'] = $this->arraySort($data['list'],'distance');
		$data['max']=[];
		return $data;
	}

	public function getInfo($where)
	{
		return  M(Sight::class)->field('name,longitude,latitude')->where($where)->getOne();
	}

	private function arraySort($arr, $keys)
	{
	    if (!is_array($arr)) {
	        return false;
	    }
	    $keysvalue = array_column($arr, $keys);
	    asort($keysvalue);
	    $new_array = [];
	    foreach ($keysvalue as $key => $value) {
	    	$new_array[] = $arr[$key];
	    }
	    return $new_array;
	}
}