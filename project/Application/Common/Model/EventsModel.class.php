<?php
namespace Common\Model;
use Think\Model;
class EventsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function findById($id='') {
        $id = \Common\Lib\Idhandler::decode($id);
        $filter = array('id' => $id);
        $res = $this->where($filter)->find();
        if($res){
            $photoMdl = D('Photo');    
            $res['photo'] = $photoMdl->getRow(array('photo_id' => $res['photo_id']));
            if($id == '4'){
                $res['photo']['url'] = 'http://wdwd-shop.qiniudn.com/event_coconuttop_banner_940.jpg';
                $res['photo']['width'] = '940';
                $res['photo']['height'] = '448';
            }

            $res['thumb'] = $photoMdl->getRow(array('photo_id' => $res['thumb_id']));

            $product_ids = explode(';', $res['product_ids']);
            if($product_ids){
                foreach($product_ids as &$id){
                    $id = \Common\Lib\Idhandler::encode($id);
                }
            }
            $res['product_ids'] = implode(';', $product_ids);

            $present = explode(';', $res['present']);
            if($present){
                foreach($present as &$id){
                    $id = \Common\Lib\Idhandler::encode($id);
                }
            }
            $res['present'] = implode(';', $present);
        }
        $eventExtraMdl = D('EventExtra');
        $extra = $eventExtraMdl->findById($res['id']);
        if($extra){
            if($extra['pages']){
                $extra['pagesimage'] = explode(',', $extra['pages']);
            } 
        }else{
            $extra = array();
        } 

        $res['extra'] = $extra;

        $res['id'] = \Common\Lib\Idhandler::encode($res['id']);
        $res['coupon_id'] = \Common\Lib\Idhandler::encode($res['coupon_id']);
        

        return $res;
    }

    public function getCount($filter) {
        $res = $this->where($filter)->count();
        return $res;
    }

    public function getLast($order='create_time desc') {
        $res = $this->order($order)->find();
        if($res){
            $photoMdl = D('Photo');    
            $res['photo'] = $photoMdl->getRow(array('photo_id' => $res['photo_id']));
            $res['thumb'] = $photoMdl->getRow(array('photo_id' => $res['thumb_id']));

            if($res['id'] == '4'){
                $res['photo']['url'] = 'http://wdwd-shop.qiniudn.com/event_coconuttop_banner_940.jpg';
                $res['photo']['width'] = '940';
                $res['photo']['height'] = '448';
            }
            $product_ids = explode(';', $res['product_ids']);
            if($product_ids){
                foreach($product_ids as &$id){
                    $id = \Common\Lib\Idhandler::encode($id);
                }
            }
            $res['product_ids'] = implode(';', $product_ids);

            $present = explode(';', $res['present']);
            if($present){
                foreach($present as &$id){
                    $id = \Common\Lib\Idhandler::encode($id);
                }
            }
            $res['present'] = implode(';', $present);
            $res['coupon_id'] = \Common\Lib\Idhandler::encode($res['coupon_id']);
        }
        return $res;
    }

    public function getList($filter, $limit=10, $page=1, $order='create_time desc') {
        $res = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        if($res){
            $photoMdl = D('Photo');    
            foreach($res as &$r){
                $r['photo'] = $photoMdl->getRow(array('photo_id' => $res['photo_id']));
                $r['thumb'] = $photoMdl->getRow(array('photo_id' => $res['thumb_id']));

                if($r['id'] == '4'){
                    $r['photo']['url'] = 'http://wdwd-shop.qiniudn.com/event_coconuttop_banner_940.jpg';
                    $r['photo']['width'] = '940';
                    $r['photo']['height'] = '448';
                }

                $product_ids = explode(';', $r['product_ids']);
                if($product_ids){
                    foreach($product_ids as &$id){
                        if($id){
                            $id = \Common\Lib\Idhandler::encode($id);
                        }
                    }
                }
                $r['product_ids_array'] = $product_ids;
                $r['product_ids'] = implode(';', $product_ids);

                $present = explode(';', $r['present']);
                if($present){
                    foreach($present as &$id){
                        if($id){
                            $id = \Common\Lib\Idhandler::encode($id);
                        }
                    }
                }
                $r['present_array'] = $present;
                $r['present'] = implode(';', $present);
                $r['id'] = \Common\Lib\Idhandler::encode($r['id']);
                $r['coupon_id'] = \Common\Lib\Idhandler::encode($r['coupon_id']);
            }
        }
        return $res;
    }

    public function saveData($data) {
        if(isset($data['id']) && $data['id']){
            unset($data['create_time']);
        }
        if($this->create($data)){
            if(isset($data['id']) && $data['id']){
                $this->save();
                $id = $data['id'];
            }else{
                $id = $this->add();
            }
            return array('status' => 'success', 'msg' => '修改保存成功','data' => array('id' => $id));
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

    public function del($id) {
        if($this->where(array('id' => $id))->save(array('status' => 3))){
            return array('status' => 'success', 'msg' => '删除成功');
        }else{
            return array('status' => 'fail', 'msg' => $this->getError());
        }
    }

}
