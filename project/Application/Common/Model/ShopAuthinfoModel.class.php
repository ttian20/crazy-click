<?php
namespace Common\Model;
use Think\Model;
class ShopAuthinfoModel extends Model {
    private $_prism = null;
    public function __construct() {
        parent::__construct();
    }

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getList($filter, $limit=10, $page=1, $order='update_time desc') {
        $res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $res;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

    public function saveData($data) {
        $shopId = $data['shop_id'];
        $authInfo = $this->getRow(array('shop_id' => $shopId));

        if($authInfo && isset($data['create_time']) && $data['create_time']){
            unset($data['create_time']);
        }

        if($this->create($data)){
            if($authInfo){
                $this->save();
                return array('status' => 'success', 'msg' => '修改保存成功');
            }else{
                //$data['shop_id'] = $this->_genShopId(); 
                $this->add();
                return array('status' => 'success', 'msg' => '创建成功');
            }
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
