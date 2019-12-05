<?php
define ( "G_YUEPAI_PROJECT", 1 );
//include_once ('poco_app_common.inc.php');

//引入活动类
//include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');

/**
 * 取列表
 *
 * @param string $where_str    查询条件
 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
 * @param string $limit        查询条数
 * @param string $order_by     排序条件
 * @return array|int
 */
function get_event_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'last_update_time DESC')
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$return_array = $event_details_obj->get_event_list ( $where_str, $b_select_count, $limit, $order_by );
	$return_array = format_event_item ( $return_array );
	return $return_array;
}

/**
 * 格式数据
 * @param array $row 
 * return array
 *
 */
function format_event_item($rows)
{
	
	if (! empty ( $rows ))
	{
		
		if (! is_array ( current ( $rows ) ))
		{
			$rows = array ($rows );
			$is_single = true;
		}
		foreach ( $rows as $k => $v )
		{
			$rows [$k] ['user_id'] = get_relate_yue_id ( $v ['user_id'] );
		}
	}
	
	if ($is_single)
		return $rows [0];
	else
		return $rows;

}

/**
 * 取列表
 *
 * @param string $where_str    查询条件
 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
 * @param string $limit        查询条数
 * @param string $order_by     排序条件
 * @return array|int
 */
function get_event_list_v2($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'last_update_time DESC')
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$tmp_array = $event_details_obj->get_event_list ( $where_str, $b_select_count, $limit, $order_by );
	$return_array [0] ['event_detail'] = $tmp_array [0];
	
	return $return_array;
}

/*
 * 获取我发布的活动
 * @param int $user_id
 * @param bool $b_select_count
 * @param string $limit
 */
function get_my_event_list($user_id, $b_select_count = false, $limit = '0,10')
{
    $user_id = ( int ) $user_id;
    $user_id = get_relate_poco_id ( $user_id );

    $activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );


    $tmp_array =curl_event_data('event_api_class','get_my_event_list',array($user_id, $b_select_count, $limit));

    foreach ( $tmp_array as $k => $val )
    {
        $new_array [$k]  = $val;
        $new_array [$k] ['event_detail'] ['nickname'] = get_user_nickname_by_user_id ( get_relate_yue_id($val ['event_detail']['user_id']) );
        $new_array [$k] ['event_detail'] ['is_comment'] = 1;


        if ($val['event_detail'] ['event_id'])
        {

            //活动是否有至少一个报名者扫码
            $check_scan = $activity_code_obj->check_event_code_scan ( $val ['event_detail']['event_id'] );
            if ($check_scan)
            {
                $new_array [$k] ['event_detail'] ['is_scan'] = 1;
            } else
            {
                $new_array [$k] ['event_detail'] ['is_scan'] = 0;
            }

        }

    }

    return $new_array;
}



/**
 * 活动全文高级搜索
 * 
 * @param string $time_querys  时间搜索条件 today ,weekend ,history
 * @param string $price_querys 价格搜索条件  budget_0-100, budget_100-200, budget_200-500 ,budget_500-1000
 * @param string $start_querys 发起人搜索条件 start_by_net_friends ,start_by_authority
 * @param bool $b_select_count TRUE:取记录总数 FALSE:取具体数据
 * @param string $limit 记录数
 * @return array
 */
function event_fulltext_search($time_querys = '', $price_querys = '', $start_querys = '', $b_select_count = false, $limit = '0,10',$location_id=0, $keyword='',$querys=array())
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );

	
	switch ($time_querys)
	{
		case "today" : //今天
			

			$today = strtotime ( date ( 'Y-m-d' ) );
			$tomorrow = $today + 86400;
			$querys ["start_time"] = $tomorrow;
			$querys ["end_time"] = $today;
			$querys ["event_status"] = "0";
			break;
		
		case "weekend" : //周末
			$arr = event_system_class::get_weekend_by_date ();
			$querys ["start_time"] = $arr ["Sun"];
			$querys ["end_time"] = $arr ["Sat"];
			$querys ["event_status"] = "0";
			break;
		
		case "history" : //活动回顾
			$nowtime = time ();
			$querys ["end_time"] = "< {$nowtime}";
			$querys["status_type"] = "1";
			$querys["event_status"] = "2";
			break;
		
		default :
			$querys ["event_status"] = "0,2";
			break;
	
	}
	
	switch ($price_querys)
	{
		case 'budget_0-100' :
			$querys ["budget"] = "0,100";
			break;
		
		case 'budget_101-200' :
			$querys ["budget"] = "101,200";
			break;
		
		case 'budget_201-500' :
			$querys ["budget"] = "201,500";
			break;
		
		case 'budget_501-1000' :
			$querys ["budget"] = "501,1000";
			break;
		
		default :
			
			break;
	}
	
	switch ($start_querys)
	{
		case 'start_by_net_friends' :
			$querys ["is_authority"] = "0,2";
			break;
		
		case 'start_by_authority' :
			$querys ["is_authority"] = 1;
			break;
		
		default :
			
			break;
	}
	
	$querys ["type_icon"] = "photo";
	
	$querys ["new_version"] = "2";
	
	if($keyword)
	{
		$querys ["titleusername"] = $keyword;
	}
	
	if($location_id)
	{
		$querys ["location_id"] = $location_id;
	}


    //约约专属排序
    $querys['FOR_YUEYUE_ORDER'] = 1;


    //$log_arr['querys'] = $querys;
    //pai_log_class::add_log($log_arr, 'event_log', 'event_log');
	
	$ret = $event_details_obj->event_fulltext_search ( $querys, $b_select_count, $limit, 'is_top DESC 4,event_status ,add_time DESC', false );
	
	foreach ( $ret as $k => $val )
	{
		
		$ret [$k] ['cover_image'] = yueyue_resize_act_img_url ( $val ['cover_image'], 145 );
		$ret [$k] ['nickname'] = get_user_nickname_by_user_id ( get_relate_yue_id($val ['user_id']) );
		
		$ret [$k] ['start_time'] = date ( "Y.m.d", $val ['start_time'] );
		if ($val ['is_authority'] == 1)
		{
			$ret [$k] ['is_authority'] = 1;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 0;
		} elseif ($val ['is_recommend'] == 1)
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 1;
			$ret [$k] ['is_free'] = 0;
		} elseif (( int ) $val ['budget'] == 0)
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 1;
		} else
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 0;
		}

	}
	
	
	return $ret;
}



