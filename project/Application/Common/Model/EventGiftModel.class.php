<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventGiftModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function getRow($filter) {
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function getAll($filter) {
        $rs = $this->where($filter)->select();
        return $rs;
    }

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function findByCustomer($customerId, $eventId, $prize) {
        $_id = md5($eventId . '-' . $customerId . '-' . $prize);
        return $this->findById($_id);
    }

    public function createNew($data) {
        $current = time();
        $params = array(
            'event_id' => (int)$data['event_id'],
            'shop_id' => (int)$data['shop_id'],
            'uid' => $data['uid'],
            'prize' => $data['prize'],
            'img' => $data['img'],
            'received' => (int)$data['received'],
            'create_time' => $current,
            'update_time' => $current,
        );
        $_id = md5($params['event_id'] . '-' . $params['uid'] . '-' . $params['prize']);
        $params['_id'] = $_id;
        if (!$this->findByCustomer($params['uid'], $params['event_id'], $params['prize'])) {
            $this->add($params);
        }
    }

    public function getCount($filter) {
        $rs = $this->where($filter)->count();
        return $rs;
    }
}
