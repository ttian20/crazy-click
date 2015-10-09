<?php
namespace Common\Model;
use Think\Model;
class UserModel extends Model {
    private $_prism = null;
    private $_secret = 'crazyclick';

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getCount($filter){
        $count = $this->where($filter)->count();
        if (!$count){
            $count = 0;
        }
        return $count;
    }

    public function getLists($filter, $page=1, $limit=10, $order='created_at desc'){
        $users = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        return $users;
    }

    public function verify($loginname, $password) {
        $filter = array('loginname' => $loginname);
        $rs = $this->getRow($filter);
        $res = array();
        if (!$rs) {
            $res = array('status' => 'fail', 'msg' => 'user_not_exists');
        }
        else {
            if ($rs['password'] != $this->_genPassword($password, $rs['salt'])) {
                $res = array('status' => 'fail', 'msg' => 'password_error');
            }
            elseif (1 == $rs['disabled']) {
                $res = array('status' => 'fail', 'msg' => 'user_blocked');
            }
            else {
                $res = array('status' => 'success', 'data' => array('user' => $rs));
            }
        }
        return $res;
    }

    public function createNew($params) {
        $current = time();
        $salt = $this->_genSalt();
        $data = array(
            'loginname' => $params['loginname'],
            'password' => $this->_genPassword($params['password'], $salt),
            'salt' => $salt,
            'created_at' => $current,
            'updated_at' => $current
        );
        if ($params['qq']) {
            $data['qq'] = $params['qq'];
        }
        if ($params['email']) {
            $data['email'] = $params['email'];
        }
        if ($params['mobile']) {
            $data['mobile'] = $params['mobile'];
        }
        $res = $this->data($data)->add();
        if ($res) {
            $userId = $res;
            $user = $this->getRow(array('id' => $userId));
        }
        else {
            $userId = 0;
            $user = array();
        }
        return $user;
    }

    private function _genPassword($password, $salt) {
        return md5($password . $salt . $this->_secret);
    }

    private function _genSalt() {
        $length = 6;
        $string = '0123456789abcdefghijklmnopqrstuvwxyz';
        $min = 0;
        $max = strlen($string) - 1;
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $salt .= $string{$rand};
        }
        return $salt;
    }

    public function findByUserid($userid) {
        $filter = array('user_id' => $userid);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function changePassword($username, $password) {
        return $res;
    }

    public function resetPassword($data) {
        return $res;
    }

    public function getInfo($data) {
        if ($this->prism) {
            $res = $this->_prism->get('user/init-info', $data);
        }
        else {
            $res = $this->_apiexec->goApi('user/init-info', $data, 'get');
        }
        return $res;
    }
}
