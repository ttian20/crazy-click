<?php
namespace Common\Model;
use Think\Model;
class SupplierProductOptionsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $rows;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }
}
