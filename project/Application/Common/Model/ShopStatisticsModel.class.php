<?php
namespace Common\Model;
use Think\Model;

class ShopStatisticsModel extends Model {

    public function sumDelCount($filter) {
        return $this->where($filter)->sum('deal_count');
    }

    public function sumAmount($filter) {
        return $this->where($filter)->sum('amount');
    }

    public function sumSkuSum($filter) {
        return $this->where($filter)->sum('sku_sum');
    }

    public function getList($filter, $order='date asc') {
        return $this->where($filter)->order($order)->select();
    }

}
