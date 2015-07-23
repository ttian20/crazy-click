<?php
namespace Common\Model;
use Think\Model;
class SupplierPhotoModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter, $order="created_at desc"){
        $rows = $this->where($filter)->order($order)->select();
        return $rows;
    }

    public function getCount($filter){
        $rows = $this->where($filter)->count();
        return $rows;
    }

    public function getAllByPage($filter,$limit=18, $page=1, $order = 'created_at desc'){
        $rows = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $rows;
    }

    public function createNew($data) {
        $photo_id = $this->add($data);
        if ($photo_id) {
            $data['photo_id'] = $photo_id;
            return $data;
        }
        else {
            return false;
        }
    }

    public function getMainPhoto($productId) {
        $filter = array('product_id' => $productId, 'position' => 0);
        $photo = $this->getRow($filter);
        return $photo;
    }
}
