<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index() {
        if ($_GET['err']) {
            $this->assign('error', $_GET['err']);
        }
        if (session('?passport')) {
            $this->redirect('/home/product/lists');
        }
        $this->display();
    }

    public function verify() {
        header('Content-type: application/json');
        $p = I("post.");
        $passportMdl = D("Passport");
        $res = $passportMdl->verify($p['loginname'], $p['password']);
        if ('success' == $res['status']) {
            //写session
            $passport = $res['data']['passport'];
            unset($passport['password'], $passport['salt']);
            session('passport', $passport);
            echo json_encode(array('status' => 'success', 'data' => array('redirect_url' => '/home/product/lists')));
            exit;
        }
        else {
            echo json_encode(array('status' => 'fail', 'msg' => "用户名或密码错误"));
            exit;
        }
    }

    public function register() {
        if (isset($_GET['referrer']) && $_GET['referrer']) {
            cookie('referrer', trim($_GET['referrer']), 86400*30);
        }
        $passportMdl = D("Passport");
        $this->display();
    }

    public function reg() {
        header('Content-type: application/json');
        $p = I("post.");
        $necessaryArgs = array(
            'loginname' => '用户名',
            'password' => '密码',
            'password_confirm' => '确认密码',
        );
        foreach ($necessaryArgs as $k => $v) {
            if (!isset($p[$k]) || empty($p[$k])) {
                echo json_encode(array('status' => 'fail', 'msg' => $v . "不能为空"));
                exit;
            }
        }

        if ($p['password'] != $p['password_confirm']) {
            echo json_encode(array('status' => 'fail', 'msg' => "两次密码不一致"));
            exit;
        }

        $passportMdl = D('Passport');
        $filter = array('loginname' => $p['loginname']);
        $row = $passportMdl->getRow($filter);
        if ($row) {
            echo json_encode(array('status' => 'fail', 'msg' => "用户名已存在"));
            exit;
        }

        $referrer = cookie('referrer');
        if ($referrer) {
            $p['spreader_passport_id'] = \Common\Lib\Idhandler::decode($referrer);
        }

       $passport = $passportMdl->createNew($p);
        if ($passport) {
            unset($passport['password']);
            $this->_setLoginSession($passport);
            echo json_encode(array('status' => 'success', 'data' => array('redirect_url' => '/')));
            exit;
        }
        else {
            echo json_encode(array('status' => 'fail', 'msg' => "注册失败"));
            exit;
        }
    }

    public function logout() {
        session('passport', null);
        $this->redirect('/');
    }

    private function _setLoginSession($passport) {
        session('passport', $passport);
    }
}
