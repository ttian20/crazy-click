<?php
namespace Common\Model;
use Think\Model;
class CouponsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

    public function getAll($filter, $order='create_time desc') {
        $res = $this->where($filter)->order($order)->select();
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='create_time desc') {
        $res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $res;
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
                $id = $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => array('id' => $id));
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

    public function del($id) {
        if($this->where(array('id' => $id))->save(array('status' => '2', 'update_time' => time()))){
            return array('status' => 'success', 'msg' => '删除成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
