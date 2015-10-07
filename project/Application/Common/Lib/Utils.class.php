<?php
namespace Common\Lib;

class Utils
{
    public function __construct() {

    }

    public function qrUrl($url='',$size='144') {
        //$str = "http://omnisale.cn/qr?size=".$size."&txt=".urlencode($url);
        $str = "http://qr.wdwd.com/qr?size=".$size."&txt=".urlencode($url);
        return $str;
    }

    public function pagination($count, $limit=10, $parameter = array()) {
        if ($count < $limit) {
            return '';
        }
        $Page = new \Common\Lib\Pagination((int)$count, $limit, $parameter);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        return $show;
    } 

    //获取ac
    public function get_ac(&$params){
        $tmp_verfy = '';
        $params['time'] = time();
        ksort($params);
        $token = date('Ymd',$params['time']);
        foreach ($params as $k => $v) {
            $tmp_verfy.=$params[$k];
        }
        $params['ac']=strtolower(md5(trim($tmp_verfy.$token)));

        return $params;
    }

    public function ob2ar($obj) {
        if(is_object($obj)) {
             $obj = (array)$obj;
             $obj = $this->ob2ar($obj);
        }elseif(is_array($obj)) {
            foreach($obj as $key => $value) {
                $obj[$key] = $this->ob2ar($value);
            }
        }
        return $obj;
   }


   public function arrurlencode($obj) {
        if(is_array($obj)) {
            foreach($obj as $key => &$value) {
                $value = $this->arrurlencode($value);
            }
        }else{
            $obj = urlencode($obj);
        }
        return $obj;
   }

   public static function log($type, $filename, $data) {
        $path = RUNTIME_PATH . 'Logs/' . $type;
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }   
        $filename = date('Ymd').'_'.$filename;
        $file = $path . "/" . $filename;
        if (is_string($data)) {
            $msg = $data . "\n";
        }   
        elseif (is_array($data)) {
            $msg = print_r($data, true);
        }   
        else {
            $msg = serialize($data) . "\n";
        }   
        $msg = date("Y-m-d H:i:s") . "\n" . $msg;
        error_log($msg, 3, $file);
    } 

    public static function cashier_sign($data){
        $data['source'] = C('CASHIER_KEY');
        $secret = C('CASHIER_SECRET');
        ksort($data);
        $d_params = array();

        foreach($data as $k => &$da){
            if(is_array($da)){
                $da = json_encode($da);
            }
            $d_params[] = $k . '='. $da;
        }

        $sign = md5(implode('&', $d_params) . $secret);

        $data['sign'] = $sign;
        
        return $data;
    }
   
    public static function isMobile() {
        //判断手机发送的客户端标志
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
             $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
             $clientkeywords = array(
                  'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-'
                  ,'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
                 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini',
                'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if(preg_match("/(".implode('|',$clientkeywords).")/i",$userAgent)&&strpos($userAgent,'ipad') === false)
            {
                 return true;
            }
        }
        return false;
    }

    public static function managerApi($url, $data, $method='get'){
         $curl = new \Common\Lib\Curl;
         $curl->setResponseDecode(true);
         $res = $curl->{$method}( C('MANAGER_API_URL') . $url, $data); 
         return $res;
    }


    public static function genApiSign($data){
        $secret = strtoupper(md5($data['appkey'].'^_^'.'iloveshopex'));
        ksort($data);
        $d_params = array();
        foreach($data as $k => &$da){
            if(is_array($da)){
                $da = json_encode($da);
            }    
            $d_params[] = $k . '='. $da; 
        }    
        $sign = md5(implode('&', $data) . $secret);

        return $sign;
    }

    public function buildInnerUrl($url, $spm) {
        if(!$spm){
            return $url;
        }
        $urlArr = explode('?', $url);
        $query = $urlArr[1];
        if (!$query) {
            $params = array();
            $params['spm'] = $spm;
            $queryStr = http_build_query($params);
            $url = $urlArr[0] . '?' . $queryStr;
        }   
        else {
            parse_str($query, $params);
            $params['spm'] = $spm;
            $queryStr = http_build_query($params);
            $url = $urlArr[0] . '?' . $queryStr;
        }   

        return $url;
    }

    public function set_cookie($name='', $value='', $domain='wdwd.com', $expired=0){
        if($name){
            if($expired){
                $expired = time() + $expired;
            }
            setcookie($name, $value, $expired, '/', $domain);
            $_COOKIE[$name] = $value;
        }
    }

    public function replace_html($str) {
        $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签
        $str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签
        $str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); //过滤frame标签
        $str=preg_replace("/<(\/?i?frame.*?)>/si","",$str); //过滤frame标签 
        $str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签
        $str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签
        $str=preg_replace("/on([a-z]+)\s*=/si","\\1=",$str); //过滤script标签
        $str=preg_replace("/&#/si","&＃",$str); //过滤script标签，如javAsCript:alert( 
        return $str; 
    }
}
