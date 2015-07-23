<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventLogModel extends MongoModel {
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
}