/*
 * 非全文搜索活动列表
 */
function get_event_list_no_fulltext($time_querys = '', $price_querys = '', $start_querys = '', $b_select_count = false, $limit = '0,10', $location_id=0,$event_id_str="",$title_like='')
{
	$enroll_obj = POCO::singleton('event_enroll_class');
	
	$wherer_str = '1 ';
	
	switch ($time_querys)
	{
		case "today" : //今天
			

			$today = strtotime ( date ( 'Y-m-d' ) );
			$tomorrow = $today + 86400;
			$wherer_str .= " AND start_time >{$today} AND  end_time<{$tomorrow}";
			break;
		
		case "weekend" : //周末
			$arr = event_system_class::get_weekend_by_date ();
			$sun = $arr ["Sun"];
			$sat = $arr ["Sat"];
			
			$wherer_str .= " AND start_time >{$sat} AND  end_time<{$sun}";
			break;
		
		case "history" : //活动回顾
			$nowtime = time ();
			$wherer_str .= " AND (end_time<{$nowtime} or event_status='2')";
			break;
		
		default :
			
			break;
	
	}
	
	switch ($price_querys)
	{
		case 'budget_0-100' :
			$between_querys ["budget"] = " 0 and 100";
			break;
		
		case 'budget_101-200' :
			$between_querys ["budget"] = " 101 and 200";
			break;
		
		case 'budget_201-500' :
			$between_querys ["budget"] = " 201 and 500";
			break;
		
		case 'budget_501-1000' :
			$between_querys ["budget"] = " 501 and 1000";
			break;
		
		default :
			
			break;
	}
	
	switch ($start_querys)
	{
		case 'start_by_net_friends' :
			$querys ["is_authority"] = 0;
			break;
		
		case 'start_by_authority' :
			$querys ["is_authority"] = 1;
			break;
		
		default :
			
			break;
	}
	
	if ($time_querys == 'history')
	{
		$querys ["event_status"] = "'2'";
	} else
	{
		//$querys ["event_status"] = "'0'";
	}
	
	if($location_id)
	{
		$querys ["location_id"] = $location_id;
	}
	
	global $yue_login_id;
	
	
	
	if($event_id_str)
	{
		$wherer_str .= " AND event_id in ($event_id_str)";
		$wherer_str .= " AND type_icon='photo'";
	}
	else
	{ 
		$wherer_str .= " AND type_icon='yuepai'";
	}
	//$wherer_str .= " AND new_version=2";
	//$querys ["type_icon"] = "'photo'";
	
	if($title_like)
	{
		$wherer_str .= " AND title LIKE '%{$title_like}%'";
	}

	if ($querys)
	{
		foreach ( $querys as $key => $val )
		{
			$wherer_str .= ' AND ' . $key . '=' . $val;
		}
	}
	
	if ($between_querys)
	{
		foreach ( $between_querys as $key => $val )
		{
			$wherer_str .= ' AND ' . $key . ' BETWEEN ' . $val;
		}
	}
	
	$ret = get_event_list ( $wherer_str, $b_select_count, $limit, 'event_status ASC,add_time DESC' );
	
	foreach ( $ret as $k => $val )
	{
	
		$event_id = $val['event_id'];
		
		$ret [$k] ['cover_image'] = yueyue_resize_act_img_url ( $val ['cover_image'], 145 );
		$ret [$k] ['nickname'] = get_user_nickname_by_user_id ( $val ['user_id'] );
		
		$ret [$k] ['start_time'] = date ( "Y.m.d", $val ['start_time'] );
		if ($val ['is_authority'] == 1)
		{
			$ret [$k] ['is_authority'] = 1;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 0;
		} elseif ($val ['is_recommend'] == 1)
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 1;
			$ret [$k] ['is_free'] = 0;
		} elseif (( int ) $val ['budget'] == 0)
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 1;
		} else
		{
			$ret [$k] ['is_authority'] = 0;
			$ret [$k] ['is_recommend'] = 0;
			$ret [$k] ['is_free'] = 0;
		}
		
		$table_obj = POCO::singleton ( 'event_table_class' );
		
		$limit_num = $table_obj->sum_table_num($event_id);
		
		$join_num = $enroll_obj->sum_enroll_num($val ['event_id'],0,'0,1');
		
		$ret [$k] ['event_join'] = $join_num.'/'.$limit_num;
		
	}
	
	return $ret;
}

/**
 * 取活动详细信息
 * 
 * @param int $event_id 活动ID
 * 
 * */
