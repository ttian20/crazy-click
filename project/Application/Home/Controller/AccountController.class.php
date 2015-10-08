<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class AccountController extends CommonController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $click_account_mdl = D('ClickAccount');
        $click_account_log_mdl = D('ClickAccountLog');
        $filter = array('passport_id' => $this->_passport['id']);
        $count = $click_account_log_mdl->getCount($filter);
        $click_account = $click_account_mdl->getRow($filter);

        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $logs = $click_account_log_mdl->getLists($filter);
        $this->assign('page', $pagination);
        $this->assign('logs', $logs);
        $this->display();
    }
}
