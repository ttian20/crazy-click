<?php
namespace Common\Model;
use Common\Model\PubModel;
class TagsApiModel extends PubModel {

    //获得标签名称获得标签的列表
    public function getListByName($params) {
        return $this->apiexec->goApi('tag/search-like', $params, 'get');
    }
}