function get_event_by_event_id($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$table_obj = POCO::singleton ( 'event_table_class' );
	$activity_code_obj = POCO::singleton ( 'pai_activity_code_class' );

	$user_obj = POCO::singleton ( 'pai_user_class' );

	global $yue_login_id;
	
	$ret = $event_details_obj->get_event_by_event_id ( $event_id );
	
	$ret = format_event_item( $ret );
	

	$ret ['event_time'] = date ( "m.d H:i", $ret ['start_time'] ) . " - " . date ( "m.d H:i", $ret ['end_time'] );
	
	$ret ['other_info_detail'] = unserialize ( $ret ['other_info'] );
	$ret ['leader_info_detail'] = unserialize ( $ret ['leader_info'] );

	
	$ret ['event_organizers'] =  $ret['user_id'];
	
	$ret ['nickname'] =  get_user_nickname_by_user_id($ret['user_id']);
	
	$ret ['user_icon'] =  get_user_icon($ret['user_id']);
	
	$ret ['event_title'] = $ret ['title'];
	
	$ret ['content'] = str_replace("\n","<br />",$ret ['content']);
	
	$ret ['remark'] = str_replace("\n","<br />",$ret ['remark']);
	

	
	//组合图片数据
	$limit_arr ['width'] = 750;
	
	foreach ( $ret ['other_info_detail'] as $ak => $aval )
	{
		foreach ( $aval ["img"] as $bk => $bval )
		{
			
			$real_arr = get_img_width_height ( $bval );
			$size_arr = poco_item_img_scaling_size ( $real_arr, $limit_arr );
			$new_arr [$bk] ["width"] = $size_arr ["width"];
			$new_arr [$bk] ["height"] = $size_arr ["height"];
			$new_arr [$bk] ["img_s"] = str_replace ( '_640', '_440', $bval );
			$new_arr [$bk] ["img_l"] = $bval;
		
		}
		$ret ['other_info_detail'] [$ak] ["img"] = $new_arr;
		unset($new_arr);
	}
	
	//活动是否有至少一个报名者扫码
	$check_scan = $activity_code_obj->check_event_code_scan ( $event_id );
	if ($check_scan)
	{
		$ret ['is_scan'] = 1;
	} else
	{
		$ret ['is_scan'] = 0;
	}
	
	//1.0.5版按钮控制
	if($ret['event_status']==='0')
	{
		if($check_scan)
		{
			$ret ['event_finish_button'] = 1;
			$ret ['event_cancel_button'] = 0;
		}
		else
		{
			$ret ['event_finish_button'] = 0;
			$ret ['event_cancel_button'] = 1;
		}
		$ret ['event_scan_button'] = 1;
	}
	else
	{
		$ret ['event_scan_button'] = 0;
		$ret ['event_finish_button'] = 0;
		$ret ['event_cancel_button'] = 0;
	}
	
	$limit_num = $table_obj->sum_table_num ( $event_id );
	$total_join = $activity_code_obj->get_code_list ( true, "event_id={$event_id}" );
	$total_mark = $activity_code_obj->get_code_list ( true, "event_id={$event_id} and is_checked=1" );
	
	$ret ['scan_detail'] = "一共{$limit_num}人，{$total_join}人报名，{$total_mark}人签到";
	
	$ret ['add_time'] = date ( "Y-m-d H:i:s", $ret ['add_time'] );
	
	if($ret ['complete_time'])
	{
		$ret ['complete_time'] = date ( "Y-m-d H:i:s", $ret ['complete_time'] );
	}
	else
	{
		$ret ['complete_time'] = '';
	}
	
	if($ret ['cancel_time'])
	{
		$ret ['cancel_time'] = date ( "Y-m-d H:i:s", $ret ['cancel_time'] );
	}
	else
	{
		$ret ['cancel_time'] = '';
	}
	
	$cellphone = $user_obj->get_phone_by_user_id($yue_login_id);
	$cellphone = base_convert($cellphone,10,8);

	$share_url = 'http://www.yueus.com/event/'.$ret['event_id']."?share_event_id={$event_id}&ph={$cellphone}";
	$share_url_v3 = 'http://www.yueus.com/event_v3/'.$ret['event_id']."?share_event_id={$event_id}&ph={$cellphone}";
	$share_img = $ret['cover_image'];
	
	$share_text['title'] = '【'.$ret['event_title'].'】 ︳约约';
	$share_text['content'] = '中国最In的达人活动，简直不能错过。';
	$share_text['sina_content'] = '中国最In的达人活动，简直不能错过。 '.$share_url;//.$share_url;
	$share_text['remark'] = '中国最In的达人活动，简直不能错过。 ';//.$share_url;
	$share_text['url'] = $share_url;
	$share_text['url_v3'] = $share_url_v3;
	$share_text['img'] = yueyue_resize_act_img_url($share_img,'145');
	$share_text['user_id'] = $yue_login_id;
	$share_text['qrcodeurl'] = $share_url;
	
	$ret['share_text'] = $share_text;
	
	return $ret;
}

/**
 * 添加活动
 * 
 * @param Array $data 活动数据
 * @param int   $audit 0=>需要审核 1=>审核通过
 * 
 * */
