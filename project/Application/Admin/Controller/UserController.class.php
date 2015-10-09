<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class UserController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $userMdl = D('User');
        $count = $userMdl->count();
        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $users = $userMdl->getLists(array(), $page);
        $this->assign('users', $users);
        $this->assign('page', $pagination);
        $this->display();
    }
}
