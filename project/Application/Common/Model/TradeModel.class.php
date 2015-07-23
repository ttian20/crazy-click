<?php
namespace Common\Model;
use Think\Model;
class TradeModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter){
        $trade_count = $this->where($filter)->count();
        if(!$trade_count){
            $trade_count = 0;
        }
        return $trade_count;
    }

    public function getList($filter, $limit=10, $page=1, $order='created_at desc'){
        $tradelists = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        $trades = array();
        if($tradelists){
            $itemMdl = D('TradeItem');
            $addressMdl = D('TradeShippingAddresses');
            $tranMdl = D('TradeTransactions');
            
            foreach($tradelists as $key=>$tl){
                $tl['shop_id'] = \Common\Lib\Idhandler::encode($tl['shop_id']);
                $trades[$key]['trade'] = $tl;
                $trade_id = $tl['id'];

                //商品地址
                $trades[$key]['trade_address'] = array();
                $address = $addressMdl->getRow(array('trade_id' => $trade_id));
                if($address){
                    $trades[$key]['trade_address'] = $address;
                }
                 
                //订单详情
                $trades[$key]['trade_item'] = array();
                $item = $itemMdl->getItemsProductsByTradeId($trade_id);

                $trades[$key]['trade_item'] = $item;


                $trades[$key]['trade_tran'] = $tranMdl->getRow(array('trade_id' => $trade_id,'status' => 'success'));
            }
        }
        return $trades;
    }

    public function getCountByMobile($filter){
        $addressMdl = M('TradeShippingAddresses');
        $trade_count = $addressMdl->where($filter)->count();
        if(!$trade_count){
            $trade_count = 0;
        }
        return $trade_count;
    }

    public function getListByMobile($filter, $limit=10, $page=1, $order='id desc'){
        $addressMdl = M('TradeShippingAddresses');
        $tranMdl = D('TradeTransactions');
        $address = $addressMdl->where($filter)->order($order)->limit($limit)->page($page)->select();

        $trades = array();
        if($address){
            $itemMdl = D('TradeItem');
            foreach($address as $key=>$ad){
                $trades[$key]['trade_address'] = $ad;
                $trade_id = $ad['trade_id'];
                $trades[$key]['trade'] = $this->getRow(array('id' => $trade_id));

                //订单详情
                $trades[$key]['trade_item'] = array();
                $item = $itemMdl->getItemsProductsByTradeId($trade_id);

                $trades[$key]['trade_item'] = $item;

                $trades[$key]['trade_tran'] = $tranMdl->getRow(array('trade_id' => $trade_id));
            } 
        }
        return $trades;
    }


    public function getTotalAmount($filter){
        $amount = 0;
        $res = $this->where($filter)->sum('total_price');

        if(!empty($res)){
           $amount = $res;
        }
        
        return $amount;
    }
}
