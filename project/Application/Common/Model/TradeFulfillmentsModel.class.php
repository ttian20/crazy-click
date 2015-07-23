<?php
namespace Common\Model;
use Think\Model;
class TradeFulfillmentsModel extends Model {

    public function getRowByItemId($item_id){
        $tfoiMdl = M('TradeFulfillmentsOrderItems');
        $tfoi = $tfoiMdl->where(array('trade_item_id' => $item_id))->find();
        $tf = array();
        if($tfoi){
            $tf = $this->where(array('id' => $tfoi['trade_fulfillments_id']))->find();
        }

        return $tf;
    }

    public function getRowByTradeId($trade_id){
        $tf = $this->where(array('trade_id' => $trade_id))->select();

        if(!$tf){
            $tf = array();
        }

        return $tf;
    }
}