function add_event($data, $audit = 1)
{
	$event_check_obj = POCO::singleton ( 'event_check_class' );
	$data ['user_id'] = ( int ) get_relate_poco_id ( $data ['user_id'] );
	$data ['start_time'] = ( int ) $data ['start_time'];
	$data ['end_time'] = ( int ) $data ['end_time'];
	$data ['new_version'] = 2;
	if (empty ( $data ['user_id'] ) || empty ( $data ['start_time'] ) || empty ( $data ['end_time'] ))
	{
		return false;
	
	} else
	{
		
		$system_obj = POCO::singleton ( 'event_system_class' );
		$data = $system_obj->merge_extra_input_data ( $data, 'photo' );
		unset ( $data ['club_id'] );
		$ret = $event_check_obj->add_event ( $data, $audit );
		return $ret;
	
	}

}

/**
 * 更新审核状态
 *
 * @param int $check_id
 * @param array $data		// $data=array(
 * 'audit_user_id'=>$yue_login_id,
 * 'audit_time'=>0,		
 * 'audit_status'=>0,		//0表示没审核，1表示审核通过，2表示审核不通过
 * 'audit_ip'=>0,	
 * )
 * 
 * @return bool
 */
function update_event_is_audit($check_id, $data)
{
	$event_check_obj = POCO::singleton ( 'event_check_class' );
	return $event_check_obj->update_event_is_audit ( $check_id, $data );
}

/*
 * 活动结束（绑定确定活动和活动结束）
 */
function set_event_end($event_id)
{
	$ret = set_event_status_by_confirm ( $event_id );
	
	if ($ret == - 1)
	{
		$ret = array ('status_code' => - 1, 'message' => '该活动还没有人签到，请稍后再试' );
		return $ret;
	} elseif ($ret == 0)
	{
		$ret = array ('status_code' => 0, 'message' => '活动结束失败' );
		return $ret;
	} elseif ($ret == 1)
	{
		//执行活动结束
		set_event_status_by_finish ( $event_id );
		$ret = array ('status_code' => 1, 'message' => '活动结束成功' );
		return $ret;
	} elseif ($ret == - 2)
	{
		$ret = array ('status_code' => - 2, 'message' => '活动状态异常' );
		return $ret;
	}
}

