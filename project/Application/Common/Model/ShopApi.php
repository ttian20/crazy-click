<?php
if ( ! defined('ROOT_DIR')) exit('Access denied');
class ShopApi extends Kernel{
    
    /**
     * 构造函数
     * 
     * @param $system
     */
    
    public function ShopApi(){
        parent::__construct();
        global $errorCode,$componentKeys;
        $this->componentKeys = $componentKeys;

        $this->system = &$GLOBALS['system'];
        $act = $_POST['act'];
        if( !$_POST['key']){
            $info['status'] = 'fail';
            $info['msg'] = 'Lack of parameter';
            $this->return_data($info);
        }
        
        $now_time = time();            
        if( 10 < $now_time - $_POST['time'] ){
            $info['status'] = 'fail';
            $info['msg'] = 'time error';
            $this->return_data($info);
        }

        if(!$this->verify($_POST)){
            $info['status'] = 'fail';
            $info['msg'] = 'verify fail';
            $this->return_data($info);            
        }
       
        if(!$data = $this->$act($_POST)){
            $info['status'] = 'fail';
            $info['msg'] = 'Get information fail';
            $this->return_data($info);
        }

        $info['status'] = 'true';
        $info['msg'] = 'OK';
        $info['data'] = $data;
        $this->return_data($info);    
       
    }
    
    public function return_data($data){
         $resposilbe = array(
            'true'=>'true',
            'fail'=>'false'
        );

        $result['status'] = $resposilbe[$data['status']];
        $result['msg'] = isset($data['msg']) ? $data['msg'] : '';
        $result['data'] = $data['data'] ?  $data['data'] : '';
        echo json_encode($result);exit;
    }

    //验签
    public function verify($params){
        $token = date('Ymd',time());
        if($params['token']) unset($params['token']);
        $verfy = strtolower(trim($params['ac']));
        unset($params['ac']);
        ksort($params);
        $tmp_verfy = '';
        foreach ($params as $k => $v) {
            $tmp_verfy.= stripcslashes($params[$k]);
        }
        //echo "<pre>";var_dump($verfy,strtolower(md5(trim($tmp_verfy.$token))),$tmp_verfy.$token);exit;
        if($verfy && $verfy == strtolower(md5(trim($tmp_verfy.$token)))){
            return true;
        }else{
            return false;
        }
    }

    //获取组件列表
    function getList($data){
        $config = $this->componentKeys[$data['key']];
        if(!$config || !$data['api_type'] || !$data['eid']) return false;

        $api_type = $data['api_type'];

        if( !empty($_POST['case_id']) ){
            $case['case_id'] = $_POST['case_id'];
        }
        $case['page_offset'] = isset($data['page_offset'])?$data['page_offset']:0;
        $case['page_limit'] = isset($data['page_limit'])?$data['page_limit']:500;
        $case['disabled'] = 0;
        $case['creator'] = $data['eid'];
        $case['sort'] = '-create_time';
        $result = $this->system->markets_openapi($config,$case,$api_type);
        if($result['res'] = 'succ' && $result['data']['records']){
            $count = $result['data']['count'];
            $count_page = ceil($count / $case['page_limit']);
            $curr_page = ($case['page_offset'] / $case['page_limit']) + 1;
            if( 'case' == $api_type || 'case_list' == $api_type ){
                //if($count) return $result['data']['count'];
                foreach ($result['data']['records'] as $key => $value) {
                    $list[$key]['id'] = $value['case_id'];
                    $list[$key]['active_name'] = $value['name'];
                    $list[$key]['active_stime'] = $value['start_time']?$value['start_time']:0;
                    $list[$key]['active_etime'] = $value['end_time']?$value['end_time']:0;
                    $list[$key]['active_state'] = $value['active_state'];
                    $list[$key]['active_state_name'] = $this->system->check_case_status($value['active_state'],$value['start_time'],$value['end_time'],'text');
                    $extend_info = json_decode($value['extend_info'],1);
                    $list[$key]['add_time'] = $extend_info['add_time']?$extend_info['add_time']:0;
                    $list[$key]['last_modified'] = $extend_info['last_modified']?$extend_info['last_modified']:0;
                }

            }else{
                $list =  $result['data']['records'];
            }
            if(isset($data['page_limit']) && isset($data['page_offset'])){
                $return = array('total_pages' => $count_page, 'curr_page' => $curr_page, 'data' => $list);
            }else{
                $return = $list;
            }

            return $return;
        }
        return null;     
    }

