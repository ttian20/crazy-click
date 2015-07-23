<?php
namespace Common\Model;

class PubModel {
    public $apiexec;
    public function __construct() {
        $this->apiexec = new \Common\Lib\Apiexec;
    }

}
