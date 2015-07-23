<?php
namespace Common\Model;
use Think\Model;
class ProductVariantsModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function getSkusByShopId($shopId) {
        $sql = "SELECT sku.product_id, sku.sku_id, p.title, sku.option1, sku.option2, sku.option3, sku.sku, sku.original_price, sku.price, sku.inventory_quantity, sku.quantity_setting "
             . "FROM product_variants sku "
             . "INNER JOIN products p ON sku.product_id = p.product_id AND p.status = 1 "
             . "WHERE sku.shop_id = '{$shopId}' AND sku.deleted = 0";

        $skus = $this->query($sql);
        return $skus;
    }

    public function checkSku($shopId, $sku, $skuId = 0) {
        if ('' == $sku) {
            return true;
        }

        $filter = array('shop_id' => $shopId, 'sku' => $sku);
        $row = $this->getRow($filter);
        if (!$row) {
            //没查到相关记录
            return true;
        }
        elseif (!$skuId) {
            //有相关记录，skuId为0， 意味着新增加的sku
        }
        else {
            //有相关记录，skuId不为0， 意味着被编辑的sku
            if ($skuId == $row['sku_id']) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    public function createNew($data) {
        $sku_id = $this->add($data);
        if ($sku_id) {
            $data['sku_id'] = $sku_id;
            return $data;
        }
        else {
            return false;
        }

/*        $originData = $data;
        $data['sku_id'] = $this->_genSkuId();
        if (!$this->add($data)) {
            return $this->createNew($originData); 
        }
        else {
            return $data;
        }*/
    }

    private function _genSkuId() {
        $length = 10;
        $string = '0123456789abcdefghijklmnopqrstuvwxyz';
        $min = 0;
        $max = strlen($string) - 1;
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $rand = rand($min, $max);
            $id .= $string{$rand}; 
        }
        return $id;
    }
}
