<?php
namespace Common\Model;
use Think\Model;
class GoodsSkuModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();

        return $row;
    }

    public function getAll($filter){
        $rows = $this->where($filter)->select();

        return $rows;
    }

    public function getList($shopId='', $limit=10, $page=1){
        $offset = ((int)$page - 1) * (int)$limit;
        $sql = "select p.title,p.id,p.price,po.product_image_uid,po.width, po.height from products p left join photos po on p.id = po.product_id where p.published=1 and p.quantity>0 and p.shop_id = '{$shopId}' group by po.product_id limit {$offset},{$limit}";

        $count_sql = "select count(*) as count from products where shop_id = '{$shopId}' and published=1 and quantity>0";
        $count_rs = $this->query($count_sql);

        $count = $count_rs[0]['count'];

        $rs = $this->query($sql);

        $return = array(
                       'total_pages' => ceil((int)$count/$limit),
                       'curr_page' => (int)$page,
                       'limit' => (int)$limit,
                       'data' => $rs,
                  );  

        return $return;    
    }

    public function createNew($data) {
        $originData = $data;
        $data['sku_id'] = $this->_genSkuId();
        if (!$this->add($data)) {
            return $this->createNew($originData); 
        }
        else {
            return $data;
        }
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
