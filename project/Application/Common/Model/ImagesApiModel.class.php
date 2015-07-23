<?php
namespace Common\Model;
use Common\Model\PubModel;

class ImagesApiModel extends PubModel {
    //图片列表，根据不同的类型返回：商品图片列表，其他类表
    public function getList($params) {
        return $this->apiexec->goApi('image/list', $params, 'get');
    }
}
