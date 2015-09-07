<?php
namespace Home\Controller;
use Think\Controller;
class ProductController extends Controller {
    public function index() {
        $this->display();
    }

    public function lists() {
        $productMdl = D('Products');
        $products = $productMdl->getAll();
        $this->assign('products', $products);
        $this->display();
    }
}
