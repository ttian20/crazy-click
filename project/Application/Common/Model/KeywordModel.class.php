<?php
namespace Common\Model; 
use Think\Model;

class KeywordModel extends Model {

    public function getRow($id = 0){
        $filter = array('id' => $id);
        return $this->where($filter)->find();
    }

    public function getList($shopId='', $msg_keyword = ''){
        $filter = array('shop_id' => $shopId);
        if($msg_keyword){
            $filter['msg_keyword'] = $msg_keyword;
        }
        return $this->where($filter)->select();
    }

    public function saveData($data){
        /*
        foreach ($data as $k => &$v) { 
            $v = mysql_real_escape_string($v); 
        }
         */

        $data['update_at'] = time();        
        $id = isset($data['id'])?$data['id']:'';
        if($id){
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

    /**
    * 验证关键词唯一性
    * $params auto_id,msg_keyword
    */
    function checkKeyword($arg) {

        $filter['shop_id'] = $arg['shop_id'];
        $filter['msg_keyword'] = $arg['msg_keyword'];
        $results = $this->where($filter)->select();

        $count = count($results);
        if($count==1){
            if($arg['id'] != $results[0]['id']){
                return false;
            }
        }elseif($count>1){
            return false;
        }
        return true;
    }


    public function deleteById($id){
        $filter = "id = {$id}";
        return $this->where($filter)->delete();
    }

}
