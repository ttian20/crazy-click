<?php
namespace Common\Model;
use Think\Model;
class ArtcatModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getLists($filter, $page = 1, $limit = 10, $order = 'updated_at desc') {
        $rows = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $rows;
    }

    public function getAll($filter, $order = 'updated_at desc') {
        $rows = $this->where($filter)->order($order)->select();
        return $rows;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'catname' => $params['catname'],
            'created_at' => $current,
            'updated_at' => $current
        );
        $res = $this->data($data)->add();
        if ($res) {
            $cat = $this->getRow(array('id' => $res));
        }
        else {
            $cat = array();
        }
        return $cat;
    }
}
