<?php
namespace Common\Model;
use Common\Model\PubModel;

class ShopApiModel extends PubModel {
    //获得店铺列表
    public function getInfo($id) {
        return $this->apiexec->goApi('v0/shop/'.$id, array(), 'get');
    }

    //获得用户的auth 信息
    public function authinfo($id){
        return $this->apiexec->goApi('v0/shop/authinfo', array('shop_id' => $id), 'get');
    }

}
