<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');
$mall_user_obj = POCO::singleton('pai_user_class');

if($_INPUT['submit_remark'])
{
    $service_id = $_INPUT['service_id'];
    $remark = iconv('utf-8','gbk', $_INPUT['remark']);
    $rs = $mall_service_check_obj->update_remark($service_id,$remark);
    if( $rs )
    {
        echo json_encode(1);
    }
    exit;
}

$head_arr = array("用户id","用户名","城市","手机号","类型","状态","操作名","添加时间","审核时间","备注");

//加载配置文件
$status_config = pai_mall_load_config('certificate_common_status');
$years_config = pai_mall_load_config('certificate_common_years');
$has_place_config = pai_mall_load_config('certificate_dresser_has_place');
$order_way_config = pai_mall_load_config('certificate_dresser_order_way');
$team_num_config = pai_mall_load_config('certificate_dresser_team_num');
$do_well_config = pai_mall_load_config('certificate_dresser_do_well');
$studio_area_config = pai_mall_load_config('certificate_studio_area');
$can_photo_config = pai_mall_load_config('certificate_studio_can_photo');
$photo_type_config = pai_mall_load_config('certificate_studio_photo_type');
$lighter_config = pai_mall_load_config('certificate_studio_lighter');
$other_config = pai_mall_load_config('certificate_studio_other');
$class_way_config = pai_mall_load_config('certificate_teacher_class_way');
$sex_config = pai_mall_load_config('certificate_common_sex');
$work_type_config = pai_mall_load_config('certificate_common_work_type');

$job_config = pai_mall_load_config('certificate_diet_job');
$identification_config = pai_mall_load_config('certificate_diet_identification');
$max_forward_config = pai_mall_load_config('certificate_diet_max_forward');
$diet_years_config = pai_mall_load_config('certificate_diet_years');
$service_type_config = pai_mall_load_config('certificate_service_type');

