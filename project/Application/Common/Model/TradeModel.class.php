<?php
namespace Common\Model;
use Think\Model;
class TradeModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter){
        $count = $this->where($filter)->count();
        if (!$count){
            $count = 0;
        }
        return $count;
    }

    public function getLists($filter, $page=1, $limit=10, $order='created_at desc'){
        $tradelists = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        $trades = array();
        if ($tradelists) {
            $tranMdl = D('TradeTransactions');
            
            foreach ($tradelists as $key => $tl) {
                $trades[$key]['trade'] = $tl;
                $trade_id = $tl['id'];
                $trades[$key]['trade_trans'] = $tranMdl->getRow(array('trade_id' => $trade_id));
            }
        }
        return $trades;
    }

    public function getTotalAmount($filter) {
        $amount = 0;
        $res = $this->where($filter)->sum('total_price');

        if(!empty($res)){
           $amount = $res;
        }
        
        return $amount;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'id' => $this->_genTradeId(),
            'passport_id' => $params['passport_id'],
            'product_id' => $params['product_id'],
            'product_title' => $params['product_title'],
            'status' => isset($params['status']) ? $params['status'] : 'active',
            'financial_status' => isset($params['financial_status']) ? $params['financial_status'] : 'pending',
            'total_price' => $params['total_price'],
            'total_clicks' => $params['total_clicks'],
            'created_at' => isset($params['created_at']) ? $params['created_at'] : $current,
            'updated_at' => isset($params['updated_at']) ? $params['updated_at'] : $current,
        );

        $res = $this->add($data); 
        return $data;
    }

    private function _genTradeId() {
        $date = date("Ymd");
        $microseconds = floor(microtime(true) * 1000);
        $id = $date . $microseconds;
        return $id;
    }
}
