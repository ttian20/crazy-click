<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventPlusModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function findByCustomer($customerId, $eventId) {
        $_id = md5($eventId . '-' . $customerId);
        return $this->findById($_id);
    }

    public function createNew($data) {
        $current = time();
        $params = array(
            'event_id' => (int)$data['event_id'],
            'customer_id' => (int)$data['customer_id'],
            'total' => 0,
            'nickname' => $data['nickname'],
            'mobile' => $data['mobile'],
            'create_time' => $current,
            'update_time' => $current,
        );
        $_id = md5($params['event_id'] . '-' . $params['customer_id']);
        $params['_id'] = $_id;
        $this->add($params);
    }

    public function plus($customerId, $eventId) {
        $_id = md5($eventId . '-' . $customerId);
        $plus = $this->findById($_id);
    }

    public function del($id='') {
        $filter = array('_id' => $id);
        $res = $this->where($filter)->delete();
        return $res;
    }

    public function topList($eventId, $all = false) {
        $filter = array('event_id' => $eventId);
        if ($all) {
            $res = $this->where($filter)->order('total desc, update_time desc')->select();
        }
        else {
            $res = $this->where($filter)->order('total desc, update_time desc')->limit(0, 50)->select();
        }
        return $res;
    }
    
    public function findByPath($shopId='', $path='') {
        $filter = array('shop_id' => $shopId, 'path' => $path);
        $mpage = $this->where($filter)->find();
        return $mpage;
    }

    public function getRow($filter) {
        $res = $this->where($filter)->find();
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

}
