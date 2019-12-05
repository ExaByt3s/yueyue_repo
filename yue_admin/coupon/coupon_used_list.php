<?php

/**
 *优惠券使用详情
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-27 18:02:08
 * @version 1
 */
 include_once ('common.inc.php');
 //后台常用函数
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 //优惠券类
 $coupon_obj = POCO::singleton('pai_coupon_class');
  //订单类
 $order_obj = POCO::singleton('pai_order_org_class');
 //用户类
 $user_obj  = POCO::singleton('pai_user_class');

$mall_order_obj = POCO::singleton( 'pai_mall_order_class' );//商城订单类
 
 //分页类
 $page_obj   = new show_page();
 $show_total = 30;
 $act        = trim($_INPUT['act']) ? trim($_INPUT['act']) : 'mall_order';
 $user_id    = intval($_INPUT['user_id']);
 $cash_time  = trim($_INPUT['cash_time']);

 //条件
 $where_str = "channel_module = '".mysql_escape_string($act)."' AND  is_cash=1 ";
 $setParam['act'] = $act;
 
 //摄影师ID
 if($user_id)
 {
 	$where_str .= " AND user_id = {$user_id}";
 	$setParam['user_id'] = $user_id;
 }

 if($cash_time)
 {
 	$where_str .= " AND FROM_UNIXTIME(cash_time,'%Y-%m-%d') = '{$cash_time}'";
 	$setParam['cash_time'] = $cash_time;
 }


 $total_count  = $coupon_obj->get_ref_order_list(true, $where_str);
 $page_obj->setvar($setParam);
 $page_obj->set($show_total, $total_count);

 $list = $coupon_obj->get_ref_order_list(false, $where_str, 'cash_time DESC,id DESC', $page_obj->limit());
 if(!is_array($list)) $list = array();

 //查询共用的数据
 $where_in_str = '';
 foreach ($list as $key => $vo) 
 {
 	if($key != 0)
 	{
 		$where_in_str .= ',';
 	}
 	$where_in_str .= "{$vo['channel_oid']}";
 }
 //约拍
 if($act == 'yuepai')
 {
 	//约拍模板
 	$tpl = new SmartTemplate('coupon_used_yuepai_list.tpl.htm');
 	if(strlen($where_in_str) > 0)
 	{
 		$where_tmp_str = "date_id IN ({$where_in_str})";
 		$yuepai_list    = $order_obj->get_user_id_by_where_str($where_tmp_str,"0,{$show_total}", 'date_id DESC','date_id as channel_oid,from_date_id,to_date_id,date_status,date_style,date_price');
 		if(is_array($yuepai_list))
 		{
 			$where_from_id_str = '';
 			$where_to_id_str   = '';
 		   //$list = combine_arr($list, $yuepai_list, 'event_id');
 		   foreach ($yuepai_list as $yuepai_key => $yuepai_val) 
 		   {
 		   	  if($yuepai_key != 0)
 		   	  {
 		   	  	$where_from_id_str .= ",";
 		   	  	$where_to_id_str   .= ",";
 		   	  }
 		   	  $where_from_id_str .= "{$yuepai_val['from_date_id']}";
 		   	  $where_to_id_str   .= "{$yuepai_val['to_date_id']}";
 		   }
 		   //摄影师
 		   if (strlen($where_from_id_str) > 0) 
 		   {
 		   	  $where_from_tmp_str = "user_id IN ({$where_from_id_str})";
 		   	  $from_list = $user_obj->get_user_list(false, $where_from_tmp_str, 'user_id DESC','0,{$show_total}','user_id as from_date_id, nickname as cameraman_name');
 		   	  if(is_array($from_list)) $yuepai_list = combine_arr2($yuepai_list, $from_list, 'from_date_id');
 		   }
 		   //模特
 		   if (strlen($where_to_id_str) > 0) 
 		   {
 		   	  $where_to_tmp_str = "user_id IN ({$where_to_id_str})";
 		   	  $to_list = $user_obj->get_user_list(false, $where_to_tmp_str, 'user_id DESC','0,{$show_total}','user_id as to_date_id, nickname as model_name');
 		   	  if(is_array($to_list)) $yuepai_list = combine_arr2($yuepai_list, $to_list, 'to_date_id');
 		   }
 		   //合并
 		   $list = combine_arr($list, $yuepai_list, 'channel_oid');
 		}
 	}
 }
 elseif($act == 'mall_order')//商城
 {
     $tpl = new SmartTemplate('coupon_used_mall_list.tpl.htm'); //商城模板
     if(strlen($where_in_str) > 0)
     {
         $where_tmp_str = "order_id IN ({$where_in_str})";
         $mall_list = $mall_order_obj->get_order_list(0,-1,false, $where_tmp_str,'sign_time DESC,close_time DESC,order_id DESC',"0,{$show_total}","order_id AS channel_oid,type_name,total_amount,discount_amount,buyer_user_id,seller_user_id");

         if(!is_array($mall_list)) $mall_list = array();
         $where_seller_user_id_str = '';
         $where_buyer_user_id_str   = '';
         //$list = combine_arr($list, $yuepai_list, 'event_id');
         foreach ($mall_list as $mall_key => $mall_val)
         {
             if($mall_key != 0)
             {
                 $where_seller_user_id_str .= ",";
                 $where_buyer_user_id_str   .= ",";
             }
             $where_seller_user_id_str .= "{$mall_val['seller_user_id']}";
             $where_buyer_user_id_str   .= "{$mall_val['buyer_user_id']}";
         }
         if (strlen($where_seller_user_id_str) > 0)//商家
         {
             $where_seller_user_id_str = "user_id IN ({$where_seller_user_id_str})";
             $seller_user_list = $user_obj->get_user_list(false, $where_seller_user_id_str, 'user_id DESC','0,{$show_total}','user_id AS seller_user_id, nickname as seller_name');
             if(is_array($seller_user_list)) $mall_list = combine_arr2($mall_list, $seller_user_list, 'seller_user_id');
         }
         if (strlen($where_buyer_user_id_str) > 0)//消费者
         {
             $where_buyer_user_id_str = "user_id IN ({$where_buyer_user_id_str})";
             $buyer_user_list = $user_obj->get_user_list(false, $where_buyer_user_id_str, 'user_id DESC','0,{$show_total}','user_id as buyer_user_id, nickname as buyer_name');
             if(is_array($buyer_user_list)) $mall_list = combine_arr2($mall_list, $buyer_user_list, 'buyer_user_id');
         }
         $list = combine_arr($list, $mall_list, 'channel_oid');
         //print_r($list);
     }
 }
 else  //外拍
 {
 	$tpl = new SmartTemplate('coupon_used_waipai_list.tpl.htm');
 	if(strlen($where_in_str) >0 )
 	{
 		$where_detail_str = "enroll_id IN ({$where_in_str})";
 		$waipai_list = $order_obj->get_waipai_enroll_list(false, $where_detail_str,'event_id DESC', '0,{$show_total}', 'enroll_id AS channel_oid,original_price,event_id' );
 		if(!is_array($waipai_list)) $waipai_list = array();
 		$where_event_in_str = '';
 		foreach ($waipai_list as $waipai_key => $val) 
 		{
 			if($waipai_key != 0) $where_event_in_str .= ',';
 			$where_event_in_str .= "{$val['event_id']}";
 		}
 		if(strlen($where_event_in_str) >0 )
 		{
 			$where_event_tmp_str = "event_id IN ({$where_event_in_str})";
 			$event_list = $order_obj->get_waipai_envent_list(false, $where_event_tmp_str,'event_id DESC', '0,{$show_total}', 'event_id,user_id as constitutor_id,title,start_time' );
 			if(is_array($event_list)) $waipai_list = combine_arr2($waipai_list, $event_list, 'event_id');
 		}
 		$list = combine_arr2($list, $waipai_list, 'channel_oid');
 	}
 }
 if(!is_array($list)) $list = array();
 foreach ($list as $k => $val) 
 {
 	$list[$k]['cash_time'] = date('Y-m-d', $val['cash_time']);
 	//活动时间
 	if(isset($val['start_time']))
 	{
 		$list[$k]['start_time'] = date('Y年m月d日', $val['start_time']);
 	}
 	//组织者ID
 	if(isset($val['constitutor_id']))
 	{
 		$list[$k]['constitutor_id'] = get_relate_yue_id($val['constitutor_id']);
 	}
 }

 
 $tpl->assign($setParam);
 $tpl->assign('list', $list);
 //共用部分
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();