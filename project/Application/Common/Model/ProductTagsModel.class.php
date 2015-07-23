<?php
namespace Common\Model;
use Think\Model;
class ProductTagsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter){
        return $this->where($filter)->count();
    }

    public function getTagsName($filter) {
        $rows = $this->where($filter)->select();
        $tags = array();

        $tagsMdl = D('Tags');
        if ($rows) {
            foreach ($rows as $row) {
                $tag = $tagsMdl->getRow(array('id' => $row['tag_id'])); 
                if ($tag) {
                    $tags[] = $tag['name'];
                }
            }
        }
        return $tags ? $tags : array();
        //return $tags ? implode(';', $tags) : '';
    }
}
