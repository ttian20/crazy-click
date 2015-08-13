<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class IndexController extends CommonController {
    public function __construct() {
        $this->setLoginExcept(array('login', 'verify', 'register', 'reg', 'logout'));
        parent::__construct();
    }
    
    public function index() {
        $this->display();
    }

    public function login() {
        if ($_GET['err']) {
            $this->assign('error', $_GET['err']);
        }
        $this->display();
    }

    public function verify() {
        $p = I("post.");
        $userMdl = D("User");
        $res = $userMdl->verify($p['loginname'], $p['password']);
        if ('success' == $res['status']) {
            //写session
            $this->_setLoginSession($res['data']['user']);
            $this->redirect('/');
        }
        else {
            $this->redirect('/login?err=' . $res['msg']);
        }
    }

    public function register() {
        $p = I("post.");
        $userMdl = D("User");
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

        $userMdl = D('User');
        $filter = array('loginname' => $p['loginname']);
        $row = $userMdl->getRow($filter);
        if ($row) {
            echo json_encode(array('status' => 'fail', 'msg' => "用户名已存在"));
            exit;
        }

        $user = $userMdl->createNew($p);
        if ($user) {
            $this->_setLoginSession($user);
            echo json_encode(array('status' => 'success', 'data' => array('redirect_url' => '/')));
            exit;
        }
        else {
            echo json_encode(array('status' => 'fail', 'msg' => "注册失败"));
            exit;
        }
    }

    public function logout() {
        $this->display();
    }

    private function _setLoginSession($user) {
        session('user', $user);
    }
}
