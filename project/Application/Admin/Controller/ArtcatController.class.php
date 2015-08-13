<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ArtcatController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $artcatMdl = D('Artcat');
        $count = $artcatMdl->count();
        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $artcats = $artcatMdl->getLists(array(), $page);
        $this->assign('artcats', $artcats);
        $this->assign('page', $pagination);
        $this->display();
    }

    public function add() {
        $this->assign('act', 'add');
        $this->display('Artcat/form');
    }

    public function edit() {
        if (!$_GET['id']) {
            $this->redirect('/artcat');
        }
        $artcatMdl = D('Artcat');
        $filter = array('id', $_GET['id']);
        $cat = $artcatMdl->getRow($filter);

        $this->assign('cat', $cat);
        $this->assign('act', 'edit');
        $this->display('Artcat/form');
    }

    public function save() {
        if (!$_POST) {
            $this->error('系统错误');
        }
        $p = I('post.');
        if (trim($p['catname']) == '') {
            $this->error('类型名称不能为空');
        }
        $artcatMdl = D('Artcat');
        if ('add' == $p['act']) {
            $filter = array('catname' => $p['catname']);
            if ($artcatMdl->getRow($filter)) {
                $this->error('类型名称已存在');
            }
            if ($artcatMdl->createNew($filter)) {
                $this->success(array('redirect_url' => '/artcat'));
            }
            else {
                $this->error($artcatMdl->getDbError());
            }
        }
        else {
            $filter = array('catname' => $p['catname'], 'id' => array('neq', $p['catid']));
            if ($artcatMdl->getRow($filter)) {
                $this->error('类型名称已存在');
            }
            $data = array('catname' => $p['catname'], 'updated_at' => time());
            if ($artcatMdl->where("id={$p['catid']}")->save($data)) {
                $this->success(array('redirect_url' => '/artcat'));
            }
            else {
                $this->error($artcatMdl->getDbError());
            }
        }
    }
}
