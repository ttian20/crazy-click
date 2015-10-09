<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class ProductController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $productMdl = D('Products');
        $count = $productMdl->count();
        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $products = $productMdl->getLists(array(), $page);
        $this->assign('products', $products);
        $this->assign('page', $pagination);
        $this->display();
    }

    public function add() {
        $this->assign('act', 'add');
        $this->display('Product/form');
    }

    public function edit() {
        if (!$_GET['id']) {
            $this->redirect('/product');
        }
        $productMdl = D('Products');
        $product = $productMdl->getRow(array('id' => $_GET['id']));

        $this->assign('product', $product);
        $this->assign('act', 'edit');
        $this->display('Product/form');
    }

    public function save() {
        if (!$_POST) {
            $this->error('系统错误');
        }
        $p = I('post.');
        if (trim($p['title']) == '') {
            $this->error('商品名称不能为空');
        }
        if (trim($p['price']) == '') {
            $this->error('商品价格不能为空');
        }
        if (trim($p['description']) == '') {
            $this->error('商品描述不能为空');
        }

        $productMdl = D('Products');
        if ('add' == $p['act']) {
            $data = array(
                'title' => trim($p['title']),
                'price' => trim($p['price']),
                'description' => trim($p['description']),
                'published' => 1,
            );
            if ($productMdl->createNew($data)) {
                $this->success(array('redirect_url' => '/product'));
            }
            else {
                $this->error($productMdl->getDbError());
            }
        }
        else {
            $data = array(
                'title' => trim($p['title']),
                'price' => trim($p['price']),
                'description' => trim($p['description']),
                'published' => 1,
                'updated_at' => time(),
            );
            if ($productMdl->where("id={$p['product_id']}")->save($data)) {
                $this->success(array('redirect_url' => '/product'));
            }
            else {
                $this->error($productMdl->getDbError());
            }
        }
    }
}
