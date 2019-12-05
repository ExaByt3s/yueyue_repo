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

$head_arr = array("用户id","基础认证状态","用户名","城市","手机号","类型","状态","操作名","添加时间","审核时间","备注");

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
$teacher_type_config = pai_mall_load_config('certificate_teacher_type');
$teacher_years_config =  pai_mall_load_config('certificate_teacher_years');
$teacher_train_type_config = pai_mall_load_config('certificate_teacher_train_type');
$sex_config = pai_mall_load_config('certificate_common_sex');
$work_type_config = pai_mall_load_config('certificate_cameror_work_type');
$order_income_config = pai_mall_load_config('certificate_cameror_order_income');
$team_config = pai_mall_load_config('certificate_cameror_team');


$job_config = pai_mall_load_config('certificate_diet_job');
$identification_config = pai_mall_load_config('certificate_diet_identification');
$max_forward_config = pai_mall_load_config('certificate_diet_max_forward');
$diet_years_config = pai_mall_load_config('certificate_diet_years');
$service_type_config = pai_mall_load_config('certificate_service_type');

$service_not_pass_config = pai_mall_load_config('certificate_service_not_pass_reason');

$other_identifine_label_config = pai_mall_load_config('certificate_other_identifine_label');

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
            echo "<script>alert('状态不是未审核不能修改');window.open('server_sq.php?status=0','_self');</script>";
            exit;
        }
        
        $type_id_service_type_config = pai_mall_load_config('certificate_service_type_id_service_type');
        //键值交互
        $service_static_arys = array_flip($type_id_service_type_config);
        
        $basic_status = $mall_basic_check_obj->get_person_status_by_user_id($user_id);
        
        //通过服务，要基础认证通过才可以
        if( $basic_status['status'] != 1 && $status == 1)
        {
            echo "<script>alert('基础认证不通过,不能操作');window.open('server_sq.php?seller_user_id=".$user_id."','_self');</script>";
            exit;
        }
        
        $rs = $mall_service_check_obj->change_status($id,$status,$yue_login_id);
        
        $phone = $mall_user_obj->get_phone_by_user_id($service_one['user_id']);
        
        //获取seller_info 为了做代码兼容
        $seller_info = $mall_seller_obj->get_seller_info($user_id,2);
        
        $type_ary = array(0=>$service_static_arys[$service_type]);
        
        //不通过
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
            
            echo "<script>alert('操作成功');window.open('server_sq.php?status=0','_self');</script>";
            exit;
        }
        
        //通过
        if( $status == 1 && $rs )
        {
            $res = $mall_seller_obj->update_seller_profile_type_id($seller_info['seller_data']['profile']['0']['seller_profile_id'],$type_ary);
			
            $mall_seller_obj->add_seller_service_belong($seller_info['seller_data']['seller_id'],$type_ary,$yue_login_id);
            
			//通过服务就将商家状态变为1
            $mall_service_check_obj->update_seller_status($user_id,1);
            
            //将认证的信息,同步到商家详情
            $mall_service_check_obj->update_rz_info_to_seller($id,$service_type,$seller_info['seller_data']['profile']['0']['seller_profile_id']);
            
			if($res['result'] == 1)
            {
                //发送成功短信
                if( ! empty($phone))
                {
                    $group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_SERVICE_SUCCESS';
                    $data = array(
                        'date' => date('m月d日H:i', $service_one['add_time']),
                    );
                    $ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
                }
                
                echo "<script>alert('操作成功');window.open('server_sq.php?status=0','_self');</script>";
                exit;
            }else{
                echo "<script>alert('没有该信息');window.open('server_sq.php?status=0','_self');</script>";
                exit;
            }
            
        }else
        {
            echo "<script>alert('操作失败');window.open('server_sq.php?status=0','_self');</script>";
            exit;
        }
        
    break;
    
    case 'info':
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."server_sq_info.tpl.htm" );
        
        $service_id = $_INPUT['service_id'];
        $what_service = $_INPUT['service_type'];
        $info = $mall_service_check_obj->get_info($service_id,$what_service);
        //处理反斜杠
        if( ! empty($info['0']['service_params_data']))
        {
            $match_imgs = array();
            foreach($info['0']['service_params_data'] as $k => &$v)
            {
                $v['value'] = stripslashes($v['value']);
            }
        }
        
        
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
            if($info['0']['studio_place'] !=='')
            {
                $info['0']['studio_place'] = get_poco_location_name_by_location_id($info['0']['location_id']).$info['0']['studio_place'];
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
                $info['0']['years'] = $years_config[$info['0']['years']] ? $years_config[$info['0']['years']] : $info['0']['years']."年";
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
                $info['0']['years'] = $years_config[$info['0']['years']] ? $years_config[$info['0']['years']] : $info['0']['years']."年";
            }
            
            if( ! empty($info['0']['teacher_type']))
            {
                $info['0']['teacher_type'] = $teacher_type_config[$info['0']['teacher_type']];
            }
            if( ! empty($info['0']['teacher_train_type']))
            {
                $info['0']['teacher_train_type'] = $teacher_type_config[$info['0']['teacher_train_type']];
            }
        }else if($info['0']['service_type'] == 'cameror')
        {
            if( ! empty($info['0']['years']))
            {
                $info['0']['years'] = $years_config[$info['0']['years']] ? $years_config[$info['0']['years']] : $info['0']['years']."年";
            }
            
            if( ! empty($info['0']['team']))
            {
                $info['0']['team'] = $team_config[$info['0']['team']];
            }
            if( ! empty($info['0']['order_income']))
            {
                $info['0']['order_income'] = $order_income_config[$info['0']['order_income']];
            }
        }else if($info['0']['service_type'] == 'other')
        {
            if( ! empty($info['0']['other_identifine']))
            {
                $other_identifine_ary = explode(',', $info['0']['other_identifine']);
                $other_identifine_str = '';
                foreach($other_identifine_ary as $k => &$v)
                {
                    $other_identifine_str .= $other_identifine_label_config[$v].",";
                }
                $info['0']['other_identifine'] = substr($other_identifine_str,0,-1);
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
            $info['0']['basic_info'] = $mall_service_check_obj->get_basic_info_by_user_id($info['0']['user_id']);
            if($info['0']['basic_info']['status'] == 0)
            {
                $info['0']['basic_info']['status_name'] = "<span style='color:black'>未审核</span>";
            }else if($info['0']['basic_info']['status'] == 1)
            {
                $info['0']['basic_info']['status_name'] = "<span style='color:green'>通过</span>";
            }else if($info['0']['basic_info']['status'] == 2)
            {
                $info['0']['basic_info']['status_name'] = "<span style='color:red'>不通过</span>";
            }
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
            
        }
        
        $imgs = $mall_service_check_obj->get_service_imgs($service_id);
        if( ! empty($imgs) )
        {
            $info['0']['imgs'] = $imgs;
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
        $add_time_s = $_INPUT['add_time_s'];
        $add_time_e = $_INPUT['add_time_e'];
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
            $where .= " and (i.user_id = "."'".(int)$keyword."'"." or i.phone like '".pai_mall_change_str_in($keyword)."%')";
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
        
        if( $add_time_s && $add_time_e )
        {
            $add_s = strtotime($add_time_s);
            $add_e = strtotime($add_time_e)+86400;
            $where .= " AND i.add_time > '$add_s' and i.add_time < '$add_e'";
        }
        
		$total_count = $mall_service_check_obj->get_service_list(true, $where);
        $page_obj = new show_page();
		$show_count = 20;
		$page_obj->setvar ( array (
            "status" => $status,
            'keyword'=>$keyword,
            'service_type'=>$what_service,
            'seller_user_id'=>$seller_user_id,
            'city'=>$city,
            'location_id'=>$location_id,
            'add_time_s'=>$add_time_s,
            'add_time_e'=>$add_time_e,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
        ) );
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
                
                $basic_info = $mall_service_check_obj->get_basic_info_by_user_id($val['user_id']);
                if($basic_info['status'] == 2)
                {
                    $val['baisc_info_status'] = "未通过";
                }else if( $basic_info['status'] == 1 )
                {
                    $val['baisc_info_status'] = "已通过";
                }else if( $basic_info['status'] == 0 )
                {
                    $val['baisc_info_status'] = "未审核";
                }
                
                
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
                }else
                {
                    $val['update_time'] = '--';
                }
                
                $output_data[$key]['user_id'] = $val['user_id'];
                $output_data[$key]['basic_info_status'] = $val['baisc_info_status'];
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
            
            $basic_info = $mall_service_check_obj->get_basic_info_by_user_id($val['user_id']);
           
            if($basic_info['status'] == 2)
            {
                $val['baisc_info_status'] = "<span style='color:red;'>未通过</span>";
            }else if( $basic_info['status'] == 1 )
            {
                $val['baisc_info_status'] = "<span style='color:green;'>已通过</span>";
            }else if( $basic_info['status'] == 0 )
            {
                $val['baisc_info_status'] = "<span style='color:black;'>未审核</span>";
            }
            
            $val['basic_id'] = $basic_info['basic_id'];
            unset($basic_info);
            
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
            }else
            {
                $val['update_time'] = '--';
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
            
            if($val['status'] == 0)
            {
                //server_sq.php?action=check&status=1&service_id={service_id}&seller_user_id={user_id}&service_type={service_type}
                $val['rz_url'] = urlencode("server_sq.php?action=check&status=1&service_id={$val['service_id']}&seller_user_id={$val['user_id']}&service_type={$val['service_type']}");
                $type_id_service_type = pai_mall_load_config('certificate_service_type_id_service_type');
                $service_type_type_id = array_flip($type_id_service_type);
                $val['type_id'] = $service_type_type_id[$val['service_type']];
                
            }
            
            $pai_risk_obj = POCO::singleton('pai_risk_class');
            $seller_scalping_info = $pai_risk_obj->get_scalping_seller_info($val['user_id']);
            if ($seller_scalping_info['add_by']=='system')
            {
                //系统
                $list[$key]['is_flush'] = 1;
                $list[$key]['scalping_titile'] = '刷单嫌疑(系统)';
            } elseif($seller_scalping_info['add_by']=='manual')
            {
                //人工
                $list[$key]['is_flush'] = 1;
                $list[$key]['scalping_titile'] = '刷单嫌疑(人工)';
            }
            
            
        }
        
        $tpl->assign('id_test',$yue_login_id);
        $tpl->assign('service_not_pass_config',$service_not_pass_config);
        $tpl->assign('city',$city);
        $tpl->assign('location_id',$location_id);
        $tpl->assign('keyword',$keyword);
        $tpl->assign('begin_time',$begin_time);
        $tpl->assign('end_time',$end_time);
        $tpl->assign('add_time_s',$add_time_s);
        $tpl->assign('add_time_e',$add_time_e);
        $tpl->assign('what_service',$what_service);
        $tpl->assign('status',$status);
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>