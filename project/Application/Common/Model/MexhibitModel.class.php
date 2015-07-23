<?php
namespace Common\Model;
use Think\Model\MongoModel;

class MexhibitModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function createNew($data) {
        //$data['sort'] = 0;
        $data['_id'] = md5($data['id']);
        $this->add($data);
    }
     
    public function del($id='') {
        $filter = array('_id' => $id);
        $res = $this->where($filter)->delete();
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='sort asc') {
        $limit = ($page - 1) * $limit . "," . $limit;
        //$res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        $res = $this->where($filter)->limit($limit)->order($order)->select();
        return $res;
    }

    public function getAll($filter, $order='sort asc') {
        $res = $this->where($filter)->order($order)->select();
        return $res;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

}
