<?php
ini_set('memory_limit','512M');
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';
$mall_obj = POCO::singleton('pai_mall_seller_class');
$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$service_obj = POCO::singleton('pai_mall_certificate_service_class');
$_set_debug = false;
if($_GET['debug']=='t')
{
	setcookie('debug',true,time()+3600,'/','yueus.com');
	$_set_debug = true;
}
elseif($_GET['debug']=='f')
{
	setcookie('debug',false,time()+3600,'/','yueus.com');
}
if($_COOKIE['debug'])
{
	$_set_debug = true;
}
if($_set_debug)
{
	//$mall_obj->set_debug();
}

switch($action)
{
    case "addstore":
	    $company_id = (int)$_INPUT['id'];
		$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
	    $type_list = $goods_type_obj->get_type_cate(2);
	    if($_POST)
		{
			$data = $_INPUT;
			$data['add_user'] = $yue_login_id;
			$in = $mall_obj->add_store($data);
			switch($in)
			{
				case -1:
					$message = '无权限';
				break;
				case -2:
					$message = '参数错误';
				break;
				case -3:
					$message = '超出最大店铺数量';
				break;
				case -4:
					$message = '该公司已关闭';
				break;
				default:
					$message = '添加成功';
				break;
			}
			echo "<script>alert('{$message}');window.open('seller_search.php?action=addstore&id=".$data['company_id']."','_self')</script>";
			exit;
		}
		$data = $mall_obj->show_store_data();
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."store_add.tpl.htm" );
        ///////////////////////
		$tpl->assign ( 'id', $company_id );
		$tpl->assign ( 'type_list', $type_list);
		$tpl->assign ( 'default_data', $data['default_data'] );
	break;
    case "addcompany":
	    $seller_id = (int)$_INPUT['id'];
	    if($_POST)
		{
			//$img = explode("\r\n",$_POST['img']);
			$data = $_INPUT;
			$data['add_user'] = $yue_login_id;
			$in = $mall_obj->add_company($data);
			switch($in)
			{
				case -1:
					$message = '无权限';
				break;
				case -2:
					$message = '参数错误';
				break;
				case -3:
					$message = '超出最大公司数量';
				break;
				case -4:
					$message = '商家已经关闭,不能添加';
				break;
				default:
					$message = '添加成功';
				break;
			}
			echo "<script>alert('{$message}');window.open('seller_search.php?action=addcompany&id={$seller_id}','_self')</script>";
			exit;
		}
		$goods = $mall_obj->show_seller_company_data();
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."company_add.tpl.htm" );
        ///////////////////////
		$tpl->assign ( 'id', $seller_id );
		$tpl->assign ( 'default_data', $goods['default_data'] );
	break;
    case "addseller":
	    if($_POST)
		{
			//$img = explode("\r\n",$_POST['img']);
			$img = $_INPUT['upload_imgs_0'];
			$data = $_INPUT;
			$data['add_user'] = $yue_login_id;
			$in = $mall_obj->add_seller($data);
			print_r($in);
			exit;
		}
		$seller = $mall_obj->show_seller_data();
		foreach($seller['default_data'] as $key => $val)
		{
			if($val['key'] == 'basic_type')
			{
				$input_conf = pai_mall_load_config("seller_type");
				$input_data ='<select name="'.$val['key'].'" id="'.$val['key'].'">';
				foreach($input_conf as $key_de => $val_de)
				{
					$input_data .='<option value="'.$key_de.'">'.$val_de.'</option>';
				}
				$input_data .='</select>';
				$seller['default_data'][$key]['data'] = $input_data;
			}
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_add.tpl.htm" );
        ///////////////////////
		$tpl->assign ( 'user_id', $_GET['user_id'] );
		$tpl->assign ( 'default_data', $seller['default_data'] );
	break;
    case "chsellerstatus"://修改商家状态
		$task_log_obj = POCO::singleton('pai_task_admin_log_class');
		$re = $mall_obj->change_seller_status((int)$_INPUT['id'],(int)$_INPUT['status']);
		$re['result'] == 1?$task_log_obj->add_log($yue_login_id,3003,1,$_INPUT,'',$_INPUT['id']):"";
		$re['message']=iconv('gbk','utf-8',$re['message']);
	    echo json_encode($re);
	    exit;
	break;
    case "chcompanystatus"://修改公司状态
		$task_log_obj = POCO::singleton('pai_task_admin_log_class');
		$re = $mall_obj->change_seller_company_status((int)$_INPUT['id'],(int)$_INPUT['status']);
		$re['result'] == 1?$task_log_obj->add_log($yue_login_id,3004,1,$_INPUT,'',$_INPUT['id']):"";
		$re['message']=iconv('gbk','utf-8',$re['message']);
	    echo json_encode($re);
	    exit;
	break;
    case "chstorestatus"://修改商店状态
		$task_log_obj = POCO::singleton('pai_task_admin_log_class');
		$re = $mall_obj->change_seller_store_status((int)$_INPUT['id'],(int)$_INPUT['status']);
		$re['result'] == 1?$task_log_obj->add_log($yue_login_id,3005,1,$_INPUT,'',$_INPUT['id']):"";
		$re['message']=iconv('gbk','utf-8',$re['message']);
	    echo json_encode($re);
	    exit;
	break;
    case "editseller"://修改商家资料
		$id = (int)$_INPUT['id'];
		if($_POST)
		{
			$data = $_INPUT;
			$data['add_user'] = $yue_login_id;
			$re = $mall_obj->update_seller($data['goods_id'],$data);
			echo "<script>alert('".$re['message']."');window.open('seller.php?action=editseller&id={$id}','_self')</script>";
			exit;
		}
		$seller = $mall_obj->get_seller_info($id);
		foreach($seller['default_data'] as $key => $val)
		{
			if($val['key'] == 'basic_type')
			{
				$input_conf = pai_mall_load_config("seller_type");
				$input_data ='<select name="'.$val['key'].'" id="'.$val['key'].'">';
				foreach($input_conf as $key_de => $val_de)
				{
					$input_data .='<option value="'.$key_de.'"';
					$input_data .=$val['value']==$key_de?' selected="selected"':'';
					$input_data .='>'.$val_de.'</option>';
				}
				$input_data .='</select>';
				$seller['default_data'][$key]['data'] = $input_data;
			}
		}
		//print_r($seller);
		if(!$seller)
		{
			echo "<script>alert('没有该商品信息');</script>";
			exit;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_edit.tpl.htm" );

		$tpl->assign ( 'id', $id);
		$tpl->assign ( 'default_data', $seller['default_data'] );
	break;
    case "editcompany"://修改公司资料
		$id = (int)$_INPUT['id'];
		if($_POST)
		{
			$data = $_INPUT;
			$data['add_user'] = $yue_login_id;
			$re = $mall_obj->update_seller_company($id,$data);
			echo "<script>alert('".$re['message']."');window.open('seller_search.php?action=editcompany&id={$id}','_self')</script>";
			exit;
		}
		$data = $mall_obj->get_seller_company_info($id);
		//print_r($data);
		if(!$data)
		{
			echo "<script>alert('没有该商品信息');</script>";
			exit;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."company_edit.tpl.htm" );

		$tpl->assign ( 'id', $id);
		$tpl->assign ( 'default_data', $data[0]['default_data'] );
	break;
    case "editprofile"://修改商家资料		
		$id = (int)$_INPUT['id'];
		if($_POST)
		{
			$seller_id = (int)$_INPUT['seller_id'];
			$data = $_INPUT;
			$img = $_INPUT['upload_imgs_0'];
			$img_data = array();
			foreach((array)$img as $val)
			{
				$img_data[] = array('img_url'=>$val);
			}
			$data['img'] = $img_data;
			//
/*			$data['user_id']=109650;
			$data['cacke_id']=12365478934;
			$mall_obj->set_seller_data_for_temp($data);
			$re = $mall_obj->show_seller_data_for_temp($data['cacke_id']);
			print_r($re);
			exit;
*/			//
			$re = $mall_obj->update_seller_profile($id,$data);
			echo "<script>alert('".$re['message']."');window.open('seller_search.php?action=editprofile&id={$seller_id}','_self')</script>";
			exit;
		}
		$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
	    $type_list = $goods_type_obj->get_type_cate(2);
		//print_r($type_list);
		$profile = $mall_obj->get_seller_profile($id,2);
        if(!$profile)
		{
			echo "<script>alert('没有该信息');</script>";
			exit;
		}
        foreach($profile[0]['default_data'] as $key => $val)
        {
            $profile[0]['default_data'][$key]['img'] = in_array($val['key'],array('avatar','cover'))?'<img src="'.$val['value'].'" width="100px"><br>':'';
            $val['key']=='location_id'?($profile[0]['default_data'][$key]['location_name'] = get_poco_location_name_by_location_id($val['value'])):"";
			if($val['key'] == 'sex')
			{
				$input_conf = pai_mall_load_config("sex");
				$input_data ='<select name="'.$val['key'].'" id="'.$val['key'].'">';
				foreach($input_conf as $key_de => $val_de)
				{
					$input_data .='<option value="'.$key_de.'"';
					$input_data .=$val['value']==$key_de?' selected="selected"':'';
					$input_data .='>'.$val_de.'</option>';
				}
				$input_data .='</select>';
				$profile[0]['default_data'][$key]['data'] = $input_data;
			}
        }
        ////////////////
        foreach($profile[0]['image_data']['value'] as $key => $value)
        {
            $profile[0]['image_data']['value'][$key]['data_mark'] = 0;
        }
        foreach($profile[0]['att_data'] as $key => $val)
        {
            if($val['child_data'])
			{
				$att_value_array = array();
				if($profile[0]['att_data'][$key]['input'] == 2)
				{
					$att_value_array = explode(',',$profile[0]['att_data'][$key]['value']);					
				}
				foreach($val['child_data'] as $val_de)
				{
					$att_value = array();
					$att_value['c_values'] = $val_de;
					if($att_value_array and in_array($val_de,$att_value_array))
					{
						$att_value['is_select'] = true;
					}
					$profile[0]['att_data'][$key]['child_data_format'][] = $att_value;
				}
			}
        }
		//print_r($profile);
        $img_list_count = count($profile['image_data']['value']);
        $fit_swf = false;//判定图片数是否满额
        if($img_list_count>=20)
        {
            $fit_swf =   true;
        }
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);

		$goods_type_id = explode(',',$profile[0]['type_id']);
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['checked'] = in_array($val['id'],$goods_type_id)?' checked="checked"':'';
		}
        
        if( ! empty($profile['0']['default_data']) )
        {
            foreach($profile['0']['default_data'] as $k => &$v)
            {
                if($v['key'] == 'avatar' || $v['key'] == 'cover')
                {
                    $v['big_value'] = yueyue_resize_act_img_url($v['value'], $size = '');
                }
                
            }
        }
        
        if( ! empty($profile['0']['image_data']))
        {
            foreach($profile['0']['image_data']['value'] as $k => &$v)
            {
                $v['big_img_url'] = yueyue_resize_act_img_url($v['img_url'], $size = '');
            }
        }
		//print_r($profile[0]['att_data']);
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."profile_edit.tpl.htm" );
        $tpl->assign ( 'global_header_html', $global_header_html );
		$tpl->assign ( 'id', $profile[0]['seller_profile_id']);
		$tpl->assign ( 'seller_id', $profile[0]['seller_id']);
		$tpl->assign ( 'type_list', $type_list);
		$tpl->assign ( 'att_data', $profile[0]['att_data'] );
		$tpl->assign ( 'default_data', $profile[0]['default_data'] );
        $tpl->assign ( 'image_data', $profile[0]['image_data'] );
        $tpl->assign ( 'add_time', date('Y-m-d H:i:s',$profile[0]['add_time']) );
	break;	
    case "editstore"://修改商家店铺
		$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
	    $type_list = $goods_type_obj->get_type_cate(2);
		//print_r($type_list);
		
		$id = (int)$_INPUT['id'];
		if($_POST)
		{
			$data = $_INPUT;
			//exit;
			$re = $mall_obj->update_store($id,$data);
			echo "<script>alert('".$re['message']."');window.open('seller_search.php?action=editstore&id={$id}','_self')</script>";
			exit;
		}
		$store_info= $mall_obj->get_store_info($id);
		//print_r($store_info);
		if(!$store_info)
		{
			echo "<script>alert('没有该店铺信息');</script>";
			exit;
		}
		$goods_type_id = explode(',',$store_info[0]['type_id']);
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['checked'] = in_array($val['id'],$goods_type_id)?' checked="checked"':'';
		}
		//print_r($store_info[0]['att_data']);
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."store_edit.tpl.htm" );

		$tpl->assign ( 'id', $store_info[0]['store_id']);
		$tpl->assign ( 'seller_id', $store_info[0]['seller_id']);
		$tpl->assign ( 'type_list', $type_list);
		$tpl->assign ( 'default_data', $store_info[0]['default_data'] );
	break;
    case "companylist"://公司列表
		$seller_id = (int)$_INPUT ['id'];
		$status = (int)$_INPUT ['status'];
		$seller_status = pai_mall_load_config("seller_company_status");
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."company_list.tpl.htm" );
		$seller_status_name[]=array(
			                              'key' => 0,
										  'name' => "全部",
										  'selected' => $status===0?1:0,
										  );
		foreach($seller_status as $key => $val)
		{
			$seller_status_name[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?1:0,
										  );
		}
		$where = "seller_id='{$seller_id}'";		
		$where .= $status?" AND status = '{$status}'":"";
		//echo $where;
		$total_count = $mall_obj->get_seller_company_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,"id" => $seller_id) );
		$page_obj->set ( $show_count, $total_count );		
		$list = $mall_obj->get_seller_company_list(false, $where, "", $page_obj->limit());
		$seller = $mall_obj->get_seller_info($id);
		//exit;
		//print_r($service_name);
		foreach($list as $key => $val)
		{
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['status_name'] = $seller_status[$val['status']];
		}		
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'id', $seller_id );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'service_id', $service_id );
		$tpl->assign ( 'company_num', $seller['seller_data']['company_num']);
		$tpl->assign ( 'seller_status_name', $seller_status_name );		
	break;
    case "storelist"://商铺列表
		$company_id = (int)$_INPUT ['id'];
		$status = (int)$_INPUT ['status'];
		$seller_status = pai_mall_load_config("seller_store_status");
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."store_list.tpl.htm" );
		$status_name[]=array(
							  'key' => 0,
							  'name' => "全部",
							  'selected' => $status===0?1:0,
							  );
		foreach($seller_status as $key => $val)
		{
			$status_name[] = array(
								  'key' => $key,
								  'name' => $val,
								  'selected' => $status===$key?1:0,
								  );
		}
		$goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$goods_type = $goods_type_obj->get_type_cate(0);
		foreach($goods_type as $val)
		{
			$goods_type_name[$val['id']]=$val;
		}

		$where = "company_id='{$company_id}'";		
		$where .= $status?" AND status = '{$status}'":"";
		$total_count = $mall_obj->get_seller_store_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,"id" => $seller_id) );
		$page_obj->set ( $show_count, $total_count );
		$list = $mall_obj->get_seller_store_list(false, $where, "", $page_obj->limit());
		foreach($list as $key => $val)
		{
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['status_name'] = $seller_status[$val['status']];
			$type_name = array();
			$store_type = explode(',',$val['type_id']);
			foreach($store_type as $val)
			{
				$type_name[] = $goods_type_name[$val]['name'];
			}
			$list[$key]['type_name'] = implode(' | ',$type_name);
		}		
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'id', $company_id );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'status_name', $status_name );		
	break;
    case "seller_list": // 原有的seller.php list 逻辑
		$status = (int)$_INPUT ['status'];
        $keyword = $_INPUT ['keyword'];
        $org_user_id = (int)$_INPUT['org_user_id'];
        $basic_type = $_INPUT['basic_type'];
		
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_list.tpl.htm" );
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
        $seller_status = pai_mall_load_config("seller_status");
		$seller_status_name[]=array(
			                              'key' => 0,
										  'name' => "全部",
										  'selected' => $status===0?1:0,
										  );
		foreach($seller_status as $key => $val)
		{
			$seller_status_name[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?1:0,
										  );
		}
		//经验年限的数组设置
        $years_config = pai_mall_load_config('certificate_common_years');
        $years_name[] = array(
            'key'=>-1,
            'name'=>'全部',
			'selected'=>$years ===-1 ? 1 : 0,
        );
		foreach($years_config as $key => $val)
		{
			$years_name[] = array(
				'key'=>$key,
				'name'=>$val,
				'selected'=>$years === $key ? 1 : 0,
			);
		}
		
		$where = 1;		
		$where .= $status?" AND status = '{$status}'":"";
		
        if($basic_type)
        {
            $where .= " and basic_type='{$basic_type}' ";
        }
        
		if($begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND add_time >= {$begin_time_s} AND add_time <= {$end_time_s}";
		}
		
		$where .= $keyword?" AND (user_id ='".(int)$keyword."' "
                . "or name like  '%".pai_mall_change_str_in($keyword)."%' ". " or user_name like  '%".pai_mall_change_str_in($keyword)."%' )" : '';
        
        $org_obj = POCO::singleton('pai_organization_class');
        $org_list = $org_obj->get_org_list($b_select_count = false, $where_str = 'status = 1', $order_by = 'id DESC', $limit = '0,200', $fields = '*');
        if($org_user_id > 0)
        {
            //如果有机构的用户id,其它条件都不要
            
            $relate_org_obj = POCO::singleton('pai_model_relate_org_class');
            $where_str ="org_id='{$org_user_id}'";
            $user_list =  $relate_org_obj->get_model_org_list_by_org_id($b_select_count = false,$where_str, $limit = '0,200', $order_by = 'id DESC',  $fields = 'user_id');
            
            
            $user_where = '';
            if( ! empty($user_list) )
            {
                foreach($user_list as $k => $v)
                {
                    $v['user_id'] = "'".$v['user_id']."'";
                    $user_where .=$v['user_id'].",";
                }
            }
            
            if( ! empty($user_where) )
            {
                //去除最后一个逗号
                $user_where =  substr($user_where,0,-1);
                
                $where = " 1 AND user_id in ($user_where)";
            }
            
            foreach($org_list as $k => &$v)
            {
                if($v['user_id'] == $org_user_id)
                {
                    $v['selected'] = 1;
                    break;
                }
            }
            
        }
        
		$total_count = $mall_obj->get_seller_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,"begin_time" => $begin_time,"end_time" => $end_time,"keyword" => $keyword,"org_user_id"=>$org_user_id,'action'=>'seller_list') );
		$page_obj->set ( $show_count, $total_count );		
		$list = $mall_obj->get_seller_list(false, $where, "seller_id DESC", $page_obj->limit());
		//print_r($list);
		//exit;
		//print_r($service_name);
		foreach($list as $key => $val)
		{
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['status_name'] = $seller_status[$val['status']];
			$list[$key]['user_name'] = $val['add_user'] ? get_user_nickname_by_user_id($val['add_user']) : '批量';
			$list[$key]['location_name'] = $val['location_id']?get_poco_location_name_by_location_id($val['location_id']):"全国";
			$list[$key]['store_id'] = $mall_obj->get_first_store_id_by_user_id($val['user_id']);
            
            
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
        
        $tpl->assign('org_list',$org_list);
        $tpl->assign('basic_type',$basic_type);
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'keyword', $keyword );		
		$tpl->assign ( 'seller_status_name', $seller_status_name );	
        $tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
		$tpl->assign ( 'admin_ac', in_array($yue_login_id,array(109650))?true:false );
	break;
    case "seller_export": //原有的seller.php export的逻辑
	    set_time_limit(0);
		$status = (int)$_INPUT ['status'];
		$keyword = $_INPUT ['keyword'];
        $org_user_id = (int)$_INPUT['org_user_id'];
		$seller_status = pai_mall_load_config("seller_status");
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_list.tpl.htm" );
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
		$seller_status_name[]=array(
			                              'key' => 0,
										  'name' => "全部",
										  'selected' => $status===0?1:0,
										  );
		foreach($seller_status as $key => $val)
		{
			$seller_status_name[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?1:0,
										  );
		}
		$where = 1;		
		$where .= $status?" AND status = '{$status}'":"";
		if($begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND add_time >= {$begin_time_s} AND add_time <= {$end_time_s}";
		}
		$where .= $keyword?" AND (user_id ='".(int)$keyword."' or name like  '%".pai_mall_change_str_in($keyword)."%')":"";
        
        $org_obj = POCO::singleton('pai_organization_class');
        $org_list = $org_obj->get_org_list($b_select_count = false, $where_str = 'status = 1', $order_by = 'id DESC', $limit = '0,200', $fields = '*');
        if($org_user_id > 0)
        {
            //如果有机构的用户id,其它条件都不要
            
            $relate_org_obj = POCO::singleton('pai_model_relate_org_class');
            $where_str ="org_id='{$org_user_id}'";
            $user_list =  $relate_org_obj->get_model_org_list_by_org_id($b_select_count = false,$where_str, $limit = '0,200', $order_by = 'id DESC',  $fields = 'user_id');
            
            $user_where = '';
            if( ! empty($user_list) )
            {
                foreach($user_list as $k => $v)
                {
                    $v['user_id'] = "'".$v['user_id']."'";
                    $user_where .=$v['user_id'].",";
                }
            }
            
            if( ! empty($user_where) )
            {
                //去除最后一个逗号
                $user_where =  substr($user_where,0,-1);
                
                $where = " 1 AND user_id in ($user_where)";
            }
            
            
        }
        
		$list = $mall_obj->get_seller_list(false, $where, "seller_id DESC","0,10000");
		$user_obj = POCO::singleton('pai_user_class');
		foreach($list as $key => $val)
		{
			$val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$val['status_name'] = $seller_status[$val['status']];
			$val['user_name'] = get_user_nickname_by_user_id($val['add_user']);
			$val['phone'] = $user_obj-> get_phone_by_user_id($val['user_id']);
			$val['location_name'] = $val['location_id']?get_poco_location_name_by_location_id($val['location_id']):"全国";
			
			$seller_info = $mall_obj->get_seller_search_other_data($val['seller_id']);
			$store_type = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
			$type_name = array();
			$service_belong_31 = '';
			foreach($store_type as $val_de)
			{
				$service_belong_id = "service_belong_".$val_de;
                $$service_belong_id = '';
				$$service_belong_id = get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de]);
			}
			$val['type_name'] = implode('<br>',$type_name);
			$val['service_belong_31'] = $service_belong_31;
			$offline_num = $val['goods_num']-$val['onsale_num'];
			
			$ex_val = array(
							$val['user_id'],
							$val['name'],
							$val['phone'],
							$val['location_name'],
							$val['add_time'],
							$val['onsale_num'],
							$offline_num,
							$val['goods_num'],
							$val['status_name'],
							$val['service_belong_31'],
							);
			$output_data[] = $ex_val;
		}
		
		$headArr =array(
						'商家用户ID',
					    '商家名称',
					    '电话',
					    '城市',
					    '审核时间',
					    '上架',
					    '未上架',
					    '商品总数',
					    '状态',
					    '管理员',
						);
		$select_name = "商家";
		getExcel($select_name, $headArr, $output_data,$select_name.'列表');
        //Excel_v2::start($headArr,$output_data,$select_name,'s3');
		exit;
	break;
	case "export":
	    set_time_limit(0);
		$page = (int)$_INPUT['page'];
		$type_id = (int)$_INPUT['type_id'];
		$status = (int)$_INPUT ['status'];
		$keyword = $_INPUT ['keyword'];
		$seller_status = pai_mall_load_config("seller_status");
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
        
        //数据组装
        if( ! empty($_INPUT['detail']) )
        {
            foreach($_INPUT['detail'] as $k => $v)
            {
                $_INPUT[$k] = $v;
            }
            unset($_INPUT['detail']);
        }
        
        $task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		
        $seller_status = pai_mall_load_config("seller_status");
        
		$goods_type = $task_goods_type_obj->get_type_cate(0);
		foreach($goods_type as $val)
		{
			$goods_type_name[$val['id']]=$val;
		}
        
		$show_count = 1000;
		$offect = ($page-1)*$show_count;
		$limit = "{$offect},{$show_count}";
        
		$list = $mall_obj->search_seller_list_by_fulltext($_INPUT,$limit);
        $user_obj = POCO::singleton('pai_user_class');
		foreach($list['data'] as $key => &$val)
		{
			$val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$val['status_name'] = $seller_status[$val['seller_status']];
			$val['phone'] = $user_obj-> get_phone_by_user_id($val['user_id']);
			$val['user_name'] = $val['seller_add_user']?get_user_nickname_by_user_id($val['seller_add_user']):"批量";
			$val['location_name'] = $val['location_id']?get_poco_location_name_by_location_id(str_replace(',','',$val['location_id'])):"全国";
            
            $seller_info = $mall_obj->get_seller_info($val['seller_id']);
            
            $val['service_belong'] = '无';
            
            $store_type = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
			$type_name = array();
			foreach($store_type as $val_de)
			{
				$t_name = $goods_type_name[$val_de]['name'];
				$att_name = '';
				switch($val_de)
				{
					case 5:
						$att_name = "老师类型:".$seller_info['seller_data']['profile'][0]['att_data_format']['t_teacher']['value']." 培训经验:".$seller_info['seller_data']['profile'][0]['att_data_format']['t_experience']['value'];
					break;
					case 31:
                        $val['service_belong'] = get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de]) ? get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de]) : '无';
						$att_name = "身高:".$seller_info['seller_data']['profile'][0]['att_data_format']['m_height']['value']." 罩杯:".$seller_info['seller_data']['profile'][0]['att_data_format']['m_cups']['value'].$seller_info['seller_data']['profile'][0]['att_data_format']['m_cup']['value'];
					break;
					case 40:
						$att_name = "从业年限:".$seller_info['seller_data']['profile'][0]['att_data_format']['p_experience']['value']." 擅长类型:".$seller_info['seller_data']['profile'][0]['att_data_format']['p_goodat']['value'];
					break;
				}
				$type_name[$val_de] = array(
                    'type_id'=>$val_de,
                    'type_name'=>$t_name.($att_name?"( {$att_name} )":"")
                        
                );
			}
            
            //商家评级
            $open_type_id = array();
            $rating_ary_out = array();
            $seller_rating_config = pai_mall_load_config('seller_rating');
            $val['rating'] = $mall_obj->get_seller_rating($val['user_id']);
            $open_type_id = $service_obj->get_service_status_by_user_id($val['user_id'],false);
            $val['rating_show'] = '';
            $rating_ary_out = $is_rating_type_id = array();
            if( ! empty($val['rating']) )
            {
                $rating_ary_out = explode(",",$val['rating']);
            }
            if( ! empty($rating_ary_out) )
            {
                foreach($rating_ary_out as $ork => $orv)
                {
                    $rvo_ary = explode("-",$orv);
                    $is_rating_type_id[] = $rvo_ary['0'];
                    $type_info = $goods_type_obj->get_type_info($rvo_ary['0']);
                    $val['rating_show'].= $type_info['name'].":".$seller_rating_config[$type_info['id']][$rvo_ary['1']]['text']."</br>";
                }
                foreach($open_type_id as $ook => $oov)
                {
                     if($oov['status'] == 1)
                     {
                        $type_info = $goods_type_obj->get_type_info($oov['type_id']);
                        if( ! empty($type_info) )
                        {
                           if( ! in_array($type_info['id'],$is_rating_type_id) )
                           {
                               $val['rating_show'].= $type_info['name'].":".'未评级'."</br>";
                           }
                        }
                     }   
                     
                }
               
            }else
            {
                
                foreach($open_type_id as $ok => $ov)
                {
                    if($ov['status'] == 1)
                    {
                        $type_info = $goods_type_obj->get_type_info($ov['type_id']);
                        $val['rating_show'].=$type_info['name'].":未评级</br>";
                    }
                }
                if(empty($list[$key]['rating_show']))
                {
                    $val['rating_show'] = '--';
                }
                
                
            }
            
            
			$ex_val = array(
                $val['user_id'],
                $val['name'],
                $val['phone'],
                $val['total_overall_score'],
                $val['seller_bill_finish_num'],
                $val['prices'],
                $val['location_name'],
                $val['user_name'],
                $val['add_time'],
                $val['status_name'],
                $val['onsale_num'],
				$val['goods_num'],
                $val['service_belong'],
                ! empty($type_name[3]) ? $type_name[3]['type_name'] : '',
                ! empty($type_name[5]) ? $type_name[5]['type_name'] : '',
                ! empty($type_name[12]) ? $type_name[12]['type_name'] : '',
                ! empty($type_name[31]) ? $type_name[31]['type_name'] : '',
                ! empty($type_name[40]) ? $type_name[40]['type_name'] : '',
                $val['seller_is_black'] == 1 ? '[ 屏蔽显示 ]':'否',
                str_replace("</br>",",",$val['rating_show']),
            );
            
			$output_data[] = $ex_val;
		}

        
        $headArr =array(
            '商家用户ID',
            '商家名称',
            '电话',
            '综合评分',
            '总交易次数',
            '总交易金额',
            '地区',
            '审核人',
            '审核时间',
            '状态',
            '上架',
            '商品总数',
            '模特跟进人',
            '化妆服务',
            '摄影培训服务',
            '影棚租赁服务',
            '模特服务',
            '摄影服务',
            '是否屏蔽显示',
            '商家评级',
        );
        $select_name = "商家";
		getExcel($select_name, $headArr, $output_data,$select_name.'列表');
        //Excel_v2::start($headArr,$output_data,$select_name,'s3');
		exit;
	break;
	default:
		$p = $_INPUT['p'] ? (int)$_INPUT['p'] : 1;
        $status = (int)$_INPUT ['status'];
        $keywords = $_INPUT ['keywords'];
        $type_id = (int)$_INPUT['type_id'];
        $basic_type = $_INPUT['basic_type'];
        
        //数据组装
        if( ! empty($_INPUT['detail']) )
        {
            foreach($_INPUT['detail'] as $k => $v)
            {
                $_INPUT[$k] = $v;
            }
            unset($_INPUT['detail']);
        }
        
        $ms_experience_config = pai_mall_load_config("certificate_diet_years");
        $ms_experience_list = config_data_format($ms_experience_config, $_INPUT['ms_experience'],'val');
        
        $ms_forwarding_config = pai_mall_load_config("certificate_diet_max_forward");
        $ms_forwarding_list = config_data_format($ms_forwarding_config, $_INPUT['ms_forwarding'],'val');
        
        $ms_certification_config = pai_mall_load_config("certificate_diet_identification");
        $ms_certification_list = config_data_format($ms_certification_config, $_INPUT['ms_certification'],'val');
        
        $p_team_config = pai_mall_load_config("certificate_cameror_team");
        $p_team_list = config_data_format($p_team_config, $_INPUT['p_team'],'val');
        
        $p_experience_config = pai_mall_load_config("certificate_common_years");
        $p_experience_list = config_data_format($p_experience_config, $_INPUT['p_experience'],'val');
        
        $p_order_income_config = pai_mall_load_config("certificate_cameror_order_income");
        $p_order_income_list = config_data_format($p_order_income_config, $_INPUT['p_order_income'],'val');
        
        $t_teacher_config = pai_mall_load_config("certificate_teacher_type");
        $t_teacher_list = config_data_format($t_teacher_config, $_INPUT['t_teacher'],'val');
        
        $t_experience_config = pai_mall_load_config("certificate_teacher_years");
        $t_experience_list = config_data_format($t_experience_config, $_INPUT['t_experience'],'val');
        
        $ot_label_config = pai_mall_load_config("certificate_other_identifine_label");
        $ot_label_list = config_data_format($ot_label_config, $_INPUT['ot_label'],'val');
        
        $yp_area_config = pai_mall_load_config("certificate_studio_area");
        $yp_area_list = config_data_format($yp_area_config, $_INPUT['yp_area'],'val');
        
        $yp_background_config = pai_mall_load_config("certificate_studio_can_photo");
        $yp_background_list = config_data_format($yp_background_config, $_INPUT['yp_background'],'val');
        
        $yp_can_photo_config = pai_mall_load_config("certificate_studio_photo_type");
        $yp_can_photo_list = config_data_format($yp_can_photo_config, $_INPUT['yp_can_photo'],'val');
        
        $yp_lighter_config = pai_mall_load_config("certificate_studio_lighter");
        $yp_lighter_list = config_data_format($yp_lighter_config, $_INPUT['yp_lighter'],'val');
        
        $yp_other_equitment_config = pai_mall_load_config("certificate_studio_other");
        $yp_other_equitment_list = config_data_format($yp_other_equitment_config, $_INPUT['yp_other_equitment'],'val');
        
        $hz_order_way_config = pai_mall_load_config("certificate_dresser_order_way");
        $hz_order_way_list = config_data_format($hz_order_way_config, $_INPUT['hz_order_way'],'val');
        
        $hz_team_config = pai_mall_load_config("certificate_dresser_team_num");
        $hz_team_list = config_data_format($hz_team_config, $_INPUT['hz_team'],'val');
        
        $hz_place_config = pai_mall_load_config("certificate_dresser_has_place");
        $hz_place_list = config_data_format($hz_place_config, $_INPUT['hz_place'],'val');
        
        $hz_experience_config = pai_mall_load_config("certificate_common_years");
        $hz_experience_list = config_data_format($hz_experience_config, $_INPUT['hz_experience'],'val');
        
        $hz_goodat_config = pai_mall_load_config("certificate_dresser_do_well");
        $hz_goodat_list = config_data_format($hz_goodat_config, $_INPUT['hz_goodat'],'val');
        
        $seller_rating_config = pai_mall_load_config('seller_rating');
        $type_id_rating_config = $seller_rating_config[$type_id];
        
        if( isset($_INPUT['rating']) )
        {
            if($_INPUT['rating'] !=='')
            {
                foreach($type_id_rating_config as $k => $v)
                {
                    if($v['value'] == $_INPUT['rating'])
                    {
                        $type_id_rating_config[$k]['selected'] = 1;
                    }
                }
            }
        }
        
       
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_list_search.tpl.htm" );
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
        
        
        $task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		
		$goods_type = $task_goods_type_obj->get_type_cate(0);
		foreach($goods_type as $val)
		{
			$goods_type_name[$val['id']]=$val;
		}
        
        $seller_status = pai_mall_load_config("seller_status");
		$seller_status_name[]=array(
			                              'key' => 0,
										  'name' => "全部",
										  'selected' => $status===0?1:0,
										  );
		foreach($seller_status as $key => $val)
		{
			$seller_status_name[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?1:0,
										  );
		}
		
        $show_count = 20;
		$offect = ($p-1)*$show_count;
		$limit = "{$offect},{$show_count}";
        if($_set_debug)
		{
			$_INPUT['debug']=true;
		}
        
        $page_obj = new show_page ();
        
		$seller_data = $mall_obj->search_seller_list_by_fulltext($_INPUT,$limit);
        $total_count=$seller_data['total'];
		
		unset($_INPUT['p']);
		unset($_INPUT['IP_ADDRESS']);
		unset($_INPUT['IP_ADDRESS1']);
		unset($_INPUT['request_method']);
		unset($_INPUT['s']);
		unset($_INPUT['page']);
		unset($_INPUT['action']);
		foreach($_INPUT as $key => $val)
		{
			if($val === "")
			{
				unset($_INPUT[$key]);
			}
		}
		//exit;
		$_INPUT['s_action'] = "seller";
		$post_data = mall_query_str($_INPUT);
		
		$page_obj->setvar ($_INPUT);
		$page_obj->set ( $show_count, $total_count );		
		$list = $seller_data['data'];
		//exit;
		//print_r($service_name);
		foreach($list as $key => $val)
		{
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['seller_add_time']);
			$list[$key]['status_name'] = $seller_status[$val['seller_status']];
			$list[$key]['user_name'] = $val['seller_add_user']?get_user_nickname_by_user_id($val['seller_add_user']):"批量";
			$list[$key]['location_name'] = $val['location_id']?get_poco_location_name_by_location_id(str_replace(',','',$val['location_id'])):"全国";
			$seller_info = $mall_obj->get_seller_search_other_data($val['seller_id']);
			$store_type = explode(',',$seller_info['seller_data']['profile'][0]['type_id']);
			$type_name = array();
			foreach($store_type as $val_de)
			{
                $t_name = $goods_type_name[$val_de]['name'];
				$att_name = '';
				switch($val_de)
				{
					case 5:
						$att_name = "老师类型:".$seller_info['seller_data']['profile'][0]['att_data_format']['t_teacher']['value']." 培训经验:".$seller_info['seller_data']['profile'][0]['att_data_format']['t_experience']['value'];
					break;
					case 31:
						$att_name = "身高:".$seller_info['seller_data']['profile'][0]['att_data_format']['m_height']['value']." 罩杯:".$seller_info['seller_data']['profile'][0]['att_data_format']['m_cups']['value'].$seller_info['seller_data']['profile'][0]['att_data_format']['m_cup']['value'];
					break;
					case 40:
						$att_name = "从业年限:".$seller_info['seller_data']['profile'][0]['att_data_format']['p_experience']['value']." 擅长类型:".$seller_info['seller_data']['profile'][0]['att_data_format']['p_goodat']['value'];
					break;
					case 41:
						$att_name = "从业年限:".$seller_info['seller_data']['profile'][0]['att_data_format']['ms_experience']['value']." 身份认证:".$seller_info['seller_data']['profile'][0]['att_data_format']['ms_certification']['value']." 转发量:".$seller_info['seller_data']['profile'][0]['att_data_format']['ms_forwarding']['value'];
					break;
					case 43:
						$att_name = "标签:".$seller_info['seller_data']['profile'][0]['att_data_format']['ot_label']['value'];
					break;
				}
				$type_name[] = $t_name.($seller_info['seller_data']['service_belong'][$val_de]?"[".get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de])."]":"").($att_name?"<br>( {$att_name} )":"");
			}
			$list[$key]['type_name'] = implode('<br>',$type_name);
			$list[$key]['store_id'] = $mall_obj->get_first_store_id_by_user_id($val['user_id']);
            $list[$key]['seller_url'] = urlencode("seller_search.php?type_id={$type_id}&status=1");
            $list[$key]['type_id'] = $type_id;
            
            $val['rating'] = $mall_obj->get_seller_rating($val['user_id']);
            $open_type_id = array();
            $rating_ary_out = array();
            $open_type_id = $service_obj->get_service_status_by_user_id($val['user_id'],false);
            $list[$key]['rating_show'] = '';
            $rating_ary_out = $is_rating_type_id = array();
            if( ! empty($val['rating']) )
            {
                $rating_ary_out = explode(",",$val['rating']);
            }
            if( ! empty($rating_ary_out) )
            {
                foreach($rating_ary_out as $ork => $orv)
                {
                    $rvo_ary = explode("-",$orv);
                    $is_rating_type_id[] = $rvo_ary['0'];
                    $type_info = $goods_type_obj->get_type_info($rvo_ary['0']);
                    $list[$key]['rating_show'].= $type_info['name'].":".$seller_rating_config[$type_info['id']][$rvo_ary['1']]['text']."</br>";
                }
                foreach($open_type_id as $ook => $oov)
                {
                    //开通了的服务
                    if($oov['status'] == 1)
                    {
                        $type_info = $goods_type_obj->get_type_info($oov['type_id']);
                        if( ! empty($type_info) )
                        {
                           if( ! in_array($type_info['id'],$is_rating_type_id) )
                           {
                               $list[$key]['rating_show'].= $type_info['name'].":".'未评级'."</br>";
                           }
                        }
                    }
                     
                }
               
            }else
            {
                
                foreach($open_type_id as $ok => $ov)
                {
                    if($ov['status'] == 1)
                    {
                        $type_info = $goods_type_obj->get_type_info($ov['type_id']);
                        $list[$key]['rating_show'].=$type_info['name'].":未评级</br>";
                    }
                }
                if(empty($list[$key]['rating_show']))
                {
                    $list[$key]['rating_show'] = '--';
                }
                
                
            }
            
            if($type_id)
            {
                if(preg_match("/$type_id/",$val['rating']))
                {
                    $rating_ary = explode(",",$val['rating']);
                    foreach($rating_ary as $rk => $rv)
                    {
                        if(preg_match("/$type_id/",$rv))
                        {
                            $rv_ary = explode("-",$rv);
                            $list[$key]['seller_rating'] = $rv_ary['1'];
                            break;
                        }    
                    }
                }
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
        
        //print_r($list);
        $tpl->assign('type_id',$type_id);
      
        $tpl->assign('org_list',$org_list);
        $tpl->assign('add_time_s',$_INPUT['add_time_s']);
        $tpl->assign('add_time_e',$_INPUT['add_time_e']);
		$tpl->assign('city',$_INPUT['city']);
		$tpl->assign('location_id',$_INPUT['location_id']);
        
        
		$tpl->assign('m_height',$_INPUT['m_height']);
		$tpl->assign('m_cups',$_INPUT['m_cups']);
		$tpl->assign('m_cup',$_INPUT['m_cup']);
		$tpl->assign('m_sex',$_INPUT['m_sex']);
        
		$tpl->assign('p_goodat',$_INPUT['p_goodat']);
		
        $tpl->assign ('post_dataformat',$post_data);
        
		$tpl->assign('level',$_INPUT['level']);
		$tpl->assign('total_point',$_INPUT['total_point']);
		$tpl->assign('total_times_s',$_INPUT['total_times_s']);
		$tpl->assign('total_times_e',$_INPUT['total_times_e']);
		$tpl->assign('total_money_s',$_INPUT['total_money_s']);
		$tpl->assign('total_money_e',$_INPUT['total_money_e']);
        $tpl->assign('price_s',$_INPUT['price_s']);
        $tpl->assign('price_e',$_INPUT['price_e']);
        $tpl->assign('list_s',$_INPUT['list_s']);
        $tpl->assign('list_e',$_INPUT['list_e']);
        $tpl->assign('operator_id',$_INPUT['operator_id']);
        
        $tpl->assign('ms_experience_list',$ms_experience_list);
        $tpl->assign('ms_forwarding_list',$ms_forwarding_list);
        $tpl->assign('ms_certification_list',$ms_certification_list);
        $tpl->assign('p_team_list',$p_team_list);
        $tpl->assign('p_experience_list',$p_experience_list);
        $tpl->assign('p_order_income_list',$p_order_income_list);
        $tpl->assign('t_teacher_list',$t_teacher_list);
        $tpl->assign('t_experience_list',$t_experience_list);
        $tpl->assign('ot_label_list',$ot_label_list);
        $tpl->assign('yp_area_list',$yp_area_list);
        $tpl->assign('yp_background_list',$yp_background_list);
        $tpl->assign('yp_can_photo_list',$yp_can_photo_list );
        $tpl->assign('yp_lighter_list',$yp_lighter_list );
        $tpl->assign('yp_other_equitment_list',$yp_other_equitment_list );
        $tpl->assign('hz_order_way_list',$hz_order_way_list );
        $tpl->assign('hz_place_list',$hz_place_list );
        $tpl->assign('hz_team_list',$hz_team_list );
        $tpl->assign('hz_experience_list',$hz_experience_list );
        $tpl->assign('hz_goodat_list',$hz_goodat_list );
        $tpl->assign('type_id_rating_config',$type_id_rating_config );
		
		$tpl->assign ( 'admin_ac', in_array($yue_login_id,array(109650))?true:false );     
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
        $tpl->assign ( 'basic_type',$basic_type);
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'keywords', $keywords );		
		$tpl->assign ( 'seller_status_name', $seller_status_name );	
        $tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
	break;
}
$tpl->output ();
?>