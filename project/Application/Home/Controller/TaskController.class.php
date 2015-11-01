<?php
namespace Home\Controller;
use Home\Controller\CommonController;
class TaskController extends CommonController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $tasks_mdl = D('Tasks');
        $tasks_filter = array('passport_id' => $this->_passport['id']);
        $count = $tasks_mdl->getCount($tasks_filter);

        $utils = new \Common\Lib\Utils();
        $pagination = $utils->pagination($count, C('PAGE_LIMIT'));

        if (!isset($_GET['p']) || !$_GET['p']) {
            $page = 1;
        }
        else {
            $page = intval($_GET['p']);
        }
        $tasks = $tasks_mdl->getLists($tasks_filter);
        $this->assign('page', $pagination);
        $this->assign('tasks', $tasks);

        $taskTypes = $this->_getSearchTypes();
        $this->assign('taskTypes', $taskTypes);
        $this->display();
    }

    public function add() {
        $taskType = $_GET['platform'];
        $taskTypes = $this->_getSearchTypes();
        $this->assign('taskTypes', $taskTypes);
        $this->assign('taskType', $taskType);
        $this->display();
    }

    protected function _getSearchTypes() {
        $taskTypes = array(
            array('label' => '淘宝搜索', 'value' => 'tbpc_c'),
            array('label' => '天猫搜索', 'value' => 'tbpc_b'),
            array('label' => '京东搜索', 'value' => 'jdpc'),
        );
        return $taskTypes;
    }

    public function doAdd() {
        $p = I("post.");

        //输入检查
        //
        //账户检查
        $click_account_mdl = D('ClickAccount');
        $click_account_log_mdl = D('ClickAccountLog');
        $tasks_mdl = D('Tasks');
        $click_account_filter = array('passport_id' => $this->_passport['id'], 'type' => '');
        $click_account = $click_account_mdl->getRow($click_account_filter);
        if (!$click_account || $click_account['clicks'] < $p['times']) {
            $this->error('账户点击数余额不足，当前余额为' . $click_account['clicks'] . ', 请去充点!');
        }
        else {
            //扣除点数
            $click_account_mdl->startTrans();
            $click_account_sql = sprintf("UPDATE click_account SET clicks = clicks - %d, updated_at = %d WHERE id = %d", $p['times'], time(), $click_account['id']);

            \Common\Lib\Utils::log('task', 'add.log', "the sql is {$click_account_sql}");
            $res = $click_account_mdl->execute($click_account_sql);

            //点击账户异动日志
            $des = array('product_id' => $trade['product_id'], 'trade_id' => $trade['id']);
            $log_params = array(
                'passport_id' => $this->_passport['id'],
                'changed_type' => 'task',
                'changed_clicks' => $p['times'] * -1,
                'balance_clicks' => $click_account['clicks'] - $p['times'],
                'description' => '', //先留空，之后补充
            );
            $click_account_log = $click_account_log_mdl->createNew($log_params);
            \Common\Lib\Utils::log('task', 'add.log', $click_account_log_mdl->getLastSql());
            $click_account_mdl->commit();
        }
        //
        //请求api
        $api = new \Common\Lib\Api(C('CS_CONFIG'));

        $data = $p;
        switch ($p['platform']) {
            case 'tbpc_b':
                $method = 'tbpc/add';
                $platform = 'tbpc';
                $data['shop_type'] = 'b';
                break;
            case 'tbpc_c':
                $method = 'tbpc/add';
                $platform = 'tbpc';
                $data['shop_type'] = 'c';
                break;
            case 'jdpc':
                $method = 'jdpc/add';
                $platform = 'jdpc';
                $data['shop_type'] = 'b';
                break;
        }
        $data = $this->_buildApiData($platform, $data);
        
        $res = $api->request($method, $data);
        \Common\Lib\Utils::log('task', 'add.log', $method);
        \Common\Lib\Utils::log('task', 'add.log', $data);
        \Common\Lib\Utils::log('task', 'add.log', $res);
        if ('success' == $res['status']) {
            $kid = $res['data']['id'];
            //写入tasks
            $task_data = $this->_buildTaskData($kid, $platform, $data);
            $task_res = $tasks_mdl->createNew($task_data);
            \Common\Lib\Utils::log('task', 'add.log', $tasks_mdl->getLastSql());

            //写入click异动原因
            $des = json_encode(array('kid' => $kid));
            $sql = sprintf("UPDATE click_account_log SET description = '%s', updated_at = %d WHERE id = %d", $des, time(), $click_account_log['id']);
            $res = $click_account_log_mdl->execute($sql);
            \Common\Lib\Utils::log('task', 'add.log', $click_account_log_mdl->getLastSql());
            $this->success(array($p, $data, $res));
        }
        else {
            //退回点数
            $click_account_mdl->startTrans();
            $click_account_sql = sprintf("UPDATE click_account SET clicks = clicks + %d, updated_at = %d WHERE id = %d", $p['times'], time(), $click_account['id']);

            \Common\Lib\Utils::log('task', 'add.log', "the sql is {$click_account_sql}");
            $res = $click_account_mdl->execute($click_account_sql);

            //click_account_log失效
            $des = json_encode(array('kid' => $res['data']['id']));
            $sql = sprintf("UPDATE click_account_log SET is_deleted = 1, updated_at = %d WHERE id = %d", time(), $click_account_log['id']);
            $res = $click_account_log_mdl->execute($sql);
            \Common\Lib\Utils::log('task', 'add.log', $click_account_log_mdl->getLastSql());

            $click_account_mdl->commit();
            $this->error('系统错误');
        }
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

    private function _buildApiData($platform, $p) {
        $data = array(
            'kwd' => trim($p['kwd']),
            'nid' => trim($p['nid']),
            'shop_type' => $p['shop_type'],
            'times' => trim($p['times']),
            'sleep_time' => 20,
            'click_start' => trim($p['click_start']),
            'click_end' => trim($p['click_end']),
            'status' => 'active',
            'begin_time' => trim($p['begin_time']),
            'end_time' => trim($p['begin_time']),
        );
        switch ($platform) {
            case 'tbpc':
                if ('c' == $p['shop_type']) {
                    $data['path1'] = 100;
                    $data['path2'] = 0;
                    $data['path3'] = 0;
                }
                else {
                    $data['path1'] = 0;
                    $data['path2'] = 0;
                    $data['path3'] = 100;
                }
                break;
            case 'jdpc':
                break;
        }
        return $data;
    }

    private function _buildTaskData($kid, $platform, $p) {
        $data = array(
            'id' => $kid,
            'passport_id' => $this->_passport['id'],
            'kwd' => trim($p['kwd']),
            'nid' => trim($p['nid']),
            'platform' => $platform,
            'shop_type' => $p['shop_type'],
            'times' => trim($p['times']),
            'begin_time' => strtotime(trim($p['begin_time'])),
            'end_time' => strtotime(trim($p['begin_time'])),
            'click_start' => trim($p['click_start']),
            'click_end' => trim($p['click_end']),
            'status' => trim($p['status']),
        );
        return $data;
    }
}
