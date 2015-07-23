<?php
namespace Common\Model;
use Think\Model;
class SysSettingModel extends Model {
    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter){
        $rows = $this->where($filter)->select();
        return $rows;
    }
}
