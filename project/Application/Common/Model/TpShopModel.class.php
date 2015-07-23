<?php
namespace Common\Model;
use Think\Model;
class TpShopModel extends Model {
    public function findByUsername($username) {
        $filter = array('user_name' => $username);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function getListByShopId($shopId = '', $type = 'shop') {
        $filter = array('shop_id' => $shopId, 'shop_type' => $type);
        $shops = $this->where($filter)->select();
        return $shops;
    }

    public function del($id='') {
        $filter = array('id' => $id);
        $res = $this->where($filter)->delete();

        return $res;
    }

    public function sync($id) {
        $tpShop = $this->find($id);

        $config = C('MQ_TPSHOP');
        /****pub mq start****/
        /*$cnn = new \AMQPConnection();
        $cnn->setHost($config['host']);
        $cnn->setPort($config['port']);
        $cnn->setLogin($config['user']);
        $cnn->setPassword($config['password']);
        $cnn->setVhost($config['vhost']);
        $cnn->connect();
    
        if(!$cnn->isConnected()) {
            exit('Cannot connect to the broker');
        }
    
        $ch = new \AMQPChannel($cnn);
        $ex = new \AMQPExchange($ch);
        $ex->setName('sync_task');
    
        $q_data = array(
            'shop_id' => array($tpShop['shop_id']),
            'type' => array('taobao.init'),
            'user_sess' => array($tpShop['thirdparty_session']),
            'site_title' => array($tpShop['shop_title']),
        );
        $rs = $ex->publish(json_encode($q_data), 'task.node.init.taobao');*/

            $data = array(
                'id' => $id,
                'sync_status' => 0,
                'update_time' => time(),
            );
            $this->save($data);
    }
}
