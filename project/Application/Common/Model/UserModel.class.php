<?php
namespace Common\Model;
use Think\Model;
class UserModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function findByUsername($username, $type = 'taobao') {
        $filter = array('user_name' => $username, 'user_type' => $type);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function findByUserid($userid, $type = 'taobao') {
        $filter = array('thirdparty_user_id' => $userid, 'user_type' => $type);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function findWechatFollower($shopId) {
        $filter = array('shop_id' => $shopId, 'user_type' => 'wechat');
        $user = $this->where($filter)->find();
        return $user;
    }

    public function createNew($data) {
        $user_id = $this->add($data);
        if ($user_id) {
            $data['user_id'] = $user_id;
            return $data;
        }
        else {
            return false;
        }
    }

    public function getCount($filter) {
        $row = $this->where($filter)->count();
        return $row;
    }
}
