<?php 

  include_once './include/Classes/PHPExcel.php';
	include_once 'common.inc.php';
  //地区引用
  include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
  include_once 'include/common_function.php';
  //查看权限
  check_authority_by_list('exit_type',$authority_list,'model', 'is_select');
	  $page_obj = new show_page ();
	  $show_count = 20;
	  $model_add_obj  = POCO::singleton('pai_model_add_class');
    $tpl  = new SmartTemplate("model_term_search.tpl.htm");
    $act       = $_INPUT['act'] ? $_INPUT['act'] : 'search';
    $recycle   = 0;
    $name      = $_INPUT['name']  ? $_INPUT['name'] : '';
    $phone     = $_INPUT['phone'] ? intval($_INPUT['phone']) : '';
    $app_name  = $_INPUT['app_name'] ? urldecode($_INPUT['app_name']) : '';
    $uid       = $_INPUT['uid'] ? intval($_INPUT['uid']) : '';
    $nick_name = $_INPUT['nick_name'] ? urldecode($_INPUT['nick_name']) : '';
    //echo $nick_name;
    $sort      = $_INPUT['sort'] ? $_INPUT['sort'] : 'ptime_desc';
    //判断
    $where_str = "1 AND recycle = {$recycle}";
    switch ($act) {
    	 case 'search':
    	 	   if (!empty($name)) 
    	 	   {
    	 	   	 $where_str .= " AND name like '%{$name}%'";
    	 	   }
    	 	   if (!empty($phone)) 
    	 	   {
    	 	   	  $where_str .= " AND phone = {$phone} ";
    	 	   }
    	 	   if (!empty($app_name)) 
    	 	   {
    	 	   	 $where_str .= " AND app_name like '%{$app_name}%' ";
    	 	   }
    	 	   if (!empty($uid)) 
    	 	   {
    	 	   	   $uid = (int)$uid;
    	 	   	   $where_str .= " AND uid = {$uid} ";
    	 	   }
           //昵称
           if (!empty($nick_name)) 
           {
             $where_str .= " AND nick_name like '%{$nick_name}%' ";
           }
    	 	   $total_count = $model_add_obj->get_model_list(true, $where_str);
           $page_obj->setvar (
            array(
              'name'      => $name, 
              'phone'     => $phone, 
              'uid'       => $uid,
              'nick_name' => $nick_name, 
              'act'       => $act, 
              'sort'      => $sort 
              )
            );
    	 	    $page_obj->set ( $show_count, $total_count );
            $list = $model_add_obj->get_model_list(false, $where_str, select_sort($sort), $page_obj->limit(), $fields = '*');
    	       break;
       //导出数据
       case 'export':
          if (!empty($name)) 
           {
             $where_str .= " AND name like '%{$name}%'";
           }
           if (!empty($phone)) 
           {
              $where_str .= " AND phone = {$phone} ";
           }
           if (!empty($app_name)) 
           {
             $where_str .= " AND app_name like '%{$app_name}%' ";
           }
           if (!empty($uid)) 
           {
               $uid = (int)$uid;
               $where_str .= " AND uid = {$uid} ";
           }
           //昵称
           if (!empty($nick_name)) 
           {
             $where_str .= " AND nick_name like '%{$nick_name}%' ";
           }
           $list = $model_add_obj->get_model_list(false, $where_str, select_sort($sort), '0,1000', $fields = '*');
           $data = array();
           foreach ($list as $key => $vo) 
           {
             $prof_data  = $model_add_obj->get_model_profession($vo['uid']);
             //风格及风格价格
             $style_data = $model_add_obj->list_style($vo['uid']);
             //其他信息
             $other_data = $model_add_obj->get_model_other($vo['uid']);
             //身材信息
             $stature_data       = $model_add_obj->get_model_stature($vo['uid']);
             $data[$key]['id']          = $key+1;
             $data[$key]['name']        = $vo['name'];
             $data[$key]['nickname']    = $vo['nick_name'];
             $data[$key]['weixin_name'] = $vo['weixin_name'];
             $data[$key]['discuz_name'] = $vo['discuz_name'];
             $data[$key]['poco_name']   = $vo['poco_name'];
             $data[$key]['app_name']    = $vo['app_name'];
             $data[$key]['phone']       = $vo['phone'];
             $data[$key]['weixin_id']   = $vo['weixin_id'];
             $data[$key]['qq']          = $vo['qq'];
             $data[$key]['email']       = $vo['email'];
             $data[$key]['poco_id']     = $vo['poco_id'];
             $data[$key]['state']       = change_text_by_state($prof_data['p_state']);
             $data[$key]['p_school']    = $prof_data['p_school'];
             $data[$key]['information_sources'] = $other_data['information_sources'];
             //$data[$key]['activity'] = $other_data['activity'];
             $data[$key]['city']         = get_poco_location_name_by_location_id ($vo['location_id']);
             $data[$key]['inputer_name'] = $vo['inputer_name'];
             $data[$key]['inputer_time'] = $vo['inputer_time'];
             $data[$key]['alipay_info']  = $other_data['alipay_info'];
             $data[$key]['sex']          = $stature_data['sex'] == 0 ? '女' : '男';
             $data[$key]['age']    =$stature_data['age'] ? date('Y', time()) - substr($stature_data['age'], 0, 4) : '';
             $data[$key]['height'] = $stature_data['height'];
             $data[$key]['weight'] = $stature_data['weight'];
             $data[$key]['cup']    = change_into_text_by_cup_id($stature_data['cup_id']).get_cup_text_by_cup_a_id($stature_data['cup_a']);
             $data[$key]['chest']  = $stature_data['chest'].'/'.$stature_data['waist'].'/'.$stature_data['chest_inch'];
           }
           $fileName = "yueyue_excel";
           $headArr = array("序号","姓名","昵称","微信名称","论坛名称","POCO用户名","APP昵称","手机号码","微信","QQ","邮箱","POCOID","职业状态","学校名称","来源", "常驻城市", "录入者", "录入时间", "支付宝账号", "性别", "年龄","身高", "体重", "罩杯", "三围" );
           $title = "模特库数据";
           getExcel($fileName,$title,$headArr,$data);
           exit;
         break;
    	 default:
             $total_count = $model_add_obj->get_model_list(true, $where_str);
              //print_r($total_count);exit;
            $page_obj->setvar ();
            $page_obj->set ( $show_count, $total_count );
            $list = $model_add_obj->get_model_list(false, $where_str, select_sort($sort), $page_obj->limit(), $fields = '*');
            break;
    }

    //排序
    switch ($sort) {
        case 'uid_desc':
            $sort_1 = "selected='true'";
            $tpl->assign('sort_1', $sort_1);
            break;
        case 'ptime_asc':
            $sort_2 = "selected='true'";
            $tpl->assign('sort_2', $sort_2);
            break;
        case 'ptime_desc':
            $sort_3 = "selected='true'";
            $tpl->assign('sort_3', $sort_3);
            break;
        default:
            # code...
            break;
    }
    //常驻城市名称获取
    if (!empty($list) && is_array($list)) 
    {
       foreach ($list as $key_city => $vo) 
       {
          $key_list = get_poco_location_name_by_location_id($vo['location_id']);
           //var_dump($key_list);
           $city_name = '';
           if (!empty($key_list)) 
           {
               $city_name = $key_list;
           }
           $list[$key_city]['city'] = $city_name;
       }
      
    }
    $tpl->assign('list', $list);
    $tpl->assign('act', $act);
    $tpl->assign('name', $name);
    $tpl->assign('phone', $phone);
    $tpl->assign('app_name', $app_name);
    $tpl->assign('uid', $uid);
    $tpl->assign('nick_name', $nick_name);
    $tpl->assign('total_count', $total_count);
    $tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>