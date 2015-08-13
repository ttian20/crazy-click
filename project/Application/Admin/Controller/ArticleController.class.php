<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ArticleController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $articleMdl = D('Article');
        $count = $articleMdl->count();
        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $articles = $articleMdl->getLists(array(), $page);
        $this->assign('articles', $articles);
        $this->assign('page', $pagination);
        $this->display();
    }

    public function add() {
        if ($_GET['err']) {
            $this->assign('error', $_GET['err']);
        }
        $artcatMdl = D('Artcat');
        $cats = $artcatMdl->getAll();
        $this->assign('cats', $cats);
        $this->assign('act', 'add');
        $this->display('Article/form');
    }

    public function del() {
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

    public function edit() {
        if (!$_GET['id']) {
            $this->redirect('/article');
        }
        $artcatMdl = D('Artcat');
        $cats = $artcatMdl->getAll();

        $articleMdl = D('Article');
        $article = $articleMdl->getRow(array('id' => $_GET['id']));

        $this->assign('cats', $cats);
        $this->assign('article', $article);
        $this->assign('act', 'edit');
        $this->display('Article/form');
    }

    public function save() {
        if (!$_POST) {
            $this->error('系统错误');
        }
        $p = I('post.');
        if (trim($p['catid']) == '') {
            $this->error('请选择分类');
        }
        if (trim($p['title']) == '') {
            $this->error('标题不能为空');
        }
        if (trim($p['content']) == '') {
            $this->error('内容不能为空');
        }
        $artcatMdl = D('Artcat');
        $filter = array('id' => $p['catid']);
        if (!$artcatMdl->getRow($filter)) {
            $this->error('分类不存在');
        }

        $articleMdl = D('Article');
        $user = session('user');
        if ('add' == $p['act']) {
            $data = array(
                'title' => trim($p['title']),
                'user_id' => $user['id'],
                'artcat_id' => $p['catid'],
                'content' => trim($p['content']),
            );
            if ($articleMdl->createNew($data)) {
                $this->success(array('redirect_url' => '/article'));
            }
            else {
                $this->error($articleMdl->getDbError());
            }
        }
        else {
            $data = array(
                'title' => trim($p['title']),
                'artcat_id' => $p['catid'],
                'content' => trim($p['content']),
                'updated_at' => time(),
            );
            if ($articleMdl->where("id={$p['article_id']}")->save($data)) {
                $this->success(array('redirect_url' => '/article'));
            }
            else {
                $this->error($articleMdl->getDbError());
            }
        }
    }
}
