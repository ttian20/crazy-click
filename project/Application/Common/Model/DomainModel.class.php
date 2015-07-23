<?php
namespace Common\Model;
use Think\Model;
class DomainModel extends Model {

    public function findByShopId($shopId='') {
        $filter = array('shop_id' => $shopId);
        $domain = $this->where($filter)->find();
        return $domain;
    }

    public function saveData($data) {
        if($this->create($data)){
            $this->save();
            return array('status' => 'success', 'msg' => '修改保存成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
