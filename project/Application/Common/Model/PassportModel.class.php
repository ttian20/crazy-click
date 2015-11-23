<?php
namespace Common\Model;
use Think\Model;
class PassportModel extends Model {
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
        $passports = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        return $passports;
    }

    public function verify($loginname, $password) {
        $filter = array('loginname' => $loginname);
        $rs = $this->getRow($filter);
        $res = array();
        if (!$rs) {
            $res = array('status' => 'fail', 'msg' => 'passport_not_exists');
        }
        else {
            if ($rs['password'] != $this->_genPassword($password, $rs['salt'])) {
                $res = array('status' => 'fail', 'msg' => 'password_error');
            }
            elseif (1 == $rs['disabled']) {
                $res = array('status' => 'fail', 'msg' => 'passport_blocked');
            }
            else {
                $res = array('status' => 'success', 'data' => array('passport' => $rs));
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
        if ($params['spreader_passport_id']) {
            $data['spreader_passport_id'] = $params['spreader_passport_id'];
        }
        $res = $this->data($data)->add();
        if ($res) {
            $passportId = $res;
            $passport = $this->getRow(array('id' => $passportId));
        }
        else {
            $passportId = 0;
            $passport = array();
        }
        return $passport;
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

    public function findByPassportid($passportid) {
        $filter = array('passport_id' => $passportid);
        $passport = $this->where($filter)->find();
        return $passport;
    }

    public function changePassword($passportname, $password) {
        return $res;
    }

    public function resetPassword($data) {
        return $res;
    }

    public function getInfo($data) {
        if ($this->prism) {
            $res = $this->_prism->get('passport/init-info', $data);
        }
        else {
            $res = $this->_apiexec->goApi('passport/init-info', $data, 'get');
        }
        return $res;
    }
}
