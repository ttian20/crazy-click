<?php
namespace Common\Model;
use Think\Model;
class EventTypeModel extends Model {
    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter = ''){
        if ($filter) {
            $rows = $this->where($filter)->select();
        }
        else {
            $rows = $this->where()->select();
        }
        return $rows;
    }

    public function getCount($filter){
        $rows = $this->where($filter)->count();
        return $rows;
    }

    public function getList($filter, $limit=10, $page=1, $order='update_time desc'){
        return $this->where($filter)->limit($limit)->page($page)->order($order)->select();  
    }

    public function update($params, $filter){
        //$sql = "update event_code set status='locked',rid=".$_COOKIE['weibo_uid'].",update_time=".time()." where id = ".$id;
        //$params = array('id' => $id, 'status' => $status, 'rid' => $_COOKIE['weibo_uid'], 'update_time' => time());
        if($filter){
            $this->where($filter)->save($params);
        }else{
            $this->save($params);
        }

        //return $this->execute($sql);
    }

    public function create($event, $trade_id, $num) {
        $codes = $this->_genEventCodes($trade_id, $num);
        if (!$codes) {
            return false;
        }

        $current = time();
        $res = array();
        foreach ($codes as $code) {
            $data = array(
                'event_id' => \Common\Lib\Idhandler::decode($event['id']),
                'code' => $code,
                'uid' => $_COOKIE['weibo_uid'],
                'trade_id' => $trade_id,
                'status' => 'unused',
                'create_time' => $current,
                'update_time' => $current,
            );
            $id = $this->add($data);
            $data['id'] = $id;
            $res[] = $data;
        }
        return $res;
    }

    private function _genEventCodes($trade_id, $num) {
        $codes = array();
        for($i = 0; $i < $num; $i++) {
            $identify = $trade_id . '-' . $i;
            $codes[] = md5($identify);
        }
        return $codes;
    }
}
