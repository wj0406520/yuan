<?php

/**
 * shop模版
 */

namespace app\admin\dao;

class AddressDao extends AllDao
{


    public function getList($count=0)
    {
        $this->setRequest(['pagesize'=>30]);
        $data = $this->request();

        $re = M(Sight::class)
        ->page($data)
        ->orderDesc()
        ->select($count);

        return $re;
    }

    public function info()
    {
        $data = $this->request();

        return  M(Sight::class)->find($data['id']);
    }

    public function postAddress($arr)
    {

        if($arr['latitude']){
            $data = $this->getAddress($arr['latitude'],$arr['longitude']);
//             $data = json_decode($data,true);
//             $data = $data['result']['addressComponent'];
// // print_r($data);
//             $arr['province'] = $data['province'];
//             $arr['city'] = $data['city'];
//             $arr['district'] = $data['district'];
//             $arr['province_id'] = intval($data['adcode']/10000).'0000';
//             $arr['city_id'] = intval($data['adcode']/100).'00';
//             $arr['district_id'] = $data['adcode'];
        }else{
            unset($arr['latitude']);
            unset($arr['longitude']);
        }

        M(Sight::class)->data($arr)->save();
    }


    private function getAddress($lat,$lng){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://apis.map.qq.com/ws/geocoder/v1/?location={$lat}%2C{$lng}&output=json&key=UVIBZ-5636O-ZL2W7-SVCR3-VESEH-B7BVJ",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "postman-token: 5ed5132b-c8b2-2515-6811-4213aa94701c"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}