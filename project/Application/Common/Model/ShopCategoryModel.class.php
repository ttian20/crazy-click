<?php
namespace Common\Model;
use Think\Model;
class ShopCategoryModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function getRow($filter, $getParent = false) {
        $row = $this->where($filter)->find();
        if ($getParent) {
            if ($row['pcid'] != 1) {
                $parent_filter = array('cid' => $row['pcid']);
                $parent = $this->where($parent_filter)->find();
                $row['parent'] = $parent;
            }
        }
        return $row;
    }

    public function getList($filter) {
        $row = $this->where($filter)->select();
        return $row;
    }
}
