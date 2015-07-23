<?php
namespace Common\Model;
use Common\Model\PubModel;

class ShowcaseApiModel extends PubModel {

    public function getInfo($id){
        $data = $this->apiexec->showcase('case/'.$id, array(), 'get');
        return $data;
    }

    public function casePublish($id){
        $data = $this->apiexec->showcase('case/'.$id.'/publish', array(), 'post');
        return $data;
    }

    public function getDefWidgets($id){
        $data = $this->apiexec->showcase('def/showcase/'.$id.'/widgets', array(), 'get');
        return $data;
    }

   public function caseList($params, $limit=10, $offset=0, $sort=array('-created_at')){
        $data = $this->apiexec->showcase('case', array('data' => json_encode(array('filter' => $params,'limit' => $limit,'offset' => $offset, 'sorts' => $sort))), 'get');
        return $data;
    }

    public function getGroup($id){
        $data = $this->apiexec->showcase('widget-group/'.$id, array(), 'get');
        return $data;
    }

    public function widgetDef($filter=array()){
        return $this->apiexec->showcase('def/widget', $filter, 'get');
    }

    public function showcaseDef($filter=array()){
        return $this->apiexec->showcase('def/showcase', array('data' => json_encode(array('filter' => $filter))), 'get');
    }

    public function savePage($params, $data){
        $res =  $this->apiexec->showcase('case/'.$params['sc_id'].'/page/' . $params['page_view'], array('data' => $data), 'put');
        /*
        if($res['status'] == 'success'){
            $res = $this->casePublish($params['sc_id']);
        }
         */
        return $res;

    }

    public function savePreview($params){
        return $this->apiexec->showcase('page/preview', array('data' => $params), 'post');
    }

    public function obtain($params){
        return $this->apiexec->showcase('case/smart-obtain', $params, 'get');
    }

    public function saveCase($id, $params){
        $data = $this->apiexec->showcase('case/'.$id, array('data' => $params, 'c_id' => $id), 'put');
        return $data;
    }

    public function caseDel($id, $shopId){
        $data = $this->apiexec->showcase('case/'.$id, array(), 'delete');
        return $data;
    }

}
