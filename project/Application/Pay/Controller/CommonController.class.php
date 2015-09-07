<?php
namespace Pay\Controller;
use Think\Controller;
class CommonController extends Controller {
    protected $_loginExcept = array();
    public function __construct() {
        parent::__construct();
    }

    protected function error($msg) {
        header('Content-type: application/json');
        echo json_encode(array('status' => 'fail', 'msg' => $msg));
        exit;
    }

    protected function success($data) {
        header('Content-type: application/json');
        echo json_encode(array('status' => 'success', 'data' => $data));
        exit;
    }
}
