<?php
namespace Common\Model;
use Think\Model\MongoModel;

class EventPlusDateModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($id = '') {
        $filter = array('_id' => $id);
        $rs = $this->where($filter)->find();
        return $rs;
    }

    public function findByCustomer($customerId, $eventId, $date) {
        $_id = md5($eventId . '-' . $customerId . '-' . $date);
        return $this->findById($_id);
    }

    public function createNew($data) {
        $current = time();
        $date = date('Ymd');
        $params = array(
            'event_id' => (int)$data['event_id'],
            'customer_id' => (int)$data['customer_id'],
            'total' => 1,
            'nickname' => $data['nickname'],
            'mobile' => $data['mobile'],
            'date' => (int)$date,
            'create_time' => $current,
            'update_time' => $current,
        );
        $_id = md5($params['event_id'] . '-' . $params['customer_id'] . '-' . $date);
        $params['_id'] = $_id;
        $this->add($params);
    }

    public function plus($customerId, $eventId) {
        $date = date('Ymd');
        $_id = md5($eventId . '-' . $customerId . '-' . $date);
        $plus = $this->findById($_id);
    }

    public function del($id='') {
        $filter = array('_id' => $id);
        $res = $this->where($filter)->delete();
        return $res;
    }

    public function topList($eventId, $date) {
        $filter = array('event_id' => $eventId, 'date' => $date);

        $res = $this->where($filter)->order('total desc, update_time desc')->limit(0, 50)->select();
        return $res;
    }

    public function getCustomerRank($customerId, $eventId, $date) {
        $myPlus = $this->findByCustomer($customerId, $eventId, $date);
        if ($myPlus) {
            $myTotal = $myPlus['total'];
            $filter = array(
                'total' => array('gt', $myTotal),
                'date' => (int)$date
            );  
            $myPosition = $this->where($filter)->count();

            $filter = array(
                'total' => $myTotal,
                'update_time' => array('gte', (int)$myPlus['update_time']),
                'date' => (int)$date
            );  
            $num = $this->where($filter)->count();
            $myPosition += $num;

            return $myPosition;
        } 
        else {
            return 0;
        }
    }
}
