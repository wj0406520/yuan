<?php
/*
+----------------------------------------------------------------------
| author     王杰
+----------------------------------------------------------------------
| time       2018-05-03
+----------------------------------------------------------------------
| version    4.0.1
+----------------------------------------------------------------------
| introduce  地理位置工具
+----------------------------------------------------------------------
*/
namespace core\tool;

class Distence
{

    /**
    * @desc 根据两点间的经纬度计算距离
    * @param float $lat 纬度值
    * @param float $lng 经度值
    * @return  int 距离单位是米
    */
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {

        $earthRadius = 6367000; //approximate radius of earth in meters


        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;

        $calcLatitude = $lat2 - $lat1;

        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);

        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));

        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    /**
    * @desc 根据一个点的经纬度计算出正方形的点
    * 有一点误差(几米的误差)
    * @param float $lat 纬度值
    * @param float $lng 经度值
    * @param int $raidus 距离单位是米
    * @return array
    */
    public static function getLocation( $lat, $lng, $raidus =5000)
    {
        $M_PI =pi();
        $EARTH_RADIUS = 6378137;
        $RAD = $M_PI / 180.0;
        $latitude = $lat;
        $longitude = $lng;
        $degree = (24901 * 1609) / 360.0;
        $raidusMile = $raidus;
        $dpmLat = 1 / $degree;
        $data = [];
        $radiusLat = round($dpmLat * $raidusMile, 6);
        $minLat = $latitude - $radiusLat;
        $maxLat = (float)$latitude + (float)$radiusLat;
        $data["max_lat"] = $maxLat;
        $data["min_lat"] = $minLat;
        $mpdLng = $degree * cos($latitude * ($M_PI / 180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = round($dpmLng * $raidusMile, 6);
        $minLng = $longitude - $radiusLng;
        $maxLng = (float)$longitude + (float)$radiusLng;
        $data["max_lng"] = $maxLng;
        $data["min_lng"] = $minLng;
        return $data;
    }

    /**
    * @desc 根据一个点的经纬度计算出正方形的点
    * 公式问题 lng有误差（几百米的误差）
    * 自己写的以供参考
    * @param float $lat 纬度值
    * @param float $lng 经度值
    * @param int $distance 距离单位是米
    * @return array
    */
    public static function getLocation1($lat1, $lng1,$distance = 5000)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        $stepTwo = $distance / $earthRadius;

        $stepOne = pow(sin($stepTwo/2),2);

        $lat=$lat1;
        $lng=$lng1;

        $lat2 = ($lat1 * pi() ) / 180;
        $lng2 = ($lng1 * pi() ) / 180;
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $x = $lat2 - $lat1;
        $y = $lng2 - $lng1;

        $calcLongitude1 = asin(sqrt(($stepOne - pow(sin($x / 2), 2) )) / (cos($lat1) * cos($lat2))) * 2;

        $calcLongitude2 = asin(sqrt($stepOne - pow(sin($x / 2), 2) / (cos($lat1) * cos($lat2) )))* 2;

        $calcLongitude = ($calcLongitude1+$calcLongitude2)/2;

        $calcLatitude= 2*asin(sqrt($stepOne-cos($lat1) * cos($lat2) * pow(sin($y / 2), 2)));

        $lng2 = (($calcLongitude + $lng1) * 180) / pi();
        $lat2 = (($calcLatitude + $lat1) * 180) / pi();


        // return round($lat2,6);
        // return round($lng2,6);
        $arr['max_lat'] = round($lat2,6);
        $arr['min_lat'] = $lat*2-round($lat2,6);

        $arr['max_lng'] = round($lng2,6);
        $arr['min_lng'] = $lng*2-round($lng2,6);

        return $arr;
    }


    public function text()
    {

        // $lat2=32.052794;
        // $lat1=118.791112;
        // $lng2=31.983964;
        // $lng1=118.782156;


        // $stepOne = pow(sin(($lat2 - $lat1) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng2 - $lng1) / 2), 2);


        // $stepOne = (1-cos($lat2 - $lat1))/2 +cos($lat1) *cos($lat2)*(1-cos($lng2 - $lng1))/2;
        $a=DistenceTool::getLocation(52.502897,122.444305);
        print_r($a);
    // [max_lat] => 52.547891
    // [min_lat] => 52.457903
    // [max_lng] => 122.527516
    // [min_lng] => 122.361094
        $a=DistenceTool::getLocation(52.502897,122.444305);
        print_r($a);

    // [maxLat] => 52.547823
    // [minLat] => 52.457971
    // [maxLng] => 122.518109
    // [minLng] => 122.370501
    // getAround
        $a=DistenceTool::getLocation(32.052794,118.791112);
        print_r($a);
    // [max_lat] => 32.097788
    // [min_lat] => 32.0078
    // [max_lng] => 118.844927
    // [min_lng] => 118.737297
        $a=DistenceTool::getLocation(18.405459,109.631476);
        print_r($a);
    // [max_lat] => 18.450453
    // [min_lat] => 18.360465
    // [max_lng] => 109.678961
    // [min_lng] => 109.583991

        $a=DistenceTool::getDistance(18.405459,109.631476,18.360465,109.631476);
        $a=DistenceTool::getDistance(52.502897,122.444305,52.547823,122.444305);
    // cos($lat1) *(1-cos($lng2 - $lng1))/2=($stepOne*2-(1-cos($lat2 - $lat1)))/(2*cos($lat2));
    // 0.53380516812793
    // 0.53380516812793
        // echo $stepOne;
        echo $a;

    }
}