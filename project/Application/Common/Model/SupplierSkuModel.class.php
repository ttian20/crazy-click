<?php
namespace Common\Model;
use Think\Model;
class SupplierSkuModel extends Model {
    public function getRow($filter) {
        $row = $this->where($filter)->find();
        return $row;
    }

    public function getAll($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function getSkusBySupplierId($supplierId) {
        $sql = "SELECT sku.product_id, sku.sku_id, p.title, sku.option1, sku.option2, sku.option3, sku.option4, sku.sku_code, sku.min_add_price, sku.max_add_price, sku.price, sku.inventory_quantity, sku.quantity_setting "
             . "FROM supplier_sku sku "
             . "INNER JOIN suppier_products p ON sku.product_id = p.product_id AND p.status = 1 "
             . "WHERE sku.supplier_id = '{$supplierId}' AND sku.deleted = 0";

        $skus = $this->query($sql);
        return $skus;
    }

    public function checkSku($supplierId, $sku, $skuId = 0) {
        if ('' == $sku) {
            return true;
        }

        $filter = array('supplier_id' => $supplierId, 'sku' => $sku);
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
