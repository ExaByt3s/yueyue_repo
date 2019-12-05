<?php

include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
include_once 'common.inc.php';
include_once 'config/authority_model_audit.php';
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once 'include/common_function.php';
include_once 'include/locate_file.php';
$tpl = new SmartTemplate("model_audit_list.tpl.htm");

$page_obj = new show_page ();
//机构模特表
$model_relate_org  = POCO::singleton('pai_model_relate_org_class');

$user_obj = POCO::singleton ( 'pai_user_class' );
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');
$model_card_obj = POCO::singleton('pai_model_card_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
//获取支付宝账号
$user_account_obj = POCO::singleton('pai_bind_account_class');
$is_approval = isset($_INPUT['is_approval']) ? (int)$_INPUT['is_approval'] : -1 ;
$phone = (int)$_INPUT['phone'] ? (int)$_INPUT['phone'] : '';
$user_id   = (int)$_INPUT['user_id'] ? (int)$_INPUT['user_id'] : '';
$start_add_time   = $_INPUT['start_add_time'] ? $_INPUT['start_add_time'] : '';
$end_add_time     = $_INPUT['end_add_time'] ? $_INPUT['end_add_time'] : '';
$start_audit_time = $_INPUT['start_audit_time'] ? $_INPUT['start_audit_time'] : '';
$end_audit_time   = $_INPUT['end_audit_time'] ? $_INPUT['end_audit_time'] : '';
$province         = $_INPUT['province'] ? (int)$_INPUT['province'] : 0;
$location_id      = $_INPUT['location_id'] ? (int)$_INPUT['location_id'] : 0;
$is_org           = $_INPUT['is_org'] ? (int)$_INPUT['is_org'] : 0;
/*if ($yue_login_id != 100293) 
{
  echo "程序正在开发中。。。";
  exit;
}*/
if($_INPUT['act'] == 'approval')
{
    $reason = $_INPUT['reason'] ?  $_INPUT['reason'] : '';
    $user_id = $_INPUT['user_id'] ? $_INPUT['user_id'] : 0;
    if (empty($user_id)) 
    {
        echo "<script>alert('非法操作');location.href='model_audit_list.php'</script>";
        exit;
    }
    $inputer_name = get_user_nickname_by_user_id($yue_login_id);
    $user_info   = $user_obj->get_user_info_by_user_id($user_id);
    //判断是否有权限
    $location_id_root = trim($authority_list[0]['location_id']);
    if(strlen($location_id_root) >0)
    {
      $location_id_root = explode(',', $location_id_root);
    	if(!in_array($user_info['location_id'],$location_id_root))
    	{
    		js_pop_msg('非法操作');
    		exit;
    	}
    
    }
    if ($_INPUT['is_approval'] == 1 && !$user_add_obj->get_is_set_by_user_id($user_id)) 
    {
        $user_alipay = $user_account_obj->get_alipay_account_by_user_id($user_id);
        //var_dump($user_alipay);exit;
        if (!empty($user_info) && is_array($user_info)) 
         {
        /*
        *数据处理
        */
          //基本数据
           $data_info['app_name']       = $user_info['nickname'];
           $data_info['phone']          = $user_info['cellphone'];
           $data_info['location_id']    = $user_info['location_id'];
           $data_info['img_url']        = $user_info['user_icon'];
           $data_info['inputer_time']   = date('Y-m-d H:i:s');
           $data_info['inputer_name']   = $inputer_name;
           $data_info['inputer_id']     = $yue_login_id;
           //print_r($data_info);exit;
           $user_add_obj->insert_model_info(true ,$user_id, $data_info);
           //print_r($user_info);
           //身材数据
           $stature_data['chest']    = $user_info['chest'];
           $stature_data['waist']    = $user_info['waist'];
           $stature_data['chest_inch'] = $user_info['hip'];
           $stature_data['height']   = $user_info['height'];
           $stature_data['weight']   = $user_info['weight'];
           $stature_data['sex']      = intval($user_info['sex']);
           $stature_data['age']      = substr($user_info['birthday'],0, 7);
           $user_add_obj->insert_model_stature($user_id, $stature_data);
           //print_r($stature_data);
           //风格循环
           if (!empty($user_info['model_style_v2']) && is_array($user_info['model_style_v2'])) 
           {
              foreach ($user_info['model_style_v2'] as $key => $vo) 
              {
                 $style_data['style']      = change_style_int($vo['style']);
                 $style_data['addh_price'] = $vo['continue_price'];
                 $price = $vo['price'];
                 if ($vo['hour'] == 2) 
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 else 
                 {
                    $style_data['twoh_price'] = $price;
                 }
                 $user_add_obj->add_style($user_id, $style_data);
               } 
            }
           //图片处理
            if (!empty($user_info['model_pic']) && is_array($user_info['model_pic'])) 
            {
              foreach ($user_info['model_pic'] as $pic_key => $vo) 
              {
                 $pic_data['img_url'] = $vo['img'];
                 $user_add_obj->insert_model_pic($pic_data, $user_id);
              }
            }
            //支付宝信息
            $other_data['alipay_info'] = $user_alipay;
            $other_data['information_sources'] = 'APP用户';
            $user_add_obj->insert_model_other($user_id, $other_data);
         }
    }
    /*die();
    print_r($user_info);exit;*/
    //var_dump($user_info);exit;
    if($model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time(),"audit_user_id"=>$yue_login_id, "reason"=> $reason), $user_id))
    {
        echo "<script>alert('操作成功');location.href='{$_SERVER['HTTP_REFERER']}'</script>";
        exit;
    }
    
}