function set_event_end_v2_old($event_id)
{

	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );	
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$enroll_obj = POCO::singleton ( 'event_enroll_class' );
	$table_obj = POCO::singleton ( 'event_table_class' );
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');  
	
	$where_str = "event_id = $event_id and pay_status=1";
	$enroll_list = get_enroll_list ( $where_str,false, '0,10000' );
	$event_info = get_event_by_event_id ( $event_id );
	$sum_cost = 0;
	$join_num = 0;
	$budget = $event_info ['budget'];
	
	$event_status = $event_info ['event_status'];
	
	$event_title = $event_info ['title'];
	
	if($event_info ['type_icon']=='yuepai_app')
	{
		$channel_module = "yuepai";
	}
	else
	{
		$channel_module = "waipai";
	}
	
	//判断状态是否为进行中
	if ($event_status != 0)
	{
		return - 2;
	}
	
	//判断活动是否有被扫过码
	$check_event_code_scan = $code_obj->check_event_code_scan ( $event_id );
	
	if (! $check_event_code_scan)
	{
		return - 1;
	}
	//var_dump($enroll_list);
	foreach ( $enroll_list as $key => $val )
	{
		$refund_total_amount = 0;
		
		$code_info = $code_obj->get_code_list ( false, "enroll_id=" . $val ['enroll_id'] );
		//print_r($code_info);
		foreach ( $code_info as $code_val )
		{
			if ($code_val ['is_checked'] == 0)
			{
				$refund_total_amount += $budget;
				$refund_code_arr [] = $code_val ['code'];
			} else
			{
				$sum += $budget;
				$join_num ++;
			}
		}
		//收集没被扫的活动券给啊裕
		$refund_code_str = implode ( ",", $refund_code_arr );
		
		$remark = "没扫活动券" . $refund_code_str;
		
		//退款
		if ($refund_total_amount != 0)
		{
			$refund_arr['channel_module'] = $channel_module;
			$refund_arr['user_id'] = $val ['user_id'];
			$refund_arr['apply_id'] = $val ['enroll_id'];
			$refund_arr['amount'] = $refund_total_amount;
			$refund_arr['remark'] = $remark;
			$refund_arr['subject'] = $event_title;
			$refund_arr['org_user_id'] = $event_info['org_user_id'];
			
			$refund_list[] = $refund_arr;
		}
		unset ( $refund_code_arr, $refund_code_str,$refund_arr );
	}
	//echo $join_num;
	

	if ($join_num != 0)
	{
		//付款
		$in_list[0]['channel_module'] = $channel_module;
		$in_list[0]['amount'] = $sum;
		$in_list[0]['user_id'] = $event_info['user_id'];
		$in_list[0]['subject'] = $event_title;
		$in_list[0]['org_user_id'] = $event_info['org_user_id'];
		
		if($event_info['org_user_id'])
		{
			$in_list[0]['org_amount'] = $sum;
		}
		
		$pay_info = $payment_obj->end_event($event_id, $refund_list, $in_list);
		
		//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
		$log_arr['refund_list'] = $refund_list;
		$log_arr['in_list'] = $in_list;
		$log_arr['result'] = $pay_info;
		pai_log_class::add_log($log_arr, 'end_event', 'info');
		
		if ($pay_info ['error'] === 0)
		{
		    
			//获取至少签到过一次的报名ID
			$where_str = "event_id = $event_id and is_checked=1";
			$enroll_code_list = $code_obj->get_code_list ( false, $where_str, 'id DESC', '', 'distinct(enroll_id)' );
			
			$table_arr = $table_obj->get_event_table_num_array($event_id);
			
			foreach ( $enroll_code_list as $key => $val )
			{
				
				//检查是否是约拍
				$check_is_date = get_event_date ( 'event_id', $event_id );
				
				if ($check_is_date)
				{
					$cameraman_user_id = $check_is_date [0] ['from_date_id'];
					$model_user_id = $check_is_date [0] ['to_date_id'];
					$date_id = $check_is_date [0] ['date_id'];
					$date_time = $check_is_date [0] ['date_time'];
					$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
					$cameraman_nickname = get_user_nickname_by_user_id ( $cameraman_user_id );
							
					$msg_obj = POCO::singleton ( 'pai_information_push' );
					
					$send_data['media_type'] = 'card';
					$send_data['card_style'] = 1;
					$send_data['card_text1'] = '已完成了和'.$cameraman_nickname.'的本次约拍,钱包将收到:';
					$send_data['card_text2'] = '￥'.sprintf ( "%.2f", $sum );
					$send_data['card_title'] = '评价摄影师';
							
					$to_send_data['media_type'] = 'card';
					$to_send_data['card_style'] = 2;
					$to_send_data['card_text1'] = '已完成了和你的本次约拍';
					$to_send_data['card_title'] = '评价模特';
										
					
					//微信信息                    	
	            	$template_code = 'G_PAI_WEIXIN_DATE_FINISHED';
	            	$data = array('datetime'=>date("Y年n月j日 H:i",$date_time),'nickname'=>$model_nickname);
	            	
	            	$version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
	            	$cache_ver = trim($version_control['wx']['cache_ver']);
	            	$to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#mine/consider_details_camera/{$date_id}";
	            	
	            
					if (! defined ( 'YUE_OA_IMPORT_ORDER' ))
					{
						$msg_obj->send_msg_data($model_user_id, $cameraman_user_id, $send_data, $to_send_data, $date_id);
						$weixin_pub_obj->message_template_send_by_user_id($cameraman_user_id, $template_code, $data, $to_url);
					}
				
				} else
				{
					$enroll_info = get_enroll_by_enroll_id ( $val ['enroll_id'] );
					$to_user_id = $enroll_info ['user_id'];
					$table_id = $enroll_info ['table_id'];
					$num = $table_arr[$table_id];
					$table_url = '{"table_id":"'.$table_id.'"}';
					$table_url = urlencode($table_url);

					$to_url = "http://yp.yueus.com/mall/user/comment/?event_id={$event_id}&table_id={$table_id}&type=event";
					$content = '你参加“'.$event_info ['title'].'”活动第'.$num.'场已经结束，给活动评价吧！';
					send_message_for_10002 ( $to_user_id, $content, $to_url );
					
					$nickname = get_user_nickname_by_user_id($event_info['user_id']);
					$template_code = 'G_PAI_WEIXIN_WAIPAI_FINISHED';
				 	$data = array('event_title'=>$event_info ['title'], 'datetime'=>date("Y年m月d日 H:s"), 'nickname'=>$nickname);
				 	
				 	if($to_user_id==100028)
				 	{
				 		$url = '#comment/event/' . $event_id . '/' . $event_info['user_id'].'/'.$table_url;
				 		$to_url = $weixin_pub_obj->auth_get_authorize_url(array('mode' => 'beta','route' => $url), 'snsapi_base');
				 	}
				 	else
				 	{
				 		$to_url = 'http://yp.yueus.com/m/wx?'.$cache_ver.'#comment/event/' . $event_id . '/' . $event_info['user_id'].'/'.$table_url;
				 	}
				 	
				 	$weixin_pub_obj->message_template_send_by_user_id($to_user_id, $template_code, $data, $to_url);

				}
			
			}
			
			foreach($refund_list as $refund_val)
			{
				$enroll_info = get_enroll_by_enroll_id ( $refund_val ['apply_id'] );
				$user_id = $enroll_info ['user_id'];
				$table_id = $enroll_info ['table_id'];
				$num = $table_arr[$table_id];
				$content = '你参加“'.$event_info ['title'].'”活动第'.$num.'场你未进行签到，系统已取消交易，费用即将退还到你钱包，请留意查看！';
				send_message_for_10002 ( $refund_val ['user_id'], $content );
				
				
				$template_code = 'G_PAI_WEIXIN_WAIPAI_CODE_NO_CHECKED';
			 	$data = array('event_title'=>$event_info ['title'], 'order_no'=>$enroll_info ['enroll_id'], 'datetime'=>date("Y年m月d日 H:s"));
			 	$to_url = "http://app.yueus.com/";
			 	$weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data, $to_url);
			}
			
			
			//活动结束
			$event_details_obj->set_event_status_by_finish ( $event_id );
			
			return 1;
		}
	}
	
	return 0;
}


