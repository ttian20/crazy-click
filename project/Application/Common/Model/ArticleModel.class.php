<?php
namespace Common\Model;
use Think\Model;
class ArticleModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function createNew($params) {
        $current = time();
        $data = array(
            'title' => $params['title'],
            'user_id' => $params['user_id'],
            'artcat_id' => $params['artcat_id'],
            'content' => $params['content'],
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
        $artcatMdl = D('Artcat');
        $res = $artcatMdl->getAll(array());
        $artcats = array();
        if ($res) {
            foreach ($res as $v) {
                $artcats[$v['id']] = $v['catname'];
            }
        }
        $rows = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        foreach ($rows as &$row) {
            $row['catname'] = $artcats[$row['artcat_id']];
        }
        return $rows;
    }

    public function getAll($filter, $order = 'updated_at desc') {
        $rows = $this->where($filter)->order($order)->select();
        return $rows;
    }
}
