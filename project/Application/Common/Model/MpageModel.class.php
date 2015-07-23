<?php
namespace Common\Model;
use Think\Model\MongoModel;

class MpageModel extends MongoModel {
    protected $connection = 'MONGO_CONFIG';
    protected $_idType = self::TYPE_STRING;

    public function findById($shopId='') {
        $filter = array('_id' => $shopId);
        $mpage = $this->where($filter)->find();
        return $mpage;
    }

    public function getList($shopId='') {
        $filter = array('shop_id' => $shopId, '_id' => array('$ne' => $shopId));
        $mpages = $this->where($filter)->order('lastmodify desc')->select();
        return $mpages;
    }

    public function del($id='') {
        $filter = array('_id' => $id);
        $res = $this->where($filter)->delete();
        return $res;
    }
    
    public function findByPath($shopId='', $path='') {
        $filter = array('shop_id' => $shopId, 'path' => $path);
        $mpage = $this->where($filter)->find();
        return $mpage;
    }

    public function saveData($shop_id, $params) {
        $data_id = isset($params['_id'])?$params['_id']:'';
        if(!$data_id){
           $params['_id'] = $this->_genId();
        }

        if($data_id && $this->findById($data_id)){
            foreach($params as $k=>&$p){
                if($k != '_id'){
                   $p = array('set', $p);
                }
            }
            $this->create($params);
            $this->save();
            return array('status' => 'success', 'msg' => '修改保存成功', 'data' => array('id' => $params['_id']));
        }else{
            $this->create($params);
            $this->add();
            return array('status' => 'success', 'msg' => '创建成功', 'data' => array('id' => $params['_id']));
        }
    }

    public function decorateSave($shop_id, $data) {
        $params['_id'] = $shop_id;
        if($this->findById($shop_id)){
            $params['data'] = array('set', $data);
            $params['lastmodify'] = array('set', time());
            $this->create($params);
            $this->save();
            return array('success' => 'true', 'msg' => '修改保存成功', 'data' => array('id' => $shop_id));
        }else{
            $params['shop_id'] = $shop_id;
            $params['data'] = $data;
            $params['lastmodify'] = time();
            $params['title'] = '首页';

            $this->create($params);
            $this->add();
            return array('success' => 'true', 'msg' => '创建成功', 'data' => array('id' => $shop_id));
        }
    }

    public function _genId() {
        $length = 8;
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $min = 0;
        $max = strlen($string) - 1;
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $id .= $string{$rand};
        }

        if ($this->findById($id) != '') {
             $id = $this->_genId();
        }
        return $id;
    }

    public function getListPage($shopId='', $limit, $page) {
        $filter = array('shop_id' => $shopId, '_id' => array('$ne' => $shopId));
        $mpages = $this->where($filter)->limit((int)$limit, ($page - 1) * $limit)->order('lastmodify desc')->select();
        return $mpages;
    }
     
    public function getCount($shopId='') {
        $filter = array('shop_id' => $shopId, '_id' => array('$ne' => $shopId));
        $mpages = $this->where($filter)->count();
        return $mpages;
    }

}
