<?php
namespace Pay\Controller;
use Pay\Controller\CommonController;
class AlipayController extends CommonController {
    public function __construct() {
        parent::__construct();
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');
    }
    
    public function index() {
        $this->display();
    }

    public function pay() {
        $alipay_config = C('ALIPAY_CONFIG');
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = $_SERVER["REMOTE_ADDR"]; 
        //构造要请求的参数数组，无需改动
        $parameter = array(
        	"service" => "create_direct_pay_by_user",
        	"partner" => $alipay_config['partner'],
        	"seller_email" => $alipay_config['seller_email'],
        	"payment_type" => C('ALIPAY_SETTING.payment_type'),
        	"notify_url" => C('ALIPAY_SETTING.notify_url'),
        	"return_url" => C('ALIPAY_SETTING.return_url'),
        	"out_trade_no" => trim($_POST['out_trade_no']),
        	"subject" => trim($_POST['subject']),
        	"total_fee"	=> trim($_POST['total_fee']),
        	"body" => trim($_POST['body']),
        	"show_url" => trim($_POST['show_url']),
        	"anti_phishing_key"	=> $anti_phishing_key,
        	"exter_invoke_ip" => $exter_invoke_ip,
        	"_input_charset" => $alipay_config['input_charset']
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
        exit;
    }

    public function notify() {
        \Common\Lib\Utils::log('alipay', 'notify.log', $_POST);
        $alipay_config = C('ALIPAY_CONFIG');
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        
        if ($verify_result) {//验证成功
        	$out_trade_no = $_POST['out_trade_no'];
        	$trade_no = $_POST['trade_no'];
        	$trade_status = $_POST['trade_status'];
            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
        		//判断该笔订单是否在商户网站中已经做过处理
        			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        			//如果有做过处理，不执行商户的业务程序
        				
        		//注意：
        		//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        		//判断该笔订单是否在商户网站中已经做过处理
        			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        			//如果有做过处理，不执行商户的业务程序
        				
        		//注意：
        		//付款完成后，支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        
        	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
                
        	echo "success";		//请不要修改或删除
        }
        else {
            //验证失败
            echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    public function back() {
        \Common\Lib\Utils::log('alipay', 'back.log', $_GET);
        $alipay_config = C('ALIPAY_CONFIG');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {
            //验证成功
        	//商户订单号
        	$out_trade_no = $_GET['out_trade_no'];
            $trans_id = $out_trade_no;
        	//支付宝交易号
        	$trade_no = $_GET['trade_no'];
            //付款方信息
            $paid_account = $_GET['buyer_email'];
            //支付时间
            $paid_at = strtotime($_GET['notify_time']);
        	//交易状态
        	$trade_status = $_GET['trade_status'];
            $trans_mdl = D('TradeTransactions');
            $trade_mdl = D('Trade');

            $trans_filter = array('trans_id' => $trans_id);
            $trans = $trans_mdl->getRow($trans_filter);
            if (!$trans) {
        	    echo "订单不存在<br />";
                exit;
            }
            else {
                $trade_filter = array('id' => $trans['trade_id']);
                $trade = $trade_mdl->getRow($trade_filter);
            }
        
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        		//判断该笔订单是否在商户网站中已经做过处理
                if ('success' == $trans['status']) {
                    echo "不做处理";
                    exit;
                }

                //更新transaction
                $trans_mdl->startTrans();
                $trans_data = array(
                    'status' => 'success',
                    'batch_no' => $trade_no,
                    'paid_account' => $paid_account,
                    'paid_at' => $paid_at,
                    'updated_at' => time(),
                );
                $res = $trans_mdl->where("id='{$trans['id']}'")->save($trans_data);
                \Common\Lib\Utils::log('alipay', 'return.log', $trans_mdl->getLastSql());

                if (false === $res) {
                    $trans_mdl->rollback();
                    exit('更新失败');
                }

                //更新trade
                $trade_data = array(
                    'financial_status' => 'paid',
                    'paid_at' => $paid_at,
                    'updated_at' => time(),
                );
                $res = $trade_mdl->where("id='{$trade['id']}'")->save($trade_data);
                \Common\Lib\Utils::log('alipay', 'return.log', $trans_mdl->getLastSql());

                //计入点击账户
                //查询账户是否存在
                $click_account_mdl = D('ClickAccount');
                $click_account = $click_account_mdl->getRow(array('passport_id' => $trade['passport_id']));
                if ($click_account) {
                    $sql = sprintf("UPDATE click_account SET clicks = clicks + %d WHERE id = %d", $trade['total_clicks'], $click_account['id']);
                    $res = $click_account_mdl->execute($sql);
                    \Common\Lib\Utils::log('alipay', 'return.log', $click_account_mdl->getLastSql());
                    $click_account = $click_account_mdl->getRow(array('passport_id' => $trade['passport_id']));
                }
                else {
                    $click_account_data = array(
                        'passport_id' => $trade['passport_id'],
                        'clicks' => $trade['total_clicks'],
                    );
                    $click_account = $click_account_mdl->createNew($click_account_data);
                    \Common\Lib\Utils::log('alipay', 'return.log', $click_account_mdl->getLastSql());
                }

                //点击账户异动日志
                $click_account_log_mdl = D('ClickAccountLog');
                $des = array('product_id' => $trade['product_id'], 'trade_id' => $trade['id']);
                $log_params = array(
                    'passport_id' => $trade['passport_id'],
                    'changed_clicks' => $trade['total_clicks'],
                    'balance_clicks' => $click_account['clicks'],
                    'description' => json_encode($des),
                );
                $log = $click_account_log_mdl->createNew($log_params);
                \Common\Lib\Utils::log('alipay', 'return.log', $click_account_log_mdl->getLastSql());
                $trans_mdl->commit();

        	    echo "验证成功<br />";
            }
            else {
                echo "trade_status=".$_GET['trade_status'];
            }
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }
}