if($_INPUT['act'] == 'approval')
{
    $user_id = $_INPUT['user_id'];
    $model_audit_obj->update_model(array("is_approval"=>$is_approval,"audit_time"=>time()), $user_id);

    $user_info = $user_obj->get_user_info($user_id);
    $location_id = $user_info["location_id"];

    //推送到热门模特
    //$hot_model_obj->add_model(array("user_id"=>$user_id,"location_id"=>$location_id));

    echo "<script>alert('审核成功');</script>";
    exit;
}



$where  = "1 AND aut.user_id = u.user_id";
$setParam = array();

//$setvar = "";
if ($is_approval != -1) 
{
  $where .= " AND aut.is_approval={$is_approval}";
  //$setvar = array("is_approval"=>$is_approval);
}
if($phone)
{
    $where .= " AND aut.cellphone={$phone}";
}

if($user_id)
{
    $where .= " AND aut.user_id = {$user_id}";
}
if($start_add_time && $end_add_time)
{
  $start_tmp_add_time = strtotime($start_add_time);
  $end_tmp_add_time   = strtotime($end_add_time)+24*3600;
  $where .= " AND aut.add_time BETWEEN {$start_tmp_add_time} AND {$end_tmp_add_time}";
}
if($start_audit_time && $end_audit_time)
{
  $start_tmp_audit_time = strtotime($start_audit_time);
  $end_tmp_audit_time   = strtotime($end_audit_time)+24*3600;
  $where .= " AND aut.audit_time BETWEEN {$start_tmp_audit_time} AND {$end_tmp_audit_time}";
}

//判断是否存在 地区管理员
if(strlen($authority_list[0]['location_id']) >0)
{
	$where .= " AND u.location_id IN ({$authority_list[0]['location_id']})";
	$setParam['aut_location'] = true;
}
//走正常流程
else
{
	if($province)
	{
		//$where .= " AND aut.user_id = u.user_id ";
		if ($location_id)
		{
			$where .= " AND u.location_id = {$location_id}";
		}
		else
		{
			$where .= " AND left(u.location_id,6) = {$province}";
		}
    }
}


