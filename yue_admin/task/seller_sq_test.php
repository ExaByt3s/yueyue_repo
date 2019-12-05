<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';
$mall_basic_check_obj = POCO::singleton('pai_mall_certificate_basic_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$mall_user_obj = POCO::singleton('pai_user_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');

//加载配置文件
$status_config = pai_mall_load_config('certificate_common_status');
$bank_config = pai_mall_load_config('certificate_company_bank');

if($_INPUT['submit_remark'])
{
    $basic_id = $_INPUT['basic_id'];
    $remark = iconv('utf-8','gbk', $_INPUT['remark']);
    $rs = $mall_basic_check_obj->update_remark($basic_id,$remark);
    if( $rs )
    {
        echo json_encode(1);
    }
    
    exit;
    
}

if($_INPUT['re_check'])
{
    $basic_id = (int)$_INPUT['basic_id'];   
    $rs = $mall_basic_check_obj->change_status($basic_id,0,$yue_login_id);
    if($rs)
    {
        echo json_encode(1);
        exit;
    }
    exit;
    
}

$head_arr = array("用户id","用户名","城市","手机号","类型","状态","添加时间","操作员","审核时间","备注");

switch($action)
{
    //商家申请
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
			$rs = $mall_basic_check_obj->add_seller_sq($data);
            if($rs['status'] == 1)
            {
                echo "<script>alert('添加成功');window.open('seller_sq.php','_self');</script>";
                exit;
            }else if($rs['status'] == -1)
            {
                echo "<script>alert('资料不完整不能提交');window.open('seller_sq.php','_self');</script>";
                exit;
            }
            else
            {
                echo "<script>alert('插入失败');window.open('seller_sq.php','_self');</script>";
                exit;
            }
		}
        //引入模板
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_sq_add.tpl.htm" );
        ///////////////////////
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
        $tpl->assign ( 'global_header_html', $global_header_html );
        ///////////////////////
    break;
    //商家审核
    case 'check':
        
        $basic_id = (int)$_GET['basic_id'];
        $status = (int)$_GET['status'];
        $basic_type = addslashes($_GET['basic_type']);
        $user_id = (int)$_GET['user_id'];
        $name = addslashes($_GET['name']);
        
        $basic_one = $mall_basic_check_obj->get_info($id);
        if($basic_one['status'] !=0 )
        {
           echo "<script>alert('状态不是未审核不能改');window.open('seller_sq.php','_self');</script>";
           exit;
        }
        
        $rs = $mall_basic_check_obj->change_status($basic_id,$status,$yue_login_id);
        
        //审核通过
        if($status == 1)
        {
            if($rs)
            {
                //发送基础验证成功短信
                $phone = $mall_user_obj->get_phone_by_user_id($basic_one['user_id']);
				
				if( ! empty($phone) )
				{
					$group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_SUCCESS';
					$data = array(
					'date' => date('m月d日H:i', $basic_one['add_time']),
					);
					$ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
				}
				
                echo "<script>alert('操作成功');window.open('seller.php?action=companylist&id=".$rs."','_self');</script>";
                exit;
            }else
            {
                echo "<script>alert('操作失败');window.open('seller_sq.php','_self');</script>";
                exit;
            }
        }

        //审核不通过
        if($status == 2)
        {
			if($rs)
            {
                //发送基础验证失败短信
                $phone = $mall_user_obj->get_phone_by_user_id($basic_one['user_id']);
                
                if( ! empty($phone) )
                {
                    $group_key = 'G_PAI_MALL_SELLER_CERTIFICATE_BASIC_FAIL';
                    $data = array(
                        'date' => date('m月d日H:i', $basic_one['add_time']),
                    );
                    $ret = $pai_sms_obj->send_sms($phone, $group_key, $data);
                }
                echo "<script>alert('操作成功');window.open('seller_sq.php','_self');</script>";
                exit;
            }else
            {
                echo "<script>alert('操作失败');window.open('seller_sq.php','_self');</script>";
                exit;
            }
        }
        
        
        exit;
        
        
    break;
    case 'info':
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_sq_info.tpl.htm" );
        $basic_id = $_INPUT['basic_id'];
        $info = $mall_basic_check_obj->get_info($basic_id);
        
        //数据组装 每种类型特有的
        if($info['basic_type'] == 'person')
        {
            if( $info['brand_img_url'] )
            {
                $info['brand_img_url'] = yueyue_resize_act_img_url($info['brand_img_url'], $size = '');
            }

            if( $info['heads_img_url'] )
            {
                $info['heads_img_url'] = yueyue_resize_act_img_url($info['heads_img_url'], $size = '');
            }

            if( $info['tails_img_url'] )
            {
                $info['tails_img_url'] = yueyue_resize_act_img_url($info['tails_img_url'], $size = '');
            }
            
            if( ! empty($info['person_zone_id']) )
            {
                $info['person_zone_id'] = get_poco_location_name_by_location_id ( $info['person_zone_id'] );
            }
        }else if($info['basic_type'] == 'company')
        {
            if( $info['company_license_img_url'] )
            {
                $info['company_license_img_url'] = yueyue_resize_act_img_url($info['company_license_img_url'], $size = '');
            }
            if( ! empty($info['company_date_line']))
            {
                $info['company_date_line'] = date('Y-m-d H:i:s',$info['company_date_line']);
            }
            if( ! empty($info['company_bank_id']) )
            {
                $info['company_bank_id'] = $bank_config[$info['company_bank_id']];
            }
            
            if( ! empty($info['company_bank_city_id']))
            {
                $info['company_bank_city_id'] = get_poco_location_name_by_location_id ( $info['company_bank_city_id'] );
            }
            
        }
        
        //公共的
        if( $info['status'] !=='' )
        {
            $info['status'] = $status_config[$info['status']];
        }
        if( ! empty($info['user_id']) )
        {
            $info['user_id'] = get_user_nickname_by_user_id($info['user_id']);
        }
        if( ! empty($info['update_time']))
        {
            $info['update_time'] = date('Y-m-d H:i:s',$info['update_time']);
        }
        
        $tpl->assign('info',$info);
    break;

    //列表页
    default://商家申请列表
        $status = isset($_INPUT['status']) && $_INPUT['status']!=='' ? (int)$_INPUT ['status'] : '';
        $basic_type = $_INPUT['basic_type'];
        $begin_time = $_INPUT['begin_time'];
        $end_time = $_INPUT['end_time'];
        $keyword = $_INPUT['keyword'];
        $city = $_INPUT['city'];
        $location_id = $_INPUT['location_id'];
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_sq_list.tpl.htm" );
        
        //where 条件
		$where = 1;
        
        if( $keyword )
        {
            $where .= " and (user_id = "."'".(int)$keyword."'"." or phone like '%".pai_mall_change_str_in($keyword)."%')";
        }
        
        if($location_id)
        {
            $where .= " and ( person_zone_id='$location_id' or company_bank_city_id='{$location_id}' )";
        }
        
        if( $status !=='')
        {
            $where .= " AND status='{$status}'";
        }
        if( $basic_type )
        {
            $where .= " AND basic_type='{$basic_type}'";
        }
        if( $begin_time && $end_time )
        {
            $s = strtotime($begin_time);
            $e = strtotime($end_time)+86400;
            $where .= " AND update_time  > '$s' AND update_time < '$e' ";
        }
        
		$total_count = $mall_basic_check_obj->get_seller_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,'keyword'=>$keyword,'basic_type'=>$basic_type,'begin_time'=>$begin_time,'end_time'=>$end_time,'city'=>$city,'location_id'=>$location_id) );
		$page_obj->set ( $show_count, $total_count );		
        
        if($_INPUT['output']==1)
        {
            set_time_limit(0);
            $export_page = (int)$_INPUT['export_page'];
            $export_offect = ($export_page-1)*1000;
            $export_list = $mall_basic_check_obj->get_seller_list(false, $where, "basic_id desc", "$export_offect,1000");
            
            foreach($export_list as $key => &$val)
            {
                $val['user_name'] = get_user_nickname_by_user_id($val['user_id']);
                $val['operator_name'] = $val['operator_id'] ? get_user_nickname_by_user_id($val['operator_id']) : '--';
                
                $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);

                if($val['basic_type'] == 'person')
                {
                    $val['city_name'] = get_poco_location_name_by_location_id($val['person_zone_id']);
                }else
                {
                    $val['city_name'] = get_poco_location_name_by_location_id ($val['company_bank_city_id']);
                }
                
                if(empty($val['city_name']))
                {
                    $val['city_name'] = '全国';
                }

                if($val['update_time'])
                {
                    $val['update_time'] = date('Y-m-d H:i:s',$val['update_time']);
                }
                
                $output_data[$key]['user_id'] = $val['user_id'];
                $output_data[$key]['user_name'] = $val['user_name'];
                $output_data[$key]['city_name'] = $val['city_name'];
                $output_data[$key]['phone'] = $val['phone'];
                $output_data[$key]['basic_type'] = $val['basic_type'];
                $output_data[$key]['status'] = $status_config[$val['status']];
                $output_data[$key]['add_time'] = $val['add_time'];
                $output_data[$key]['operator_name'] =  $val['operator_name'];
                $output_data[$key]['update_time'] = $val['update_time'];
                $output_data[$key]['remark'] = $val['remark'];
            }
            
            $fileName = "基础验证";
            getExcel ( $fileName, $head_arr, $output_data );
            //Excel_v2::start($head_arr,$output_data,$fileName,'s3');
            exit;
        }
        
		$list = $mall_basic_check_obj->get_seller_list(false, $where, "basic_id desc", $page_obj->limit());
        
		$add_time_color = '';
		
		foreach($list as $key => &$val)
		{
            //$mall_basic_check_obj->update_user_phone($val['user_id']);
            $val['user_name'] = get_user_nickname_by_user_id($val['user_id']);
            $val['operator_name'] = $val['operator_id'] ? get_user_nickname_by_user_id($val['operator_id'])."</br>[{$val['operator_id']}]" : '--';
			
            if( (time()-$val['add_time']) > 24*3600 && $val['status']== 0)
            {
                $add_time_color = "rgb(245, 60, 60);";
            }    
            $val['add_time_color'] = $add_time_color;
            $val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
            
            if($val['basic_type'] == 'person')
            {
                $val['city_name'] = get_poco_location_name_by_location_id ( $val['person_zone_id']);
            }else
            {
                $val['city_name'] = get_poco_location_name_by_location_id ( $val['company_bank_city_id']);
            }
            
            if(empty($val['city_name']))
            {
                $val['city_name'] = '全国';
            }
            
            if($val['update_time'])
            {
                $val['update_time'] = date('Y-m-d H:i:s',$val['update_time']);
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
            $list[$key]['status_show'] = "<span style='color:".$color.";font-weight:bold;'>".$status_config[$val['status']]."</span>";
            unset($add_time_color);
        }
        
        $tpl->assign('city',$city);
        $tpl->assign('location_id',$location_id);
        $tpl->assign('keyword',$keyword);
        $tpl->assign('begin_time',$begin_time);
        $tpl->assign('end_time',$end_time);
        $tpl->assign('basic_type',$basic_type);
        $tpl->assign('status',$status);
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
	break;
}
$tpl->output ();
?>