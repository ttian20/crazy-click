<?php
namespace Common\Model;
use Think\Model;
class ProductQuotaModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='create_time desc') {
        $res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $res;
    }

    public function saveData($data) {
        if($this->create($data)){
            if(isset($data['id']) && $data['id']){
                $this->save();
                $id = $data['id'];
            }else{
                $id = $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功','data' => array('id' => $id));
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

    public function del($filter) {
        if($this->where($filter)->delete()){
            return array('status' => 'success', 'msg' => '删除成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
