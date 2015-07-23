<?php
namespace Common\Model;
use Think\Model;
class InvitecodeModel extends Model {

    public function getRow($filter){
        $res = $this->where($filter)->find();
        return $res;
    }
    public function findByUserid($userid, $type = 'taobao') {
        $filter = array('user_id' => $userid, 'invite_type' => $type);
        $user = $this->where($filter)->find();
        return $user;
    }

    public function createNew($data, $prefix='') {
        $data['invite_code'] = $this->_genId($prefix);

        //$logfile = RUNTIME_PATH . 'Logs/request.log';
        //error_log(print_r($data, true), 3, $logfile);
        if ($this->add($data)) {
            return $data['invite_code'];
        }
        else {
            return false;
        }
    }

    public function _genId($prefix='') {
        $length = 6;
        $string = '23456789abcdefghijkmnpqrstuvwxyz';
        $min = 0;
        $id = $prefix;
        $max = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $id .= $string{$rand};
        }
        $logfile = RUNTIME_PATH . 'Logs/request.log';

        if($this->getRow(array('invite_code' => $id))){
            $id = $this->_genId($prefix);
        }
        
        return $id;
    }
}