function set_event_end_v2($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );	
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$table_obj = POCO::singleton ( 'event_table_class' );
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');  
	$coupon_obj = POCO::singleton('pai_coupon_class');
	$pai_sms_obj = POCO::singleton('pai_sms_class');
	$pai_trigger_obj = POCO::singleton('pai_trigger_class');
	
	
	$where_str = "event_id = $event_id and pay_status=1";
	$enroll_list = get_enroll_list ( $where_str,false, '0,10000' );
	$event_info = get_event_by_event_id ( $event_id );
	$sum_cost = 0;
	$join_num = 0;
	$budget = $event_info ['budget'];
	
	$event_status = $event_info ['event_status'];
	
	$event_title = $event_info ['title'];

	$channel_module = "waipai";


    global $yue_login_id;

    if($yue_login_id!=$event_info['user_id'])
    {
        return false;
    }
	
	//判断状态是否为进行中
	if ($event_status != 0)
	{
		return - 2;
	}
	
	//判断活动是否有被扫过码
	$check_event_code_scan = $code_obj->check_event_code_scan ( $event_id );
	
	if (! $check_event_code_scan)
	{
		return - 1;
	}
	
	
	$sum = 0;
	$coupon_sum = 0;
	$sum_in_amount =0;
	$refund_list = array();
	$coupon_refund_list = array();
	$coupon_in_list = array();
	foreach ( $enroll_list as $key => $val )
	{
		$join_num = 0;
		$code_info = $code_obj->get_code_list ( false, "enroll_id=" . $val['enroll_id'] );
		foreach( $code_info as $code_val )
		{
			if ($code_val['is_checked'] == 0)
			{
				$refund_code_arr[] = $code_val['code'];
			}
			else
			{
				$join_num ++;
			}
		}
		//收集没被扫的活动券给啊裕
		$refund_code_str = implode( ",", $refund_code_arr );
		$remark = "没扫活动券" . $refund_code_str;
		
		//原金额，优惠金额
		if( $val['is_use_coupon'] )
		{
			$original_price = $val['original_price'];
			$discount_price = $val['discount_price'];
		}
		else
		{
			$original_price = $budget*$val['enroll_num'];
			$discount_price = 0;
		}
		$cash_amount = $original_price - $discount_price; //优惠后金额，余额和第三方支付的金额
		$total_in_amount = $budget*$join_num; //应该给模特或组织者多少钱
		
		$in_amount = 0;
		$refund_amount = 0;
		$coupon_in_amount = 0;
		$coupon_is_refund = false;
		
		//有未扫码的
		if( $cash_amount>=$total_in_amount )
		{
			//余额和第三方足够
			$in_amount = $total_in_amount;
			$refund_amount = $cash_amount - $total_in_amount;
			$coupon_in_amount = 0;
			$coupon_is_refund = true;	//若有优惠券，则退还
		}
		else
		{
			$in_amount = $cash_amount;
			$refund_amount = 0;
			$coupon_in_amount = $total_in_amount - $cash_amount;
			$coupon_is_refund = false;
		}
		
		$sum += ($in_amount+$coupon_in_amount);
		$sum_in_amount += $in_amount;
		$coupon_sum += $coupon_in_amount;
		
		if( $refund_amount>0 )
		{
			$refund_arr = array();
			$refund_arr['channel_module'] = $channel_module;
			$refund_arr['user_id'] = $val['user_id'];
			$refund_arr['apply_id'] = $val['enroll_id'];
			$refund_arr['amount'] = $refund_amount;
			$refund_arr['remark'] = $remark;
			$refund_arr['subject'] = $event_title;
			$refund_arr['org_user_id'] = $event_info['org_user_id'];
			$refund_list[] = $refund_arr;
		}
		
		//用到优惠券金额，目前限制了只能使用一张

		$channel_oid = $val['enroll_id'];

		$ref_order_list = $coupon_obj->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( !empty($ref_order_list) && !empty($ref_order_list[0]) )
		{
			$ref_order_info = $ref_order_list[0];
			if( $coupon_in_amount>0 )
			{
				$org_amount_tmp = 0;
				if( $event_info['org_user_id'] )
				{
					$org_amount_tmp = $coupon_in_amount;
				}
				$coupon_in_list[] = array(
					'id' => $ref_order_info['id'], 
					'user_id' => $event_info['user_id'],
					'org_user_id' => $event_info['org_user_id'], 
					'amount' => $coupon_in_amount,
					'org_amount' => $org_amount_tmp,
					'subject' => $event_title,
				);
			}
			else
			{
				$coupon_refund_list[] = array('id'=>$ref_order_info['id']);
			}
		}
		
		unset( $refund_code_arr, $refund_code_str );
	}
	

	if ($sum != 0)
	{
		//付款
		$in_list[0]['channel_module'] = $channel_module;
		$in_list[0]['amount'] = $sum;
		$in_list[0]['discount_amount'] = $coupon_sum;
		$in_list[0]['user_id'] = $event_info['user_id'];
		$in_list[0]['subject'] = $event_title;
		$in_list[0]['org_user_id'] = $event_info['org_user_id'];
	
		
		if($event_info['org_user_id'])
		{
			$in_list[0]['org_amount'] = $sum_in_amount;
		}
		
		$pay_info = $payment_obj->end_event($event_id, $refund_list, $in_list,$coupon_refund_list, $coupon_in_list);
		
		//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
		$log_arr['refund_list'] = $refund_list;
		$log_arr['in_list'] = $in_list;
		$log_arr['coupon_refund_list'] = $coupon_refund_list;
		$log_arr['coupon_in_list'] = $coupon_in_list;
		$log_arr['result'] = $pay_info;
		pai_log_class::add_log($log_arr, 'end_event', 'end_event');
		
		if ($pay_info ['error'] === 0)
		{
		    
			//获取至少签到过一次的报名ID
			$where_str = "event_id = $event_id and is_checked=1";
			$enroll_code_list = $code_obj->get_code_list ( false, $where_str, 'id DESC', '', 'distinct(enroll_id)' );
			
			$table_arr = $table_obj->get_event_table_num_array($event_id);
			
			foreach ( $enroll_code_list as $key => $val )
			{
                $enroll_info = get_enroll_by_enroll_id ( $val ['enroll_id'] );
                $to_user_id = $enroll_info ['user_id'];
                $table_id = $enroll_info ['table_id'];
                $num = $table_arr[$table_id];
                $table_url = '{"table_id":"'.$table_id.'"}';
                $table_url = urlencode($table_url);

                $to_url = "/mall/user/comment/?event_id={$event_id}&table_id={$table_id}&type=event";

                $content = '你参加“'.$event_info ['title'].'”活动第'.$num.'场已经结束，给活动评价吧！';
                send_message_for_10002 ( $to_user_id, $content, $to_url ,'yuebuyer');

                $content = "恭喜你成功完成约拍！希望您的作品被推荐？请到战略合作网站POCO.cn发作品，关键词加上“约约作品”，即可在【约约专区】展示";
                send_message_for_10002 ( $to_user_id, $content ,'' , 'yuebuyer');

                $nickname = get_user_nickname_by_user_id($event_info['user_id']);
                $template_code = 'G_PAI_WEIXIN_WAIPAI_FINISHED';
                $data = array('event_title'=>$event_info ['title'], 'datetime'=>date("Y年m月d日 H:s"), 'nickname'=>$nickname);


                $weixin_pub_obj->message_template_send_by_user_id($to_user_id, $template_code, $data, $to_url);


                //活动完成获得优惠券
                $trigger_params = array('enroll_id'=>$val ['enroll_id']);
                $pai_trigger_obj->waipai_enroll_end_after($trigger_params);

			
			}
			
			foreach($refund_list as $refund_val)
			{
				$enroll_info = get_enroll_by_enroll_id ( $refund_val ['apply_id'] );
				$user_id = $enroll_info ['user_id'];
				$table_id = $enroll_info ['table_id'];
				$num = $table_arr[$table_id];
				$content = '你参加“'.$event_info ['title'].'”活动第'.$num.'场你未进行签到，系统已取消交易，费用即将退还到你钱包，请留意查看！';
				send_message_for_10002 ( $refund_val ['user_id'], $content );
				
				
				$template_code = 'G_PAI_WEIXIN_WAIPAI_CODE_NO_CHECKED';
			 	$data = array('event_title'=>$event_info ['title'], 'order_no'=>$enroll_info ['enroll_id'], 'datetime'=>date("Y年m月d日 H:s"));
			 	$to_url = "http://app.yueus.com/";
			 	$weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data, $to_url);
			 	
				
				$pai_sms_obj->send_sms($enroll_info ['phone'], 'G_PAI_WAIPAI_CODE_NO_CHECKED', array("event_title"=>$event_info ['title'],"table_num"=>$num));
			}
			
			
			//活动结束
			$ret=$event_details_obj->set_event_status_by_finish ( $event_id );
			
			
			$log_arr2['status'] = $ret;
			pai_log_class::add_log($log_arr2, 'end_event_status', 'end_event');
			
			return 1;
		}
	}
	
	return 0;
}

