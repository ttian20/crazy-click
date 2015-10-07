<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class ProductController extends CommonController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->display();
    }

    public function lists() {
        $productMdl = D('Products');
        $products = $productMdl->getAll();
        $this->assign('products', $products);
        $this->display();
    }

    public function buy() {
        $productMdl = D('Products');
        if (!$_GET['pid']) {
            exit('error link');
        }
        $product = $productMdl->getRow(array('id' => $_GET['pid']));
        \Common\Lib\Utils::log('product', 'buy.log', $product);

        if (!$product) {
            exit('error product');
        }

        //生成订单
        $current = time();
        $tradeMdl = D('Trade');
        $data = array(
            'passport_id' => $this->_passport['id'],
            'product_id' => $product['id'],
            'product_title' => $product['title'],
            'status' => 'active',
            'financial_status' => 'pending',
            'total_price' => $product['price'],
            'total_clicks' => $product['clicks'],
            'created_at' => $current,
            'updated_at' => $current,
        );
        \Common\Lib\Utils::log('product', 'buy.log', $data);

        $trade = $tradeMdl->createNew($data);

        //生成交易单号
        $tradeTransMdl = D('TradeTransactions');
        $transData = array(
            'trade_id' => $trade['id'],
            'passport_id' => $trade['passport_id'],
            'kind' => 'capture',
            'amount' => $trade['total_price'],
            'status' => 'pending',
            'created_at' => $current,
            'updated_at' => $current,
        );
        $trans = $tradeTransMdl->createNew($transData);

        //生成交易链接
        $sHtml = "<form id='buyfrm' name='buyfrm' action='http://pay.shopflow.cn/alipay/pay' method='post'>";
        $sHtml.= "<input type='hidden' name='out_trade_no' value='" . $trans['trans_id'] . "'/>";
        $sHtml.= "<input type='hidden' name='subject' value='" . $trade['product_title'] . "'/>";
        $sHtml.= "<input type='hidden' name='total_fee' value='" . $trade['total_price'] . "'/>";
        $sHtml.= "<input type='hidden' name='body' value='" . strip_tags($product['description']) . "'/>";
        $sHtml.= "<input type='hidden' name='show_url' value='http://www.shopflow.cn/home/product/lists' />";

		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='确认'></form>";
		$sHtml = $sHtml."<script>document.forms['buyfrm'].submit();</script>"; 
        echo $sHtml;
        exit;
    }
}
