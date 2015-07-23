<?php
namespace Common\Model;
use Think\Model;
class TradeItemModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter){
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function getListByTradeId($trade_id, $shop_id){
        $rs = $this->where(array('trade_id' => $trade_id, 'shop_id' => $shop_id))->select();
        if(!$rs){
            $rs = array();
        }

        return $rs;
    }

    public function getItemsProductsByTradeId($trade_id){
        $rs = $this->where(array('trade_id' => $trade_id))->select();
        $products = array();
         
        if($rs){
            $productMdl = D('Products');
            $fulfillmentMdl = D('TradeFulfillments');
            foreach($rs as &$r){
                $r['product'] = $productMdl->getInfoByPosition($r['product_id']);
                $r['fulfillment'] = $fulfillmentMdl->getRowByTradeId($r['trade_id']);
                $r['product_id'] = \Common\Lib\Idhandler::encode($r['product_id']);
            }
        }else{
            $rs = array();
        }

        return $rs;
    }

}