/**
 * 确定活动
 * 
 * @param int $event_id
 * -2 活动不是正在进行中
 * -1 活动码没被扫过，不执行后面操作
 * 0 活动确定失败
 * 1 活动确定成功
 * 
 * */
function set_event_status_by_confirm($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	
	$where_str = "event_id = $event_id and pay_status=1";
	$enroll_list = get_enroll_list ( $where_str,false, '0,10000' );
	$event_info = get_event_by_event_id ( $event_id );
	$sum_cost = 0;
	$join_num = 0;
	$budget = $event_info ['budget'];
	
	$event_status = $event_info ['event_status'];
	
	if ($event_status != 0)
	{
		return - 2;
	}
	
	//检查活动是否有被扫过码
	$check_event_code_scan = $code_obj->check_event_code_scan ( $event_id );
	
	if (! $check_event_code_scan)
	{
		return - 1;
	}
	//var_dump($enroll_list);
	foreach ( $enroll_list as $key => $val )
	{
		$refund_total_amount = 0;
		
		$code_info = $code_obj->get_code_list ( false, "enroll_id=" . $val ['enroll_id'] );
		//print_r($code_info);
		foreach ( $code_info as $code_val )
		{
			if ($code_val ['is_checked'] == 0)
			{
				$refund_total_amount += $budget;
				$refund_code_arr [] = $code_val ['code'];
			} else
			{
				$sum += $budget;
				$join_num ++;
			}
		}
		//收集没被扫的活动券给啊裕
		$refund_code_str = implode ( ",", $refund_code_arr );
		
		$remark = "没扫活动券" . $refund_code_str;
		
		//退款
		if ($refund_total_amount != 0)
		{
			$payment_obj->submit_trade_refund ( $event_id, $val ['enroll_id'], $val ['user_id'], $refund_total_amount, array ("remark" => $remark ) );
		}
		unset ( $refund_code_arr, $refund_code_str );
	}
	//echo $join_num;
	

	if ($join_num != 0)
	{
		//计算完总费用插入一条组织者记录
		$off = $payment_obj->submit_trade_in ( $event_id, 0, $event_info ['user_id'], $sum );
		
		if ($off ['error'] === 0)
		{
			$pay_info = $payment_obj->confirm_event ( $event_id );
			//print_r ( $pay_info );
			if ($pay_info ['error'] === 0)
			{
				$event_details_obj->set_event_status_by_confirm ( $event_id );
				
				return 1;
			}
		}
	}
	
	return 0;
}

/**
 * 活动结束
 * 
 * @param int $event_id
 * 
 * */
