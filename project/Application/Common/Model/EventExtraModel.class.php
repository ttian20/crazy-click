<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventExtraModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id='') {
        $filter = array('_id' => (int)$id);
        $res = $this->where($filter)->find();
        return $res;
    }

    public function del($id='') {
        $filter = array('_id' => (int)$id);
        $res = $this->where($filter)->delete();
        return $res;
    }

    public function saveData($params) {
        $data_id = isset($params['_id'])?$params['_id']:'';
        if($params['is_prize'] == 'off'){
            $params['prize_data'] = array();
        }else{
            $prizes = array();
            $i = 0;
            foreach($params['prize_data'] as $pd){
                $prizes[$i] = $pd;
                $i++;
            }
            $params['prize_data'] = $prizes;
        }

        if($data_id && $this->findById($data_id)){
            foreach($params as $k=>&$p){
                if($k != '_id'){
                   $p = array('set', $p);
                }
            }
            $this->create($params);
            $this->save();
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => array('id' => $params['_id']));
        }else{
            $this->create($params);
            $this->add();
            return array('status' => 'success', 'msg' => '创建成功', 'data' => array('id' => $params['_id']));
        }
    }
}
