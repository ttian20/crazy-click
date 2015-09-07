<?php
namespace Pay\Controller;
use Pay\Controller\CommonController;
class IndexController extends CommonController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->display();
    }
}
