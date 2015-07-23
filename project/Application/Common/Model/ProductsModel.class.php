<?php
namespace Common\Model;
use Think\Model;
class ProductsModel extends Model {

    public function getRow($filter) {
        $row = $this->where($filter)->find();
        if($row){
            $row['product_id'] = \Common\Lib\Idhandler::encode($row['product_id']);
        }
        return $row;
    }

    public function getAllProducts($filter) {
        $rows = $this->where($filter)->select();
        return $rows;
    }

    public function createNew($data) {
        $product_id = $this->add($data);
        if ($product_id) {
            $data['product_id'] = \Common\Lib\Idhandler::encode($product_id);
            return $data;
        }
        else {
            return false;
        }

/*        $originData = $data;
        $data['product_id'] = $this->_genProductId();
        if (!$this->add($data)) {
            return $this->createNew($originData); 
        }
        else {
            return $data;
        }*/
    }

    private function _genProductId() {
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
            $photosMdl = D('Photo');
            foreach($products as &$p){
                $p['image'] = '';
                $id = $p['product_id'];
                $pFilter = array('product_id' => $id, 'position' => 0);
                $image = $photosMdl->getRow($pFilter);
                if ($image) {
                    $p['image'] = $image['url'];
                }
                $p['product_id'] = \Common\Lib\Idhandler::encode($p['product_id']);
            }
        }
        return $products;
    }

    public function getAll($shopId='', $limit=10, $page=1){
        $offset = ((int)$page - 1) * (int)$limit;
        //$sql = "select p.published,p.title,p.product_id,p.price,po.url,po.width, po.height, po.filename from products p left join photo po on p.product_id = po.product_id where p.shop_id = '{$shopId}' and p.quantity>0 and p.status = 1 and online = 1 group by po.product_id order by p.published desc limit {$offset},{$limit}";
        $sql = "select p.published,p.title,p.product_id,p.price,po.url,po.width, po.height, po.filename from products p left join photo po on p.product_id = po.product_id where p.shop_id = '{$shopId}' and p.status = 1 and online = 1 group by po.product_id order by p.published desc limit {$offset},{$limit}";

        //$count_sql = "select count(*) as count from products where status = 1 and shop_id = '{$shopId}' and quantity>0 and online = 1";
        $count_sql = "select count(*) as count from products where status = 1 and shop_id = '{$shopId}' and online = 1";
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

    public function getAllPublished($shopId='', $limit=10, $page=1){
        $offset = ((int)$page - 1) * (int)$limit;
        //$sql = "select p.published,p.title,p.product_id,p.price,po.url,po.width, po.height, po.filename from products p left join photo po on p.product_id = po.product_id where p.shop_id = '{$shopId}' and p.status = 1 group by po.product_id order by p.published desc limit {$offset},{$limit}";
        $sql = "select p.published,p.title,p.product_id,p.price,po.url,po.width, po.height, po.filename from products p left join photo po on p.product_id = po.product_id where p.shop_id = '{$shopId}' and p.status = 1 and p.online = 1 and po.position = 0 group by po.product_id order by p.published desc limit {$offset},{$limit}";

        $count_sql = "select count(*) as count from products where shop_id = '{$shopId}' and status=1 and online = 1";
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

    public function getInfo($id){
        $sql = "select p.title,p.product_id,p.price,p.is_promoted,p.commission,po.url,po.width, po.height from products p left join photo po on p.product_id = po.product_id where p.product_id = '{$id}'";
        $rs = $this->query($sql);

        if($rs){
            $rs[0]['product_id'] = \Common\Lib\Idhandler::encode($rs[0]['product_id']);
            return $rs[0];
        }
        return array();    
    }

    public function getInfoByPosition($id){
        $sql = "select p.title,p.product_id,p.price,p.is_promoted,p.commission,po.url,po.width, po.height from products p left join photo po on p.product_id = po.product_id where p.product_id = '{$id}' and po.position = 0";
        $rs = $this->query($sql);

        if($rs){
            $rs[0]['product_id'] = \Common\Lib\Idhandler::encode($rs[0]['product_id']);
            return $rs[0];
        }
        return array();    
    }
}
