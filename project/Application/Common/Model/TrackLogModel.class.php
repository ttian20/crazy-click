<?php
namespace Common\Model;
use Think\Model\MongoModel;

class TrackLogModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function createNew($data) {
        $this->add($data);
        return true;
    }

    public function getCount($filter){
        $rows = $this->where($filter)->count();
        return $rows;
    }
}
