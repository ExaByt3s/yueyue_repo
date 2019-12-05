<?php 

 /*
 * 机构列表
 *
 */
 include("common.inc.php");
 include("include/common_function.php");
 include_once 'include/locate_file.php';
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 $tpl = new SmartTemplate("org_list.tpl.htm");
 $page_obj          = new show_page ();
 $show_count        = 20;
 $organization_obj = POCO::singleton('pai_organization_class');
 $user_obj         = POCO::singleton('pai_user_class');
 $model_relate_obj = POCO::singleton('pai_model_relate_org_class');
 $payment_obj      = POCO::singleton('pai_payment_class');
 $order_obj        = POCO::singleton ( 'pai_order_org_class' );
 $user_id     = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : '';
 $nick_name   = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
 $link_man    = $_INPUT['link_man'] ? $_INPUT['link_man'] : '';
 $start_time  = $_INPUT['start_time'] ? $_INPUT['start_time'] : ''; 
 $end_time    = $_INPUT['end_time'] ? $_INPUT['end_time'] : ''; 
 $province    = $_INPUT['province'] ? intval($_INPUT['province']) : 0; 
 $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0; 
 $where_str  = '1 AND o.user_id = u.user_id';
 if ($user_id) 
 {
 	$user_id = (int)$user_id;
 	$where_str .= " AND o.user_id = {$user_id}";
 }
 //昵称
 //var_dump($nickname);
 if ($nick_name) 
 {
 	$where_str .= " AND o.nick_name = '{$nick_name}'";
 }
 if ($link_man) 
 {
 	$where_str .= " AND o.link_man = '{$link_man}'";
 }
 if ($start_time && $end_time) 
 {
 	$start_tmp_time = strtotime($start_time);
 	$end_tmp_time   = strtotime($end_time)+24*3600;
 	$where_str .= " AND o.add_time BETWEEN {$start_tmp_time} AND {$end_tmp_time}";
 }
 if ($province) 
 {
 	if ($location_id) 
 	{
 		$where_str .= " AND u.location_id = {$location_id}";
 	}
 	else
 	{
 		$where_str .= " AND left(u.location_id,6) = {$province}";
 	}
 }

 $page_arrvar = array
 (
 	'user_id'     => $user_id ,
 	'nick_name'   => $nick_name,
 	'link_man'    => $link_man,
 	'start_time'  => $start_time,
 	'end_time'    => $end_time,
 	'province'    => $province,
 	'location_id' => $location_id
 );
 //var_dump($where_str);
 $page_obj->setvar($page_arrvar);
 
 $total_count = $organization_obj->get_org_list_by_sql(true,$where_str);
 $page_obj->set ( $show_count, $total_count );
 $list = $organization_obj->get_org_list_by_sql(false, $where_str, 'id DESC', $page_obj->limit(),'*' );
 foreach ($list as $key => $vo) 
 {
 	//$list[$key]['cellphone']  = $user_obj->get_phone_by_user_id($vo['user_id'] );
 	$list[$key]['add_time']    = date('Y-m-d H:i:s', $vo['add_time']);
 	$list[$key]['city']        = get_poco_location_name_by_location_id($vo['location_id']);
 	$list[$key]['is_model']    = $model_relate_obj->get_model_total_count(true,$vo['user_id']);
 	$list[$key]['is_enter']    = $model_relate_obj->get_model_total_count(true,$vo['user_id'], 1);
 	$list[$key]['unsettle']    = sprintf('%.2f',$payment_obj->get_unsettle_org_amount($vo['user_id']));
  $list[$key]['order_count'] = $order_obj->get_order_count(30,$vo['user_id']);
  $list[$key]['pay_sum']     = $order_obj->get_user_count_budget_by_org_id(30,$vo['user_id']);
 }

  //省和接口
  $location_id_info = get_poco_location_name_by_location_id ($location_id, true, true);
  //var_dump($location_id_info);
  //省
  $province_list = change_assoc_arr($arr_locate_a);
  //省处理
  if ($province) {
       foreach ($province_list as $key => $vo) 
       {
        if ($vo['c_id'] == $province) 
        {
            $province_list[$key]['selected_prov'] = "selected='true'";
         }
       }
       $city_list = ${'arr_locate_b'.$province};
       if (!empty($city_list) && is_array($city_list) ) 
       {
         $city_list = change_assoc_arr($city_list);
         foreach ($city_list as $c_key => $vo) 
         {
          if ($location_id != 0 && $vo['c_id'] == $location_id) 
          {
              $city_list[$c_key]['selected_city'] = "selected='true'";
          }
        }
       }
  }
 /* if (isset($location_id_info['level_1']) && is_array($location_id_info['level_1'])) 
   {
       $prov_id = $province;
       $city_list = ${'arr_locate_b'.$prov_id};
       if (!empty($city_list) && is_array($city_list) ) 
       {
         $city_list = change_assoc_arr($city_list);
         foreach ($city_list as $c_key => $vo) 
         {
          if ($vo['c_id'] == $location_id_info['level_1']['id']) 
          {
              $city_list[$c_key]['selected_city'] = "selected='true'";
          }
        }
       }
   }*/
 $tpl->assign($page_arrvar);
 $tpl->assign('province_list', $province_list);
 $tpl->assign('list', $list);
 $tpl->assign('city_list', $city_list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>