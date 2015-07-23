<?php
namespace Common\Model;
use Think\Model;
class NoticeModel extends Model {

    public function findById($id='') {
        $filter = array('id' => $id);
        $doc = $this->where($filter)->find();
        return $doc;
    }

    public function getCount() {
        $res = $this->count();
        return $res;
    }

    public function getLast($order='create_time desc') {
        $res = $this->order($order)->find();
        return $res;
    }

    public function getList($limit=10, $page=1, $order='create_time desc') {
        $res = $this->limit($limit)->page($page)->order($order)->select();
        return $res;
    }

    public function saveData($data) {
        if(isset($data['id']) && $data['id']){
            unset($data['create_time']);
        }
        if($this->create($data)){
            if(isset($data['id']) && $data['id']){
                $this->save();
            }else{
                $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

    public function del($id) {
        if($this->where(array('id' => $id))->delete()){
            return array('status' => 'success', 'msg' => '修改保存成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
