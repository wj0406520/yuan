<?php

namespace app\api\controls;

use service\http\weixin;
use service\http\Alipay;

use core\tool\HttpTool;

class Index extends All
{
        public $handle = [
                'list'=>['latitude','longitude','name','token'],
                'info'=>['id']
        ];


        public function listAction()
        {
                $data = $this->request();


                $this->dao('login')->getOpenid($data['token']);
                $this->dao();
                $re = $this->dao->getList($data);
                // $this->success($re);
                $this->setValue($re);
                $this->display();
        }



        public function infoAction()
        {
                $data = $this->request();


                $re = $this->dao->getInfo($data);

                $this->setValue($re);
                $this->display();
        }

}