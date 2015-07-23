<?php
namespace Common\Model;
use Think\Model;

class SupplierPostageTemplateModel extends Model {

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function saveData($data) {
        if($this->create($data)){
            if($data['id']){
                $this->save();
                $id = $data['id'];
            }else{
                $id = $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => array('id' => $id));
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
