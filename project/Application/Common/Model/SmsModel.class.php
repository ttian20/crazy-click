<?php
namespace Common\Model;
use Think\Model;
class SmsModel extends Model {
    public function saveCode($mobile, $code, $type='reg') {
        $expired_time = 600;
        $create_time = time();

        $data = array(
            'mobile' => $mobile,
            'code' => $code,
            'type' => $type,
            'expired_time' => $expired_time,
            'create_time' => $create_time,
        );
        return $this->add($data);
    }

    public function verifyCode($mobile, $code, $type='reg') {
        $current_time = time();

        $filter = array(
            'mobile' => $mobile,
            'code' => $code,
            'type' => $type,
        );
        $rs = $this->where($filter)->order('create_time desc')->find();
        if (!$rs) {
            return array('res' => 'failed', 'msg' => '验证码错误');
        }
        elseif ($current_time > (intval($rs['create_time']) + intval($rs['expired_time']))) {
            return array('res' => 'failed', 'msg' => '验证码已失效');
        }
        else {
            return array('res' => 'success');
        }
    }
}
