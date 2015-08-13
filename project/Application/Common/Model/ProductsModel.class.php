<?php
namespace Common\Model;
use Think\Model;
class ProductsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'title' => $params['title'],
            'price' => $params['price'],
            'description' => $params['description'],
            'published' => $params['published'],
            'created_at' => $current,
            'updated_at' => $current
        );
        $res = $this->data($data)->add();
        if ($res) {
            $article = $this->getRow(array('id' => $res));
        }
        else {
            $article = array();
        }
        return $article;
    }

    public function getLists($filter, $page = 1, $limit = 10, $order = 'updated_at desc') {
        $rows = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        foreach ($rows as &$v) {
            $v['encode_id'] = \Common\Lib\Idhandler::encode($v['id']);
        }
        return $rows;
    }

    public function getAll($filter, $order = 'updated_at desc') {
        $rows = $this->where($filter)->order($order)->select();
        foreach ($rows as &$v) {
            $v['encode_id'] = \Common\Lib\Idhandler::encode($v['id']);
        }
        return $rows;
    }
}