function set_event_status_by_finish($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$enroll_obj = POCO::singleton ( 'event_enroll_class' );
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	
	global $yue_login_id;
	
	//获取至少签到过一次的报名ID
	$where_str = "event_id = $event_id and is_checked=1";
	$enroll_code_list = $code_obj->get_code_list ( false, $where_str, 'id DESC', '', 'distinct(enroll_id)' );
	
	foreach ( $enroll_code_list as $key => $val )
	{
		
		//检查是否是约拍
		$check_is_date = get_event_date ( 'enroll_id', $val ['enroll_id'] );
		
		if ($check_is_date)
		{
			$cameraman_user_id = $check_is_date [0] ['from_user_id'];
			$model_user_id = $check_is_date [0] ['to_user_id'];
			$date_id = $check_is_date [0] ['date_id'];
/**			
			//发给摄影师
			$to_url = '/mobile/app#comment/model/' . $date_id . '/' . $model_user_id;
			$content = '评价模特';
			send_message_for_10002 ( $cameraman_user_id, $content, $to_url );
			
			//发给模特
			$to_url = '/mobile/app#comment/cameraman/' . $date_id . '/' . $cameraman_user_id;
			$content = '约拍已完成,您可点击评价摄影师';
			send_message_for_10002 ( $model_user_id, $content, $to_url );
**/
		
		} else
		{
			$enroll_info = get_enroll_by_enroll_id ( $val ['enroll_id'] );
			$to_user_id = $enroll_info ['user_id'];
			$to_url = '/mobile/app?from_app=1#comment/event/' . $event_id.'/'.$yue_login_id;
			$content = '戳一下 造福人类吧';
			send_message_for_10002 ( $to_user_id, $content, $to_url );
		}
	
	}
	
	$pay_info = $payment_obj->finish_event ( $event_id );
	return $event_details_obj->set_event_status_by_finish ( $event_id );
}

/**
 * 活动取消
 * 
 * @param int $event_id
 * 返回状态 -1已扫码不能取消活动，  -2活动状态异常 ， 1活动取消成功
 * */
function set_event_status_by_cancel($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	$payment_obj = POCO::singleton ( 'pai_payment_class' );
	$enroll_obj = POCO::singleton ( 'event_enroll_class' );
	$code_obj = POCO::singleton ( 'pai_activity_code_class' );
	$pai_sms_obj = POCO::singleton('pai_sms_class');
	$table_obj = POCO::singleton ( 'event_table_class' );
	$coupon_obj = POCO::singleton('pai_coupon_class');
	
	//检查活动是否有被扫过码
	$check_event_code_scan = $code_obj->check_event_code_scan ( $event_id );
	
	if ($check_event_code_scan)
	{
		return - 1;
	}
	
	$event_info = $event_details_obj->get_event_by_event_id ( $event_id );
	$event_status = $event_info ['event_status'];
	
	if ($event_status != 0)
	{
		return - 2;
	}

    global $yue_login_id;

    $relate_poco_id = get_relate_poco_id($yue_login_id);

    if($relate_poco_id!=$event_info['user_id'])
    {
        return false;
    }
	
	
	$pay_info = $payment_obj->cancel_event ( $event_id );
	
	//加LOG  例子 http://yp.yueus.com/logs/201501/28_info.txt
	$log_arr['result'] = $pay_info;
	pai_log_class::add_log($log_arr, 'cancel_event', 'end_event');
	
	if ($pay_info['error']===0)
	{
		$table_arr = $table_obj->get_event_table_num_array($event_id);
		
		$where_str = "event_id = {$event_id}";
		$enroll_list = $enroll_obj->get_enroll_list ( $where_str,false, '0,10000' );
		foreach ( $enroll_list as $key => $val )
		{
			$data ['event_remark'] = '活动取消';
			$enroll_obj->update_enroll ( $data, $val ['enroll_id'] );
			
			$table_num = $table_arr[$val ['table_id']];

			if(!defined("DON_NOT_SEND_CANCEL_MESSAGE"))
            {
                $pai_sms_obj->send_org_cancel_refund_notice($val ['phone'], array("event_title"=>$event_info['title'],"table_num"=>$table_num), $val ['enroll_id']);
            }

			$coupon_obj->refund_coupon_by_oid("waipai", $val ['enroll_id']);
		
		}
	
		$event_details_obj->set_event_status_by_cancel ( $event_id );
		return 1;
	}
}

/**
 * 获取活动状态
 * 
 * @param int $event_id
 * 
 * */
function get_event_status($event_id)
{
	$event_details_obj = POCO::singleton ( 'event_details_class' );
	return $event_details_obj->get_event_status ( $event_id );
}

/**
 * 添加活动场次
 * 
 * @param int $event_id    活动ID
 * @param int $begin_time   场次开始时间
 * @param int $end_time     场次结束时间
 * @param int $num          场次可报名人数
 * */
function add_event_table($check_id, $event_id, $begin_time, $end_time, $num)
{
	$event_table_obj = POCO::singleton ( 'event_table_class' );
	return $event_table_obj->add_event_table ( $check_id, $event_id, $begin_time, $end_time, $num );
}

/**
 * 获取活动场次
 * 
 * @param int $event_id 活动ID
 * 
 * */
function get_event_table($event_id)
{
	$event_table_obj = POCO::singleton ( 'event_table_class' );
	return $event_table_obj->get_event_table ( $event_id );
}

function get_event_table_num($event_id)
{
	$event_table_obj = POCO::singleton ( 'event_table_class' );
	return $event_table_obj->get_event_table_num ( $event_id );
}

?>