<?php 

/**
 * 信息发送修改和添加控制器
 * 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年3月16日
 * @version 1
 */
 include("common.inc.php");
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php");
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");


 $user_obj = POCO::singleton('pai_user_class');
 $message_log_obj = POCO::singleton('pai_send_message_log_class');
 $code_log_obj = POCO::singleton('pai_send_code_log_class');

 $tpl = new SmartTemplate("send_message_edit_v2.tpl.htm");

 $act = trim($_INPUT['act']);

 //插入数据
 if($act == 'insert')
 {
 	$data_url         = trim($_INPUT['data_url']);
  	$is_multi         = intval($_INPUT['is_multi']);
  	$prov             = intval($_INPUT['province']);
 	$location_id      = intval($_INPUT['location_id']);
 	$role             = trim($_INPUT['role']);

    $type             = trim($_INPUT['type']);
 	$only_send_online = intval($_INPUT['only_send_online']);
 	$start_time       = trim($_INPUT['start_time']);
 	$end_time         = trim($_INPUT['end_time']);
 	$start_interval   = trim($_INPUT['start_interval']);
 	$end_interval     = trim($_INPUT['end_interval']);
 	$send_uid         = intval($_INPUT['send_uid']) ? intval($_INPUT['send_uid']) : 10002;
 	$user_str         = trim($_INPUT['user_str']);
    $user_str         = trim(str_replace('，', ',', $user_str),",");
    //echo $user_str;
 	$content          = trim($_INPUT['content']);
 	$content          = str_replace('<br rel=auto>', "\r\n", $content);

    $link_url         = trim($_INPUT['link_url']);
    $wifi_url         = trim($_INPUT['wifi_url']);
 	//echo $user_str;exit;
 	//$content          = poco_cutstr(strip_tags(str_replace(array("/r/n", "/r", "/n"), "", $content)), '200'); 
 	//验证码
 	$send_code       = trim($_INPUT['send_code']);
 	$market_code     = trim($_INPUT['market_code']);
 	$technical_code  = trim($_INPUT['technical_code']);

 	$ret = $code_log_obj->get_code_info($yue_login_id);

 	if (strlen($start_time) <1)
 	{
 		js_pop_msg("开始日期不能为空");
 		exit;
 	}
 	if (strlen($end_time) <1)
 	{
 		js_pop_msg("结束日期不能为空");
 		exit;
 	}
 	if (strlen($start_interval) <1)
 	{
 		js_pop_msg("开始时间不能为空");
 		exit;
 	}
 	if (strlen($end_interval) <1)
 	{
 		js_pop_msg("结束时间不能为空");
 		exit;
 	}
 	if (strlen($content)<1)
 	{
 		js_pop_msg("内容不能为空");
 		exit;
 	}
 	if ( empty($ret) || !is_array($ret)) 
 	 {
 	 	js_pop_msg("验证码有误");
 		exit;
 	 }
 	if ($send_code == '' || $send_code != $ret['send_code'] || $market_code == '' || $market_code !=$ret['market_code'] || $technical_code == '' || $technical_code != $ret['technical_code'] || $ret['expires_time'] < time()) 
 	{
 		js_pop_msg("验证码失效");
 		exit;
 	}
 	//初始化数组
 	$user_arr = array();
 	//获取连接数据
 	if($data_url)
 	{
 		$data_url_str  = uncompressed($data_url);
 		$data_url_str  = trim(str_replace('，', ',', $data_url_str),",");
 		$data_user_arr = explode(',', $data_url_str);
 		$user_arr = $data_user_arr != 0 && is_array($data_user_arr) ? array_merge($user_arr, array_unique($data_user_arr)) : $user_arr;
 		$user_arr = array_unique($user_arr);
 	}
 	//填写的user_id
 	if (!empty($user_str)) 
 	{
 		$user_id_arr = explode(',', $user_str);
 		$user_arr = $user_id_arr != 0 && is_array($user_id_arr) ? array_merge($user_arr, array_unique($user_id_arr)) : $user_arr;
 		$user_arr = array_unique($user_arr);
 	}
 	//群发开始
 	if ($is_multi == 1)
 	{
 		$where_str_tmp = "1 AND user_id >= 100000";
 		if ($location_id) 
 		{
 			$where_str_tmp .= " AND location_id = {$location_id}";
 		}
 		else
 		{
 			if($prov)
 			{
 				$where_str_tmp .= " AND left(location_id,6) = {$prov}";
 			}
 		}
 		if ($role) 
 		{
 			$where_str_tmp .= " AND role = '{$role}'";
 		}
 		//echo $where_str_tmp;exit; 
 		$total_count = $user_obj->get_user_list(true, $where_str_tmp);
 		$limit = "0,{$total_count}";
 		$multi_user_arr = $user_obj->get_user_list(false, $where_str_tmp,'user_id DESC', $limit, $fields = 'user_id');
 		$multi_user_arr = $multi_user_arr != 0 && is_array($multi_user_arr) ? array_change_by_val($multi_user_arr, 'user_id') : array();
 		$user_arr = $multi_user_arr != 0 && is_array($multi_user_arr) ? array_merge($user_arr, array_unique($multi_user_arr)) : $user_arr;
 		$user_arr = array_unique($user_arr);
 	}
 	if(count($user_arr) < 1)
 	{
 		js_pop_msg("得到用户的个数为零");
 		exit;
 	}
 	//iconv(in_charset, out_charset, str)
 	$valid_time = date('His', strtotime($start_interval)).'-'.date('His', strtotime($end_interval));
 	$duration   = date('Ymd',strtotime($start_time)).'-'.date('Ymd',strtotime($end_time));

    $option  = array();
 	//echo $data;
 	//数据
 	$option['content']          = $content;
    $option['link_url']          = $link_url;
    $option['wifi_url']          = $wifi_url;
    $option['only_send_online'] = $only_send_online;
    $option['is_multi']         = $is_multi;
    $option['role']             = $role;
    $option['location_id']      = $location_id != 0 ? $location_id : $prov ;

	{
		$info = $message_log_obj->save_group_message($type,$valid_time,$duration,$user_arr,$send_uid,$option);
		if($info)
		{
			js_pop_msg("添加信息成功");
		    exit;
		}
		
	}
	js_pop_msg("添加信息失败");
 	exit;
 }

 //发送测试
 elseif ($act == 'send_test') 
 {
    // echo "osoosos";exit;
 	$data_url         = trim($_INPUT['data_url']);
 	$is_multi         = intval($_INPUT['is_multi']);
 	$prov             = intval($_INPUT['province']);
 	$location_id      = intval($_INPUT['location_id']);
 	$role             = trim($_INPUT['role']);
 	$only_send_online = intval($_INPUT['only_send_online']);
 	$start_time       = trim($_INPUT['start_time']);
 	$end_time         = trim($_INPUT['end_time']);
 	$start_interval   = trim($_INPUT['start_interval']);
 	$end_interval     = trim($_INPUT['end_interval']);
 	$send_uid         = intval($_INPUT['send_uid']) ? intval($_INPUT['send_uid']) : 10002;
 	$user_str         = trim($_INPUT['user_str']);
    //转化中文,为英文的，
 	$user_str         = trim(str_replace('，', ',', $user_str),",");
    $type             = trim($_INPUT['type']);
 	$content          = trim($_INPUT['content']) ? trim($_INPUT['content']) : '';
 	$content          = str_replace('<br rel=auto>', "\r\n", $content);
    $link_url         = trim($_INPUT['link_url']);
    $wifi_url         = trim($_INPUT['wifi_url']);
 	//$cookie_name      = trim($_INPUT['cookie_name']) ? trim($_INPUT['cookie_name']) : 'send_code';
 	//$user_arr     = array('100293');
    $user_arr     = array('100041','100382','100002');
 	//$user_arr     = array('100293','100293','100027');

 	if (strlen($start_time) <1 || strlen($end_time)<1 || strlen($start_interval) <1
        || strlen($end_interval) <1 || strlen($content) <1)
 	{
        js_pop_msg('请把参数填写完整再提交');
 		exit;
 	}
 	$content_v2 = "{$content}";
 /*	if (strlen($data_url) >0)
 	{
 		$content_v2 .="发送链接:{$data_url}";
 	}
 	if ($is_multi >0)
 	{
 		if(!$location_id)
 		{
 			$location_id = $prov;
 		}
 		$city =  get_poco_location_name_by_location_id ($location_id);
 		$city = $city != '' ? $city : ' 全部';
 		$content_v2 .= ' 发送地区:'.$city;
 		$str_role = ' 全部';
 		if ($role == 'model') 
 		{
 			$str_role = " 模特";
 		}
 		elseif($role == 'cameraman')
 		{
 			$str_role = " 摄影师";
 		}
 		if ($only_send_online == 1) 
 		{
 			$str_role .= " 在线";
 		}
 		else
 		{
 			$str_role .= " 全部";
 		}
 		$content_v2 .= " 发送人群:{$str_role}";
 	}
 	$send_name = $send_uid == 10002 ? ' 约约小助手' : ' 约约系统助手';
 	//发送时间
 	$content_v2 .= " 发送日期段:{$start_time}至{$end_time} 发送时间段:{$start_interval}至{$end_interval} 推送消息账号:{$send_name}";
 	if ($user_str) 
 	{
 		$content_v2 .= " UID:{$user_str}";
 	}
 	$content_v2 .=" 发送文案:{$content}";
    */
    $valid_time = date('His', strtotime($start_interval)).'-'.date('His', strtotime($end_interval));
    $duration   = date('Ymd',strtotime($start_time)).'-'.date('Ymd',strtotime($end_time));

    $auto_send                = 1;
    //md5
    $option['auto_send']        = $auto_send;
    $option['link_url']         = $link_url;
    $option['wifi_url']         = $wifi_url;
 	$insert_data = array();
    $i = 0;

    foreach ($user_arr as $user_id)
 	{
        $user_id            = intval($user_id);
        $user_id            = array("{$user_id}");
        //echo $user_id;
        $code               = getStr(4);
        $tmp_content        = $content_v2." 验证码:{$code}";
        $option['content']  = trim($tmp_content);
        //echo $user_id."<br/>";
        $arr = $message_log_obj->save_group_message($type,$valid_time,$duration,$user_id,$send_uid,$option);
        if($i == 0)
        {
            $insert_data['send_code'] = $code;
        }
        if($i == 1)
        {
            $insert_data['market_code'] = $code;
        }
        if($i == 2)
        {
            $insert_data['technical_code'] = $code;
        }
        $i++;
        unset($option['content']);
 	}
     print_r($insert_data);
 	//过期时间
 	$insert_data['expires_time'] = time()+60*60;
 	$info = $code_log_obj->add_info($insert_data);
    js_pop_msg('发送验证码成功!');
 	/*if ($info)
 	{
        js_pop_msg('发送验证码成功!');
 	}
 	else
 	{
        js_pop_msg('发送验证码失败!');
 	}*/
 	exit;
 }
 //校验验证码是否错误
 elseif ($act == 'checkcode') 
 {
 	 $send_code       = trim($_INPUT['send_code']);
 	 $market_code     = trim($_INPUT['market_code']);
 	 $technical_code  = trim($_INPUT['technical_code']);
 	 $ret = $code_log_obj->get_code_info($yue_login_id);
 	 if ( empty($ret) || !is_array($ret)) 
 	 {
 	 	echo  0;
 	 	exit;
 	 }
 	 if($send_code == '' || $send_code != $ret['send_code'] || $market_code == '' || $market_code != $ret['market_code'] || $technical_code == '' || $technical_code != $ret['technical_code'] || $ret['expires_time'] < time())
 	 {
 	 	echo 1;
 	 	exit;
 	 }
 	 else
 	 {
 	 	echo 2;
 	 	exit;
 	 }
 }

 $tpl->assign('act', 'insert' );
 $province_list = change_assoc_arr($arr_locate_a);
 $tpl->assign('province_list', $province_list);
 $tpl->assign('yue_login_id', $yue_login_id);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>