    //前台活动提交  报名
        function save_active($data){
        $result_data = array('status'=>'fail','msg'=>'');

        if( !$case_id = $data['case_id'] ) die('非法访问');
        $case_id = $data['case_id'];
        
        //获取实例 字段列表
        $case_['case_id'] = $case_id;
        $api_type_ = 'case_one';

        $config = $this->componentKeys[$data['key']];
        if(!$config){
            $result_data['msg'] = 'config错误';
            $this->return_data($result_data);
        }
        // $field_list = $this->system->markets_openapi($config,$case_,$api_type_);
        $tmp = $this->system->markets_openapi($config,$case_,$api_type_);
        if($tmp['res']=='succ'){
            $list = $tmp['data'];
            unset($tmp);
        }
        $list['extend_info'] = json_decode($list['extend_info'],1);
        $list['extend_info']['packageData'] = json_decode($list['extend_info']['packageData'],1);
        //判断是否必填 必填项为空时返回错误信息
        foreach($list['extend_info']['packageData'] as $pack_k=>$pack_v){
            $item_data  = $pack_v['data'];
            if(!empty($item_data['required']) && empty($data[$item_data['item_id']])){
                $result_data['msg'] = $item_data['title'].'必填';
                $this->return_data($result_data);
            } 
        }
        if($list['fields']){
            $field_list = $list['fields'];
        }
        $tmp_field_list = $record_data = array();

        foreach ($field_list as $value) {
            if($value['label']){
                $tmp_field_list[$value['label']]['field_id'] = $value['field_id'];
                $tmp_field_list[$value['label']]['option'] = $value['form']['option'];
                $tmp_field_list[$value['label']]['type'] = $value['form']['type'];
            }elseif ($value['name']=='openid') {
                $record_data['data'][$value['field_id']] = $data['openid']?$data['openid']:'';
            }elseif($value['name']=='create_time'){
                $record_data['data'][$value['field_id']] = time();
            }elseif($value['name']=='apply_status'){
                $record_data['data'][$value['field_id']] = '未审核';
            }elseif($value['name']=='source'){
                $record_data['data'][$value['field_id']] = $data['source']?$data['source']:'';
            }
        }

        
        foreach($data as $label=>$value){
            if($tmp_field_list[$label]){
                if($tmp_field_list[$label]['type']=='checkbox' or $tmp_field_list[$label]['type']=='radio'){
                    $option = array();
                    $option = json_decode($tmp_field_list[$label]['option']);
                    if(is_array($value)){
                        $tmp_value = array();
                        foreach($value as $k1=>$v1){
                            $tmp_value[$k1] = $option[$v1];
                        }
                        $record_data['data'][$tmp_field_list[$label]['field_id']] = implode('&nbsp;,&nbsp;', $tmp_value);
                    }else{
                        $record_data['data'][$tmp_field_list[$label]['field_id']] = $option[$value];
                    }

                }else{
                    $record_data['data'][$tmp_field_list[$label]['field_id']] = $value;
                }
            }
        }
        
        if($record_data['data']){
            $record_data['data'] = json_encode($record_data['data']); 
        }else{            
            $result_data['msg'] = '提交数据错误！';
            $this->return_data($result_data);
        }
        $record_data['case_id'] = $case_id;
        $api_type = 'record';
        $return = $this->system->markets_openapi($config,$record_data,$api_type);
      
        if($return['res'] == 'succ'){
            $result_data['status'] = 'true';
            $result_data['msg'] = '报名提交成功!';
            // $this->sendMsg($case_id,$mobile,$content);//发送短信
            $this->system->traffic_statistics($case_id);//流量统计
            //记录成交数 by yangyichao
            if($data['mark']){
                $obj_spread = $this->system->loadModel("Spread");
                $obj_spread->update_parent_transaction_count($case_id,$data['mark']);
            }
            $this->return_data($result_data);
        }

        $result_data['msg'] = '报名提交失败!';
        $this->return_data($result_data);
    }

}

?>
