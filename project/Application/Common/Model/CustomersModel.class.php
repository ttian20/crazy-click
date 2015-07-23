<?php
namespace Common\Model;
use Think\Model;
class CustomersModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function createNew($data) {
        $id = $this->add($data);
        $data['id'] = $id;
        return $data;
    }

    public function findByUserid($userid, $type = 'taobao') {
        $filter = array('thirdparty_uid' => $userid, 'thirdparty_platform' => $type);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function findByUidNoShopId($userid, $type = 'taobao') {
        $filter = array('thirdparty_uid' => $userid, 'thirdparty_platform' => $type, 'shop_id' => 0);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function saveData($data) {
        if(isset($data['id']) && $data['id']){
            unset($data['create_time']);
        }
        if($this->create($data)){
            if(isset($data['id']) && $data['id']){
                $this->save();

                $id = $data['id'];
            }else{
                $id=$this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => $id);
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }
}
