<?php
namespace Common\Model;
use Think\Model;
class ShopSettingModel extends Model {

    public function findById($id='') {
        $filter = array('id' => $id);
        $res = $this->where($filter)->find();
        return $res;
    }

    public function findByKey($shop_id='', $key='') {
        $filter = array('shop_id' => $shop_id, 'key' => $key);
        $res = $this->where($filter)->find();
        return $res;
    }

    public function saveData($data) {
        $res = $this->findByKey($data['shop_id'], $data['key']);
        if($res){
            $data['id'] = $res['id'];
        }
        if($this->create($data)){
            if($res){
                $this->save();
            }else{
                $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
