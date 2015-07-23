<?php
namespace Common\Model;
use Think\Model;
class WeixinModel extends Model {

    public function getByShopId($shopId=''){
        $filter['shop_id'] = $shopId;
        return $this->where($filter)->find();
    }

    public function saveData($shop_id, $data){
        $data['update_at'] = time();        

        $filter['shop_id'] = $shop_id;
        $wx = $this->where($filter)->find();
        if($wx){
            $data['id'] = $wx['id'];
            $this->create($data);
            $this->save();
        }else{
            $data['create_at'] = time();
            $this->create($data);
            $this->add();
        }
        $return = array('success' => 'true', 'msg' => '操作成功');

        return $return;

    }

     

}
