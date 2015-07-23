<?php
namespace Common\Model;
use Think\Model;
class ShopBankcardModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function saveData($data) {
        $shop_id = $data['shop_id'];
        $card_info = $this->getRow(array('shop_id' => $shop_id));
        if($card_info){
            $data['id'] = $card_info['id'];
            unset($data['create_time']);
        }
        if($this->create($data)){
            if($card_info){
                $this->save();
                return array('status' => 'success', 'msg' => '修改保存成功');
            }else{
                $this->add();
                return array('status' => 'success', 'msg' => '创建成功');
            }
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
