<?php 
namespace Common\Model; 
use Think\Model;

class MenuModel extends Model {

    public $url = 'https://api.weixin.qq.com/cgi-bin/';

    public function getRow($id = 0, $shop_id = 0){
        $filter['id'] = $id;
        if($shop_id){
            $filter['shop_id'] = $shop_id;
        }
        return $this->where($filter)->order('weight')->find();
    }

    public function getList($shopId='', $parent_id = 0){
        $filter['shop_id'] = $shopId;
        $filter['parent_id'] = $parent_id;
        return $this->where($filter)->order('weight')->select();
    }

    public function saveData($shop_id, $data){
       
        $data['update_at'] = time();        
        
        $wx = '';
        if(isset($data['id']) && $data['id']){
            $filter['id'] = $data['id'];
            $filter['shop_id'] = $shop_id;
            $wx = $this->where($filter)->find();
        }

        if($wx){
            $data['id'] = $wx['id'];
            $this->create($data);
            $this->save();
            $rs = $wx['id'];
        }else{
            $data['create_at'] = time();
            $this->create($data);
            $rs = $this->add();
        }

        return $rs;

    }

    /**
    * 本地查询自定义菜单，组织数组打微信接口
    */
    function selectMenu($info){
        $rows = $this->getList($info['shop_id'], 0);
        if(count($rows)==0){
            $this->deleteMenu($info);exit;
        }
        foreach($rows as $r_k=>$r_v){
            $button[$r_k]['name'] = $r_v['name'];
            //$sec_rows = $this->getList($info['eid'],array(),array('msg_type'=>'menu','parent_id'=>$r_v['auto_id']),array('sort'=>'ASC'));
            $sec_rows = $this->getList($info['shop_id'], $r_v['id']);


            if(count($sec_rows)==0){
                $msg_data = unserialize($r_v['msg_data']);
                $button[$r_k]['type'] = $r_v['type'];
                if($r_v['type']=='click'){
                    $button[$r_k]['key'] = $r_v['id'];
                }else{
                    $button[$r_k]['url'] = $msg_data['url'];
                }
            }else{
                foreach($sec_rows as $s_k=>$s_v){
                    $msg_data = unserialize($s_v['msg_data']);
                    $button[$r_k]['sub_button'][$s_k]['name'] = $s_v['name'];
                    $button[$r_k]['sub_button'][$s_k]['type'] = $s_v['type'];
                    if($s_v['type']=='click'){
                        $button[$r_k]['sub_button'][$s_k]['key'] = $s_v['id'];
                    }else{
                        $button[$r_k]['sub_button'][$s_k]['url'] = $msg_data['url'];
                    }
                }

            }
        }
        $data['button'] = $button;

        $rs = $this->createMenu($info,$data);

        return $rs;
    }

    function deleteOp($id, $shop_id) {
        $filter['id'] = $id;
        $filter['shop_id'] = $shop_id;
        if($this->where($filter)->find()){
           return $this->where($filter)->delete();
        }else{
           return true;
        }
    }

    //根据parent_id 删除
    function deleteByPid($pid, $shop_id) {
        $filter['parent_id'] = $pid;
        $filter['shop_id'] = $shop_id;
        if($this->where($filter)->find()){
           return $this->where($filter)->delete();
        }else{
           return true;
        }
    }

    /**
    * 创建自定义菜单 post
    * $info 服务号的appid,appsecret  $data 要创建的菜单的数组
    */
    function createMenu($info,$data){
        // https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN
        $weixinApi = new \Common\Lib\Wechat();

        $weixinApi->resetAuth($info['appid']);
        $weixinApi->checkAuth($info['appid'], $info['appsecret']);
        //\Common\Lib\Utils::log('wechat', 'menu.log', $data);
        $res = $weixinApi->createMenu($data);
        //\Common\Lib\Utils::log('wechat', 'menu.log', $weixinApi->errCode);
        //\Common\Lib\Utils::log('wechat', 'menu.log', $weixinApi->errMsg);
        
        
        if($res){
            $return = array('status'=>'success');  
        }else{
            $return = array('status' => 'error', 'msg' => $weixinApi->errMsg);
        }
        return $return;
    }

    /**
    * 删除自定义菜单（所有的） get
    * $info 服务号的appid,appsecret
    */
    function deleteMenu($info){
        // https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN
        $weixinApi = new \Common\Lib\Wechat();
        $weixinApi->checkAuth($info['appid'], $info['appsecret']);
        $res = $weixinApi->deleteMenu();

        if($res){
            $return = array('status'=>'success');  
        }else{
            $return = array('status' => 'error', 'msg' => $weixinApi->errMsg);
        }
        return $return;

    }

}
