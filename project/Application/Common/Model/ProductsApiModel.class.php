<?php
namespace Common\Model;
use Common\Model\PubModel;
class ProductsApiModel extends PubModel {

    //获得店铺的标签
    public function tags($params) {
        return $this->apiexec->goApi('tag/shop/'.$params['shop_id'].'/topn', $params, 'get');
    }

    //获得商品列表
    public function getList($params) {
        return $this->apiexec->goApi('v0/product', $params, 'get');
    }

    //根据tag id查询商品
    public function getListByTag($params) {
        $shop_id = $params['shop_id'];
        unset($params['shop_id']);
        return $this->apiexec->goApi('admin/shop/'.$shop_id.'/product/list-by-tag', $params, 'get');
    }

    //根据搜索条件获得商品类表
    public function searchList($params) {
        $shop_id = $params['shop_id'];
        unset($params['shop_id']);
        return $this->apiexec->goApi('admin/shop/'.$shop_id.'/product/search-like', $params, 'get');
    }

    //根据店铺id，商品ID获得商品信息
    public function getInfoByIds($params) {
        return $this->apiexec->goApi('admin/shop/'.$params['shop_id'].'/product/'.$params['product_id'], array(), 'get');
    }

    public function getinfo($params){
        $id = $params['id'];
        $productInfo = $this->apiexec->goApi('v0/product/'.$id, array(), 'get');

        $data = array('status' => 'error');
        if(($productInfo['status'] == 'success') && isset($productInfo['data']['product_id']) && $productInfo['data']['product_id']){
            $product = $productInfo['data'];

            //处理信息详情
            $detail = '';
            if($product['yl_body']){
                 $detail = preg_replace('!http://([^\.]+).taobaocdn.com/!i', 'http://yunmao-pic.qiniudn.com/', $product['yl_body']);

                 $detail = preg_replace("/src=\"([^\"]+(?:jpg|bmp|png|gif|jpeg))\"/i","  class=\"lazy-loading\" data-original=\"\${1}\"",$detail);
            }

            /*
            * 新数据处理方式完成，不在使用原有字段
            elseif($product['body_html']){
              $detail = preg_replace('!http://([^\.]+).taobaocdn.com/!i', 'http://yunmao-pic.qiniudn.com/', $product['body_html']);
            }
            */

            //处理图片
            //$new_product['detail'] = preg_replace('!http://([^\.]+).taobaocdn.com/!i', 'http://yunmao-pic.qiniudn.com/', $product['body_html']);;
            unset($product['body_html'], $product['yl_body']);
            $product['detail'] = $detail;

            $product['time_limited_start_format'] = '';
            $product['time_limited_end_format'] = '';
            if($product['time_limited_start'] > 0){
                $product['time_limited_start_format'] = date('Y.m.d H:i', $product['time_limited_start']);     
            }

            if($product['time_limited_end'] > 0){ 
                $product['time_limited_end_format'] = date('Y.m.d H:i', $product['time_limited_end']);
            }
             
            $product['yl_desc'] = $product['yl_desc']?strip_tags($product['yl_desc']):'';

            $img = array();
            $images = array();
            
            $product['large_img'] =preg_replace('!http://([^\.]+).taobaocdn.com/!i', 'http://yunmao-pic.qiniudn.com/', $product['img']);

            if($images){
                foreach($images as $image){
                    $img[] = preg_replace('!http://([^\.]+).taobaocdn.com/!i', 'http://yunmao-pic.qiniudn.com/', $image['url']);
                }
            }

            $product['imgs'] = $img;

            //获得限购数量
            
            $quota_num = 9999;
            if($product['product_quota'] && ($product['product_quota']['num'] > 0)){
                $quota_num = (int)$product['product_quota']['num']; 
            }
            $product['product_quota'] = $quota_num;

            /*
            $wireless = array();
            if(isset($product['wireless_desc']) && $product['wireless_desc']) {
                include_once dirname(__FILE__) . "/../../lib/xmlparser.php";
                $wireless_desc = $product['wireless_desc'];
                $xml_parser = new wapdesc_decoder($wireless_desc);
                $xml_short_desc = $xml_parser->shot_desc;
                $xml_output = $xml_parser->output;
                       
                $wireless['short_desc'] = $xml_short_desc;
                $wireless['output'] = $xml_output;
            }
                        
            $new_product['wireless_desc'] = $wireless;
             */

            //获得sku的分类
            $props = array();
            $new_sku = array();

            $options = $product['product_option_arr'];
            $encode_product_id = $product['product_id'];
            $decode_product_id = \Common\Lib\Idhandler::decode($product['product_id']);
            if(!$options){
                $options = array(0=>array('id' => $encode_product_id, 'name'=> '默认规格', 'position' => 0, 'product_id' => $encode_product_id));
            }

            $skus = $product['sku_arr'];
            if($options){
                foreach($options as $o_k=>$opt){
                    $props[$o_k]['label'] = $opt['name'];
                    $position = $opt['position'] + 1;
                    //$code = $opt['code'];
                    $code = $opt['id'].'_'.$opt['product_id'];
                    $props[$o_k]['list'] = array();

                    
                    if(count($skus) > 0){
                        foreach($skus as $s_k=>$sku){
                            $sku_option = $sku['option'.$position]?$sku['option'.$position]:'默认'; 
                            $props[$o_k]['list'][$s_k] = array('name' => $sku_option, 'type' => $code);
                        }
                    }
                }
            }

            //获得SKU
            //判断是否卖光
            $sale_out = true;
            $prices = array();

            if(count($skus) > 0){
                foreach($skus as $k=>$sk){
                    $new_sku[$k]['stock'] = $sk['quantity_setting']?$sk['inventory_quantity']:999999;
                    $new_sku[$k]['price'] = number_format((float)$sk['price'],2);
                    $new_sku[$k]['id'] = $sk['sku_id'];
                    foreach($options as $v){
                        $v_position = $v['position']+1;
                        $v_code = $v['id'].'_'.$v['product_id'];
                        $new_sku[$k][$v_code] = $sk['option'.$v_position]?$sk['option'.$v_position]:'默认';
                    }

                    $new_sku[$k]['name'] = $sk['option1'] . ' ' . $sk['option2'] . ' ' . $sk['option3'] . ' ' . $sk['option4'];
                    $new_sku[$k]['payurl'] = $sk['wx_payurl'];

                    //判断库存
                    if($sale_out){
                        if($sk['quantity_setting'] == '0' || $sk['inventory_quantity'] > 0){
                            $sale_out = false;
                        }
                    }

                    $prices[] = (float)$sk['price'];
                }
            }

            sort($prices);

            $product['props'] = $props;
            $product['sku'] = $new_sku;
            $product['props_json'] = json_encode($props);
            $product['sku_json'] = json_encode($new_sku);
            $product['sale_out'] = $sale_out;

            //获取价格区间
            $product['min_price'] = number_format($prices[0],2); 
            $product['max_price'] = number_format(end($prices),2);

            $product['min_price_orig'] = $prices[0]; 
            $product['max_price_orig'] = end($prices);

            $coupons = false;
            //优惠券信息
            /*
            $sql = "select conditions from coupons c where shop_id = ? and status = ? and open_receive = ? and c.key = ? and start_using_end > ?";
            $sth = $this->dbconn->prepare($sql);
            if($sth->execute(array($shop_id, '1', '1', 'buy_product_promotion_money', time()))){
                $conditions = $sth->fetchAll(PDO::FETCH_ASSOC);
                if($conditions){
                    foreach($conditions as $cond){
                        if(!$coupons){
                            $cond = json_decode($cond['conditions'], true);
                            $conds = explode(',', $cond['value']);
                            if(in_array($encode_product_id, $conds)){
                                $coupons = true;
                            }
                        }
                    }
                }
            }
             */
            $couponsInfo = $this->apiexec->goApi('coupon/list', array('product_id' => $id, 'shop_id' => $product['shop_id'],  'key' => 'buy_product_promotion_money', 'limit' => json_encode(array('limit' => 1, 'offset' => 0))), 'get');
            if($couponsInfo['status'] == 'success' && $couponsInfo['data']['count'] > 0){
                $coupons = true;
            }

            $product['item_coupons'] = $coupons;

            $data = array('status' => 'success', 'data' => $product);
        }

        return $data;
    }

}
