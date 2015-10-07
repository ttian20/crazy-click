<?php
namespace Common\Model;
use Think\Model;
class ClickAccountLogModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getLists($filter, $page = 1, $limit = 10, $order = 'updated_at desc') {
        $rows = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $rows;
    }

    public function getAll($filter, $order = 'updated_at desc') {
        $rows = $this->where($filter)->order($order)->select();
        return $rows;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'passport_id' => $params['passport_id'],
            'type' => '',
            'changed_type' => $params['changed_type'],
            'changed_clicks' => $params['changed_clicks'],
            'balance_clicks' => $params['balance_clicks'],
            'description' => $params['description'],
            'created_at' => $current,
            'updated_at' => $current
        );
        $res = $this->data($data)->add();
        if ($res) {
            $log = $this->getRow(array('id' => $res));
        }
        else {
            $log = array();
        }
        return $log;
    }
}