switch($action)
{
    //商家服务申请
    case 'add':
        if($_POST)
        {
            //$img = explode("\r\n",$_POST['img']);
			$img = $_INPUT['upload_imgs_0'];
			$data = $_POST;
			$img_data = array();
			foreach($img as $val)
			{
				$img_data[] = array('img_url'=>$val);
			}
            $data['user_id'] = $yue_login_id;
			$data['img'] = $img_data;
            
            print_r($data);
            exit;
			$rs = $mall_service_check_obj->add_service_sq($data);
            
            if($rs['status'] == 1)
            {
                echo "<script>alert('添加成功');window.open('server_sq.php','_self');</script>";
                exit;
            }else if($rs['status'] == -1)
            {
                echo "<script>alert('资料不完整');window.open('server_sq.php','_self');</script>";
                exit;
            }
            else
            {
                echo "<script>alert('添加失败');window.open('server_sq.php','_self');</script>";
                exit;
            }
		}
        //引入模板
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."server_sq_add.tpl.htm" );
        ///////////////////////
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
        $tpl->assign ( 'global_header_html', $global_header_html );
        ///////////////////////
    break;
    //商家服务审核
    case 'check':
        
        $id = (int)$_INPUT['service_id'];
        $status = (int)$_INPUT['status'];
        $user_id = (int)$_INPUT['seller_user_id'];
        $service_type = trim($_INPUT['service_type']);
        
        $service_one = $mall_service_check_obj->get_service_info($id);
        
        if($service_one['status'] !=0 )
        {
            echo "<script>alert('状态不是未审核不能修改');window.open('server_sq.php','_self');</script>";
            exit;
        }
        
        $service_static_arys = array(
            'model'=>31,
            'cameror'=>40,
            'teacher'=>5,
            'dresser'=>3,
            'studio'=>12,
            'diet'=>41
        );
        
        $basic_status = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
        
        //通过服务，要基础认证通过才可以
        if( $basic_status['status'] != 1 && $status == 1)
        {
            echo "<script>alert('基础认证不通过,不能操作');window.open('server_sq.php?seller_user_id=".$user_id."','_self');</script>";
            exit;
        }
        
        $rs = $mall_service_check_obj->change_status($id,$status,$yue_login_id);
        
        $phone = $mall_user_obj->get_phone_by_user_id($service_one['user_id']);
        
        $seller_info = $mall_seller_obj->get_seller_info($user_id,2);
        
        $type_ary[] = $service_static_arys[$service_type];
        
		if($status==2 && $rs)
        {
            $mall_seller_obj->del_seller_profile_type_id($seller_info['seller_data']['profile']['0']['seller_profile_id'],$type_ary);
            //发送服务验证失败短信
            if( ! empty($phone) )
            {
                $group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_FAIL';
                $data = array(
                    'date' => date('m月d日H:i', $service_one['add_time']),
                );
                $ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
            }
            
            echo "<script>alert('操作成功');window.open('server_sq.php','_self');</script>";
            exit;
        }
        
        if($rs)
        {
            //更新商家状态
            $mall_service_check_obj->update_seller_status($user_id,1);
            
            //添加商家服务所属
            $mall_seller_obj->add_seller_service_belong($seller_info['seller_data']['seller_id'],$service_static_arys[$service_type],$yue_login_id);
            
            //发送成功短信
            if( ! empty($phone))
            {
                $group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_SUCCESS';
                $data = array(
                    'date' => date('m月d日H:i', $service_one['add_time']),
                );
                $ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
            }

            echo "<script>alert('操作成功');window.open('server_sq.php','_self');</script>";
            exit;
            
        }else
        {
            echo "<script>alert('操作失败');window.open('server_sq.php?seller_user_id=".$user_id."','_self');</script>";
            exit;
        }
        
    break;
    
    case 'info':
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."server_sq_info.tpl.htm" );
        
        $service_id = $_INPUT['service_id'];
        $what_service = $_INPUT['service_type'];
        $info = $mall_service_check_obj->get_info($service_id,$what_service);
        
        if($info['0']['service_type'] == 'studio')
        {
            if($info['0']['other']!=='')
            {
                $arys = explode(',',$info['0']['other']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$other_config[$v].",";
                }
                $info['0']['other'] = substr($str,0,-1);
            }
            
            if($info['0']['lighter']!=='')
            {
                $arys = explode(',',$info['0']['lighter']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$lighter_config[$v].",";
                }
                $info['0']['lighter'] = substr($str,0,-1);
            }
            
            if($info['0']['photo_type']!=='')
            {
                $arys = explode(',',$info['0']['photo_type']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$photo_type_config[$v].",";
                }
                $info['0']['photo_type'] = substr($str,0,-1);
            }
            
            if($info['0']['can_photo_type']!=='')
            {
                $arys = explode(',',$info['0']['can_photo_type']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$can_photo_config[$v].",";
                }
                $info['0']['can_photo_type'] = substr($str,0,-1);
            }
            
            if($info['0']['studio_area']!=='')
            {
                $arys = explode(',',$info['0']['studio_area']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$studio_area_config[$v].",";
                }
                $info['0']['studio_area'] = substr($str,0,-1);
            }
        
        }else if($info['0']['service_type'] == 'dresser')
        {
            if($info['0']['do_well']!=='')
            {
                $arys = explode(',',$info['0']['do_well']);
                $str = '';
                foreach($arys as $v)
                {
                    $str.=$do_well_config[$v].",";
                }
                $str = substr($str,0,-1);
                $final_str = $str.' '.$info['0']['do_well_other'];
                $info['0']['do_well'] = $final_str;
            }
            if($info['0']['team_num']!=='')
            {
                $info['0']['team_num'] = $team_num_config[$info['0']['team_num']];
            }
            if($info['0']['order_way'] !=='')
            {
                $info['0']['order_way'] = $order_way_config[$info['0']['order_way']];
            }
            
            if( $info['0']['has_place'] !=='')
            {
                $info['0']['has_place'] = $has_place_config[$info['0']['has_place']];
            }
            if( ! empty($info['0']['years']))
            {
                $info['0']['years'] = $years_config[$info['0']['years']];
            }
            
            
        }else if($info['0']['service_type'] == 'diet')
        {
            if( ! empty($info['0']['diet_job']) )
            {
                $info['0']['diet_job'] = $job_config[$info['0']['diet_job']];
            }
            if( ! empty($info['0']['diet_years']) )
            {
                $info['0']['diet_years'] = $diet_years_config[$info['0']['diet_years']];
            }
            if( ! empty($info['0']['diet_max_forward']) )
            {
                $info['0']['diet_max_forward'] = $max_forward_config[$info['0']['diet_max_forward']];
            }
            if( ! empty($info['0']['diet_identification']) )
            {
                $info['0']['diet_identification'] = $identification_config[$info['0']['diet_identification']];
            }
        }else if($info['0']['service_type'] == 'model')
        {
            if($info['0']['sex']!=='')
            {
                $info['0']['sex'] = $sex_config[$info['0']['sex']];
            }
        }else if($info['0']['service_type'] == 'teacher')
        {
            if($info['0']['class_way']!=='')
            {
                $info['0']['class_way'] = $class_way_config[$info['0']['class_way']];
            }
            if( ! empty($info['0']['years']))
            {
                $info['0']['years'] = $years_config[$info['0']['years']];
            }
        }else if($info['0']['service_type'] == 'cameror')
        {
            if( ! empty($info['0']['years']))
            {
                $info['0']['years'] = $years_config[$info['0']['years']];
            }
        }
        
        //数据组装
        
        if($info['0']['city_id'] !='')
        {
            $info['0']['city_name'] = get_poco_location_name_by_location_id($info['0']['city_id']);
        }else
        {
            $info['0']['city_name'] = '全国';
        }
        if( $info['0']['status'] !=='')
        {
            $info['0']['status'] = $status_config[$info['0']['status']];
        }
        if( ! empty($info['0']['user_id']))
        {
            $info['0']['user_name'] = get_user_nickname_by_user_id($info['0']['user_id']);
        }
        if( ! empty($info['0']['operator_id']))
        {
            $info['0']['operator_id'] = get_user_nickname_by_user_id($info['0']['operator_id']);
        }
        if( ! empty($info['0']['update_time']))
        {
            $info['0']['update_time'] = date('Y-m-d H:i:s',$info['0']['update_time']);
        }
        if( ! empty($info['0']['author_content']))
        {
            $info['0']['author_content'] = unserialize($info['0']['author_content']);
            foreach($info['0']['author_content'] as $wk => &$wv)
            {
                $wv['work_type_id'] = $work_type_config[$wv['work_type_id']];
                foreach($wv['work_img'] as $ik => &$iv)
                {
                    $iv['img_url'] = yueyue_resize_act_img_url($iv['img_url'], $size = '');
                    $img_str .= "<a href='{$iv['img_url']}' target='_blank'><img src='{$iv['img_url']}' /></a>";
                }
                $wv['work_img_data'] = "<p>{$img_str}</p>";
                unset($img_str);
            }
            
        }else
        {
            $info['0']['imgs'] = $mall_service_check_obj->get_service_imgs($service_id);
            foreach($info['0']['imgs'] as $k => &$v)
            {
                $v['img_url'] = yueyue_resize_act_img_url($v['img_url'], $size = '');
                $img_str .= "<a href='{$v['img_url']}' target='_blank'><img src='{$v['img_url']}' /></a>";
            }
            $info['0']['imgs_data'] = "<p>{$img_str}</p>";
            unset($img_str);
        }
        
        $tpl->assign('info',$info['0']);
    break;

    //列表页
    default://商家服务申请列表
        
        $begin_time = $_INPUT['begin_time'];
        $end_time = $_INPUT['end_time'];
        $city = $_INPUT['city'];
        $location_id = $_INPUT['location_id'];
        
		$status = isset($_INPUT['status']) && $_INPUT['status']!=='' ? (int)$_INPUT ['status'] : '';
        
        $what_service = $_INPUT['service_type'] ? addslashes($_INPUT['service_type']) : '';
        
        $seller_user_id = isset($_INPUT['seller_user_id']) ? $_INPUT['seller_user_id'] : '';
        
        $keyword = $_INPUT['keyword'];
        
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."server_sq_list.tpl.htm" );
        
        //where 条件
		$where = " WHERE 1";
        
        if( $keyword )
        {
            $where .= " and (i.user_id = "."'".(int)$keyword."'"." or i.phone like '%".pai_mall_change_str_in($keyword)."%')";
        }
        if( $status !=='')
        {
            $where .= " AND i.status='{$status}'";
        }
        
        if($location_id)
        {
            $where .= " AND i.city_id='{$location_id}'";
        }
        
        if( $what_service )
        {
            $where .= " AND i.service_type='$what_service'";
        }
        if($seller_user_id)
        {
            $where .= " AND i.user_id='$seller_user_id'";
        }
        
        if( $begin_time && $end_time )
        {
            $s = strtotime($begin_time);
            $e = strtotime($end_time)+86400;
            $where .= " AND i.update_time  > '$s' AND i.update_time < '$e' ";
        }
        
		$total_count = $mall_service_check_obj->get_service_list(true, $where);
        $page_obj = new show_page();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,'keyword'=>$keyword,'service_type'=>$what_service,'seller_user_id'=>$seller_user_id,'city'=>$city,'location_id'=>$location_id) );
		$page_obj->set ( $show_count, $total_count );
        
        if($_INPUT['output']==1)
        {
            set_time_limit(0);
            $export_page = (int)$_INPUT['export_page'];
            $export_offect = ($export_page-1)*1000;
            $export_list = $mall_service_check_obj->get_service_list(false, $where,"i.service_id", "{$export_offect},1000",'*',true);
            foreach($export_list as $key => &$val)
            {
                //$mall_service_check_obj->update_user_phone($val['user_id']);
                $val['user_name'] = get_user_nickname_by_user_id($val['user_id']);
                $val['operator_name'] = get_user_nickname_by_user_id($val['operator_id']);
                $val['city_name'] = get_poco_location_name_by_location_id($val['city_id']);
                
                if( empty($val['city_name']) )
                {
                    $val['city_name'] = '全国';
                }
                
                //商家用户id 回选
                $val['seller_user_id'] = $seller_user_id;
                $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
                $val['service_name'] = $service_type_config[$val['service_type']];
               
                if($val['update_time'])
                {
                    $val['update_time'] = date("Y-m-d H:i:s",$val['update_time']);
                }
                $output_data[$key]['user_id'] = $val['user_id'];
                $output_data[$key]['user_name'] = $val['user_name'];
                $output_data[$key]['city_name'] = $val['city_name'];
                $output_data[$key]['phone'] = $val['phone'];
                $output_data[$key]['service_type'] = $val['service_name'];
                $output_data[$key]['status'] = $status_config[$val['status']];
                $output_data[$key]['operator_name'] = $val['operator_name'];
                $output_data[$key]['add_time'] = $val['add_time'];
                $output_data[$key]['update_time'] = $val['update_time'];
                $output_data[$key]['remark'] = $val['remark'];
            }
            
            $fileName = "服务验证";
            getExcel ( $fileName, $head_arr, $output_data );
            //Excel_v2::start($head_arr,$output_data,$fileName,'s3');
            exit;
        }
        
		$list = $mall_service_check_obj->get_service_list(false, $where,"i.service_id", $page_obj->limit());
        
        
        $add_time_color = '';
        
        foreach($list as $key => &$val)
		{
            //$mall_service_check_obj->update_user_phone($val['user_id']);
            $val['user_name'] = get_user_nickname_by_user_id($val['user_id']);
            $val['city_name'] = get_poco_location_name_by_location_id($val['city_id']);
            
            if( empty($val['city_name']) )
            {
                $val['city_name'] = '全国';
            }
            
            $val['operator_name'] = $val['operator_id'] ? get_user_nickname_by_user_id($val['operator_id'])."</br>[{$val['operator_id']}]" : '--';
            //商家用户id 回选
            $val['seller_user_id'] = $seller_user_id;
            
            
            if( (time()-$val['add_time']) > 24*3600 && $val['status']== 0)
            {
                $add_time_color = "rgb(245, 60, 60);";
            }
            
            $val['add_time_color'] = $add_time_color;
            
            $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
            
            $val['service_name'] = $service_type_config[$val['service_type']];
            
            if($val['update_time'])
            {
                $val['update_time'] = date("Y-m-d H:i:s",$val['update_time']);
            }
            
            if($val['status']==1)
            {
                $color = "green";
            }else if($val['status']==2)
            {
                $color = "red";
            }else
            {
                $color = "black";
            }
            $val['status_name'] = "<span style='color:".$color.";font-weight:bold;'>".$status_config[$val['status']]."</span>";
            unset($add_time_color);
            
        }
        
        $tpl->assign('city',$city);
        $tpl->assign('location_id',$location_id);
        $tpl->assign('keyword',$keyword);
        $tpl->assign('begin_time',$begin_time);
        $tpl->assign('end_time',$end_time);
        $tpl->assign('what_service',$what_service);
        $tpl->assign('status',$status);
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>