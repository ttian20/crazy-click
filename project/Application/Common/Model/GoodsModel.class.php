<?php
namespace Common\Model;
use Think\Model;
class GoodsModel extends Model {

    public function getRow($filter){
        $row = $this->where($filter)->find();

        return $row;
    }

    /*
    public function getList($shopId='', $limit=10, $page=1){
        $filter['shop_id'] = $shopId;
        $filter['published'] = 1;
        $filter['quantity'] = array('gt',0);
        $products = $this->where($filter)->limit($limit)->page($page)->select();
        return $products;
    }
     */

    public function createNew($data) {
        $originData = $data;
        $data['goods_id'] = $this->_genGoodsId();
        if (!$this->add($data)) {
            return $this->createNew($originData); 
        }
        else {
            return $data;
        }
    }

    private function _genGoodsId() {
        $length = 8;
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

    public function pCount($shopId=''){
        $filter['shop_id'] = $shopId;
        $filter['published'] = 1;
        $filter['quantity'] = array('gt',0);
        $product_count = $this->where($filter)->count();

        if(!$product_count){
            $product_count = 0;
        }
        return $product_count;
    }

    public function getCount($filter){
        $product_count = $this->where($filter)->count();
        if(!$product_count){
            $product_count = 0;
        }
        return $product_count;
    }

    public function getList($filter, $limit=10, $page=1, $order='update_at desc'){
        $products = $this->where($filter)->order($order)->limit($limit)->page($page)->select();
        if($products){
            $photoMdl = D('Photo');
            foreach($products as &$p){
                $p['image'] = '';
                $id = $p['goods_id'];
                $pFilter = array('goods_id' => $id);
                $image = $photoMdl->getRow($pFilter);
                if($image && $image['url']){
                    $p['image'] = $image['url'];
                }
            }
        }
        return $products;
    }

    public function getAll($shopId='', $limit=10, $page=1){
        $offset = ((int)$page - 1) * (int)$limit;
        $sql = "select g.title,g.goods_id,g.price,po.url,po.width, po.height from goods g left join photo po on p.goods_id = po.goods_id where g.published=1 and g.quantity>0 and g.shop_id = '{$shopId}' group by po.goods_id limit {$offset},{$limit}";

        $count_sql = "select count(*) as count from goods where shop_id = '{$shopId}' and published=1 and quantity>0";
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
}
