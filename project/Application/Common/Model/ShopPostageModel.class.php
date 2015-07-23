<?php
namespace Common\Model;
use Think\Model;
class ShopPostageModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function findById($id='') {
        $filter = array('id' => $id);
        $res = $this->where($filter)->find();
        return $res;
    }

    public function saveData($data) {
        if($this->create($data)){
            if($data['id']){
                $res = $this->save();
            }else{
                $res = $this->add();
            }
            if($res){
                return array('status' => 'success', 'msg' => '修改保存成功');
            }else{
                return array('status' => 'fail', 'msg' => $this->getError());
            }
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
