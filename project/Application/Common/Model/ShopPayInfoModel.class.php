<?php
namespace Common\Model;
use Think\Model;

class ShopPayInfoModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }
}
