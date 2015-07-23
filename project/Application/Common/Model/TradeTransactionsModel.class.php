<?php
namespace Common\Model;
use Think\Model;
class TradeTransactionsModel extends Model {

    public function getRow($filter,$order='created_at'){
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

}
