<?php
namespace Common\Model;
use Common\Model\PubModel;

class NotifyApiModel extends PubModel {

    public function sendsms($params){
        $data = $this->apiexec->goApi('notify/sms', $params, 'post');
        return $data;
    }
}
