<?php
namespace Common\Model;
use Think\Model;
class SmsLogModel extends Model {

    public function getRow($filter){
        $res = $this->where($filter)->find();
        return $res;
    }

    public function getCount($filter){
        $res = $this->where($filter)->count();
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='create_time dsec'){
        $res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $res;
    }

    public function saveData($data) {
        if($this->create($data)){
            $this->save();
            return array('status' => 'success', 'msg' => '修改保存成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
