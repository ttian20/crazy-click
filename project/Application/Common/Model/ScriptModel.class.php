<?php
namespace Common\Model;
use Think\Model;
class ScriptModel extends Model {

    public function getPayData($shop_id='', $start, $end) {
        $sql = "select count(distinct(trade_id)) as deal_count, sum(amount) as amount from trade_transactions where shop_id = {$shop_id} and kind = 'capture' and status='success' and created_at >= {$start} and created_at <= {$end}";
        var_dump($sql);
        //$sql = "select count(distinct(trade_id)) as deal_count, sum(amount) as amount from trade_transactions where shop_id = {$shop_id} and kind = 'capture'     and status='TRADE_SUCCESS'";
        $rs = $this->query($sql);
        return $rs;
    }

}
