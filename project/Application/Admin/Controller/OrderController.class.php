<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class OrderController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $tradeMdl = D('Trade');
        $count = $tradeMdl->count();
        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $trades = $tradeMdl->getLists(array(), $page);
        $this->assign('trades', $trades);
        $this->assign('page', $pagination);
        $this->display();
    }
}
