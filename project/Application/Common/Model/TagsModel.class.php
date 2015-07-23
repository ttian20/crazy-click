<?php
namespace Common\Model;
use Think\Model;
class TagsModel extends Model {

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $res = $this->where($filter)->select();
        return $res;
    }

    public function saveData($data) {
        if($this->create($data)){
            if($data['id']){
                $this->save();
                $id = $data['id'];
            }else{
                $id = $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => array('id' => $id));
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

    public function createNew($data) {
        $tag_id = $this->add($data);
        if ($tag_id) {
            $data['tag_id'] = $tag_id;
            return $data;
        }
        else {
            return false;
        }
    }

    public function getHotTags($shopId, $limit=3) {
        $sql = "SELECT COUNT(`tag_id`) AS times, `tag_id` FROM `product_tags` WHERE `shop_id` = '{$shopId}' GROUP BY `tag_id` ORDER BY times DESC LIMIT {$limit}";
        $rs = $this->query($sql);
        $tags = array();
        if ($rs) {
            foreach ($rs as $row) {
                $row = $this->getRow(array('id' => $row['tag_id']));
                $tags[] = $row['name'];
            }
        }
        
        return $tags;
    }

}
