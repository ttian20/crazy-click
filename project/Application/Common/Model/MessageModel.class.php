<?php
namespace Common\Model;
use Think\Model;
class MessageModel extends Model {

    public function getRow($shop_id, $act){
        $filter['shop_id'] = $shop_id;
        $filter['msg_type'] = $act;
        return $this->where($filter)->find();
    }

    public function saveData($shop_id, $data){
       
        /*
        foreach ($data as $k => &$v) {
            $v = mysql_real_escape_string($v);
        }
         */
        $data['update_at'] = time();        
        $filter['shop_id'] = $shop_id;
        $filter['msg_type'] = $data['msg_type'];
        $msg = $this->where($filter)->find();
        if($msg){
            $data['id'] = $msg['id'];
            unset($data['msg_type']);
            unset($data['shop_id']);
            $this->create($data);
            $this->save();
        }else{
            $data['create_at'] = time();
            $this->create($data);
            $this->add();
        }
        $return = array('status' => 'success', 'msg' => '操作成功');

        return $return;

    }
}
