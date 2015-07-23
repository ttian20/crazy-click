<?php
namespace Common\Model;
use Think\Model;
class EventCodeModel extends Model {
    public function getRow($filter, $lock = false){
        if ($lock) {
            $row = $this->lock(true)->where($filter)->find();
        }
        else {
            $row = $this->where($filter)->find();
        }
        return $row;
    }

    public function getAll($filter, $order='update_time desc'){
        $rows = $this->where($filter)->order($order)->select();
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

    public function create($event, $tradeId, $uid, $num, $productId = 0, $price = 0.0) {
        $logfile = RUNTIME_PATH . 'Logs/payed.log';
        error_log(print_r($event, true), 3, $logfile);
        error_log($tradeId . "\n", 3, $logfile);
        error_log($uid . "\n", 3, $logfile);
        error_log($productId . "\n", 3, $logfile);
        $codes = $this->_genEventCodes($tradeId, $productId, $num);
        if (!$codes) {
            return false;
        }

        $current = time();
        $res = array();
        foreach ($codes as $code) {
            $data = array(
                'event_id' => \Common\Lib\Idhandler::decode($event['id']),
                'code' => $code,
                'uid' => $uid,
                'product_id' => $productId,
                'trade_id' => $tradeId,
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

    private function _genEventCodes($tradeId, $productId, $num) {
        $codes = array();
        for($i = 0; $i < $num; $i++) {
            $identify = $tradeId . '-' . $productId . '-' . $i;
            $codes[] = md5($identify);
        }
        return $codes;
    }

    private function _genCouponCode($max_sn) {
        $length = 6;
        $string = '0123456789abcdefghijklmnopqrstuvwxyz';
        $min = 0;
        $max = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $code .= $string{$rand}; 
        }

        return $code;       
    }

    public function createCoupon($data) {
         $couponLength = 6;
         $current = time();
         $shopId = $data['shop_id'];
		 $params = array(
			'event_id' => isset($data['event_id'])?$data['event_id']:0,
			'shop_id' => $data['shop_id'],
			'code' => $this->_genCouponCode($couponLength),
			'uid' => isset($data['uid'])?$data['uid']:'',
            'rid' => isset($data['rid'])?$data['rid']:'',
			'product_id' => isset($data['product_id']) ? $data['product_id'] : 0,
			'trade_id' => isset($data['trade_id']) ? $data['trade_id'] : 0,
			'coupon_id' => isset($data['coupon_id']) ? $data['coupon_id'] : 0,
			'status' => 'unused',
			'create_time' => $current,
			'update_time' => $current,
		);
        $filter = array('shop_id' => $data['shop_id'], 'code' => $params['code']);
        if ($this->getRow($filter)) {
            return $this->createCoupon($data);
        }
        else {
            $codeId = $this->add($params);
            return $params;
        }
    }
}
