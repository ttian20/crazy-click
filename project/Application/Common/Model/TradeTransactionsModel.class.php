<?php
namespace Common\Model;
use Think\Model;
class TradeTransactionsModel extends Model {

    public function getRow($filter) {
        $row = $this->where($filter)->order($order)->find();
        return $row;
    }

    public function getCount($filter){
        $trade_count = $this->where($filter)->count();
        if(!$trade_count){
            $trade_count = 0;
        }
        return $trade_count;
    }

    public function getTotalAmount($filter){
        $amount = 0;
        $res = $this->where($filter)->sum('amount');

        if(!empty($res)){
           $amount = $res;
        }
        
        return $amount;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'trans_id' => $this->_genTradeTransactionId(),
            'trade_id' => $params['trade_id'],
            'passport_id' => $params['passport_id'],
            'kind' => $params['kind'],
            'amount' => $params['amount'],
            'status' => $params['status'],
            'created_at' => isset($params['created_at']) ? $params['created_at'] : $current,
            'updated_at' => isset($params['updated_at']) ? $params['updated_at'] : $current,
        );

        $res = $this->add($data); 
        if ($res) {
            $data['id'] = $res;
        }
        return $data;
    }

    private function _genTradeTransactionId() {
        $date = date("Ymd");
        $microseconds = floor(microtime(true) * 1000);
        $id = 'ali' . $date . $microseconds;
        return $id;
    }
}
