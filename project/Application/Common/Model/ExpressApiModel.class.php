<?php
namespace Common\Model;
use Common\Model\PubModel;

class ExpressApiModel extends PubModel {
    //获得快递列表
    public function getlist() {
        $res = $this->apiexec->goApi('express', array(), 'get');
        $data = array();
        if($res['status'] == 'success'){
            foreach($res['data'] as $k=>$rd){
                $data[$k]['name'] = $rd['name'];
                $data[$k]['alias'] = $rd['name'] . ';' . $rd['alias']; 
                $data[$k]['code'] = $rd['code'];
            }
        }

        return $data;
    }

    //获得快递信息
    public function getinfo($params) {
        $res = $this->apiexec->thirdApi('express/logistic-path', $params, 'get');
        return $res;
    }

}