//是否机构
if ($is_org) 
{
    $where .= " AND aut.user_id IN ";
}
else
{
  $where .= " AND aut.user_id  NOT IN ";
}
/*
*设置标题和排序
*/
$title = "待审核模特";
switch ($is_approval) 
{
    case -1:
      $title = "模特搜索页";
      $sort = 'add_time DESC';
      break;
    case 0:
        $title = "待审核模特";
        $sort = 'add_time DESC';
        break;
    case 1:
        $title = "审核通过模特";
        $sort = 'audit_time DESC';
        break;
    case 2:
        $title = "审核不通过模特";
        $sort = 'audit_time DESC';
        break;
}
if ($is_org) 
{
  $title = "[机构]".$title;
}
//var_dump($where);
$show_count = 20;
$page_obj->setvar (array("is_approval"=>$is_approval, "start_add_time"=>$start_add_time, "end_add_time" => $end_add_time, "start_audit_time"=>$start_audit_time,"end_audit_time"=>$end_audit_time, "province" => $province, "location_id" => $location_id, "is_org" => $is_org));
//$model_audit_obj->get_model_list_by_sql(false, $where);
$total_count = $model_audit_obj->get_model_list_by_sql(true,$where);
//导出数据
if ($act == 'export') 
{
    $list = $model_audit_obj->get_model_list_by_sql(false, $where, $sort, "0,{$total_count}");
    $data = array();
    foreach($list as $k=>$val)
    {
    $data[$k]['user_id']     = $val ['user_id'];
    $data[$k]['nickname']    = get_user_nickname_by_user_id($val ['user_id'] );
    $data[$k]['cellphone']   = $val['cellphone'];
    $data[$k]['add_time']    = date("Y-m-d",$val['add_time']);    
    $is_complete             = $model_card_obj->check_input_is_complete($val ['user_id']); 
    $data[$k]['is_complete'] = $is_complete != '' ? '是' : '否';
    $data[$k]['is_set']  = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);
    $data[$k]['city'] = get_poco_location_name_by_location_id($val['location_id']);
    $data[$k]['audit_name'] = get_user_nickname_by_user_id($val['audit_user_id']);
    $data[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d H:i", $val['audit_time']) : '';
    if ($val['is_approval'] == 0) 
    {
      $data[$k]['status'] = '待审核';
    }
    elseif ($val['is_approval'] == 1) 
    {
      $data[$k]['status'] = '审核通过';
    }
    else
    {
      $data[$k]['status'] = '审核不通过';
    }
    $data[$k]['reason'] = $val['reason'];
    
    //$list[$k]['is_org'] = $model_relate_org->get_is_org_by_user_id($val ['user_id']);
    
    
   }
   $fileName = '审核模特库';
   $title    = '模特列表';
   $headArr  = array("用户ID","app用户名","手机","注册时间","资料是否完整","是否入库","城市","操作者","操作时间","状态","备注");
   getExcel($fileName,$title,$headArr,$data);
   exit;
   //print_r($data);exit;
}


//列表
$page_obj->set ( $show_count, $total_count );

$list = $model_audit_obj->get_model_list_by_sql(false, $where, $sort, $page_obj->limit());
//var_dump($list);exit;
//print_r($list);exit;
foreach($list as $k=>$val){
    $list[$k]['add_time'] = date("Y-m-d",$val['add_time']);    
    $list[$k]['nickname'] = get_user_nickname_by_user_id($val ['user_id'] );
    $list[$k]['user_thumb'] = str_replace("_86","",get_user_icon($val ['user_id']));
    $list[$k]['user_icon'] = str_replace("_86","_100",get_user_icon($val ['user_id'], 86));
    $list[$k]['audit_name'] = get_user_nickname_by_user_id($val['audit_user_id']);
    $list[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d H:i", $val['audit_time']) : '';
    /*$user_info = $user_obj->get_user_info($val ['user_id']);
    $user_data_info = $user_obj->get_user_info_by_user_id($val['user_id']);
    print_r($user_info);//exit;
    $cellphone = $user_info["cellphone"];*/
    $add_time = date("Y-m-d H:i",$val["add_time"]);
    $list[$k]['is_complete'] = $model_card_obj->check_input_is_complete($val ['user_id']);
    //$list[$k]['is_org'] = $model_relate_org->get_is_org_by_user_id($val ['user_id']);
    $list[$k]['city'] = get_poco_location_name_by_location_id($val['location_id']);
    $list[$k]['cellphone'] = $val['cellphone'];
    $list[$k]['add_time'] = $add_time;
    $list[$k]['is_set']  = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);

}
    //省
    $province_list = change_assoc_arr($arr_locate_a);
    foreach ($province_list as $key => $vo) 
    {
        if ($vo['c_id'] == $province) 
        {
            $province_list[$key]['selected_prov'] = "selected='true'";
        }
        # code...
    }
    //城市
    if ($province) 
    {
        $city_list = ${'arr_locate_b'.$province};
        $city_list = change_assoc_arr($city_list);
        foreach ($city_list as $c_key => $vo) 
        {
           if ($vo['c_id'] == $location_id) 
           {
              $city_list[$c_key]['selected_city'] = "selected='true'";
           }
        }
    }
//print_r($list);
$tpl->assign('title', $title);
$tpl->assign('is_org', $is_org);
$tpl->assign('province_list', $province_list);
$tpl->assign('city_list', $city_list);
$tpl->assign('start_add_time', $start_add_time);
$tpl->assign('end_add_time', $end_add_time);
$tpl->assign('start_audit_time', $start_audit_time);
$tpl->assign('end_audit_time', $end_audit_time);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('total_count', $total_count);
$tpl->assign('list', $list);
$tpl->assign('phone', $phone);
$tpl->assign('user_id', $user_id);
$tpl->assign('is_approval', $is_approval);
$tpl->assign($setParam);
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
?>