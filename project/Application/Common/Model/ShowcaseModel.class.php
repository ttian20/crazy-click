<?php
namespace Common\Model;
use Think\Model;

class ShowcaseModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        if(!$row){
            $row = array();
        }
        return $row;
    }

    public function saveData($data) {
        if($this->create($data)){
            if(isset($data['id']) && $data['id']){
                $this->save();
                return array('status' => 'success', 'msg' => '修改保存成功');
            }else{
                //$data['shop_id'] = $this->_genShopId(); 
                $this->add();
                return array('status' => 'success', 'msg' => '创建成功');
            }
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
