<?php
namespace Common\Model;
use Think\Model;
class TasksModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter){
        $count = $this->where($filter)->count();
        if(!$count){
            $count = 0;
        }
        return $count;
    }

    public function getLists($filter, $page=1, $limit=10, $order='created_at desc'){
        $lists = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        return $lists;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'id' => $params['id'],
            'passport_id' => $params['passport_id'],
            'kwd' => $params['kwd'],
            'nid' => $params['nid'],
            'platform' => $params['platform'],
            'shop_type' => $params['shop_type'],
            'times' => $params['times'],
            'begin_time' => $params['begin_time'],
            'end_time' => $params['end_time'],
            'click_start' => $params['click_start'],
            'click_end' => $params['click_end'],
            'status' => $params['status'],
            'created_at' => isset($params['created_at']) ? $params['created_at'] : $current,
            'updated_at' => isset($params['updated_at']) ? $params['updated_at'] : $current,
        );

        $res = $this->add($data); 
        return $data;
    }
}
