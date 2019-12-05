<?php 

/**
 * 摄影师入库php文件 版本2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月27日
 * @version 2
 */
	
	include_once 'common.inc.php';
 	$cameraman_add_v2_obj = POCO::singleton('pai_cameraman_add_v2_class');
 	
 	//摄影师打标签类
 	$cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
 	
 	
    $user_id = intval($_INPUT['user_id']);
    
    if($user_id <1)
    {
    	js_pop_msg('非法操作');
    	exit;
    }
    /* echo $_INPUT['join_time'];
    var_dump(strtotime($_INPUT['join_time']));
    echo "<br/>";
    echo date('Y-m-d',strtotime(trim($_INPUT['join_time'])));
    exit; */
    
    $data_info['name']           = trim($_INPUT['name']);
    $data_info['sex']            = trim($_INPUT['sex']);
    $data_info['p_status']       = trim($_INPUT['p_status']);
    $data_info['join_time']      = date('Y-m-d',strtotime(trim($_INPUT['join_time'])));
    $data_info['weixin_name']    = trim($_INPUT['weixin_name']);
    $data_info['is_fview']       = intval($_INPUT['is_fview']);
    $data_info['poco_id']        = intval($_INPUT['poco_id']);
    //车型
    $data_info['vehicle_type']   = trim($_INPUT['vehicle_type']);
    $data_info['homepage']       = trim($_INPUT['homepage']);
    $data_info['honor']          = trim($_INPUT['honor']);
    $data_info['remark']         = trim($_INPUT['remark']);
    $data_info['source']         = trim($_INPUT['source']);
    $data_info['og_activity']    = trim($_INPUT['og_activity']);

    $res = $cameraman_add_v2_obj->update_info($data_info,$user_id);
    
    //添加标签
    $label_id  = trim($_INPUT['label_id']);
    $label_arr = explode(',', $label_id);
    if(!is_array($label_arr)) $label_arr = array();
    //删除标签
    $cameraman_add_user_label->del_info($user_id);
    foreach ($label_arr as $key=>$val)
    {
    	$data['user_id']  = $user_id;
    	$data['label_id'] = $val;
    	$cameraman_add_user_label->add_info($data);
    	unset($data);
    }
    
    js_pop_msg('保存成功',true);
    exit;
   
 ?>