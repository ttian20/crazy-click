<?php
namespace Common\Model;
use Think\Model;
class TradeShippingAddressesModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }
}
