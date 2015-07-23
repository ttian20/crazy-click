<?php
namespace Common\Model;
use Think\Model\MongoModel;

class MshareModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function createNew($data) {
        $data['sort'] = 0;
        $data['_id'] = $data['coupon_id'];
        $this->add($data);
    }

    public function del($id='') {
        $filter = array();
        if($id){
            $filter = array('_id' => $id);
        }
        $res = $this->where($filter)->delete();
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='create_time desc') {
        $limit = ($page - 1) * $limit . "," . $limit;
        //$res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        $res = $this->where($filter)->limit($limit)->order($order)->select();
        return $res;
    }

    public function getAll($filter, $order='create_time desc') {
        $res = $this->where($filter)->order($order)->select();
        return $res;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

    public function saveData($params) {
        $data_id = isset($params['_id'])?$params['_id']:'';
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
