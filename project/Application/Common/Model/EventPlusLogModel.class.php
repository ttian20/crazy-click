<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventPlusLogModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function findByCustomerAndClicker($eventId, $customerId, $clickerId) {
        $_id = md5($eventId . '-' . $customerId . '-' . $clickerId);
        $log = $this->findById($_id);
        /*$filter = array(
            'event_id' => $eventId,
            'customer_id' => $customerId,
            'clicker_id' => $clickerId,
        );
        $log = $this->where($filter)->find();*/
        return $log ? $log : false;
    }

    public function createNew($eventId, $customerId, $clickerId) {
        $_id = md5($eventId . '-' . $customerId . '-' . $clickerId);
 
        $data = array(
            '_id' => $_id,
            'event_id' => $eventId,
            'customer_id' => $customerId,
            'clicker_id' => $clickerId,
            'click_time' => time(),
        );
        $this->add($data);
        return true;
    }

    public function getList($shopId='') {
        $filter = array('shop_id' => $shopId, '_id' => array('$ne' => $shopId));
        $mpages = $this->where($filter)->select();
        return $mpages;
    }

    public function del($id='') {
        $filter = array('_id' => $id);
        $res = $this->where($filter)->delete();
        return $res;
    }
    
    public function findByPath($shopId='', $path='') {
        $filter = array('shop_id' => $shopId, 'path' => $path);
        $mpage = $this->where($filter)->find();
        return $mpage;
    }
}
