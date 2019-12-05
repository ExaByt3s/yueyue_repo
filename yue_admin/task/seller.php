<?php
ini_set('memory_limit','512M');
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';
$mall_obj = POCO::singleton('pai_mall_seller_class');
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
			echo "<script>alert('{$message}');window.open('seller.php?action=addstore&id=".$data['company_id']."','_self')</script>";
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
			echo "<script>alert('{$message}');window.open('seller.php?action=addcompany&id={$seller_id}','_self')</script>";
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
			echo "<script>alert('".$re['message']."');window.open('seller.php?action=editcompany&id={$id}','_self')</script>";
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
			echo "<script>alert('".$re['message']."');window.open('seller.php?action=editprofile&id={$seller_id}','_self')</script>";
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
			echo "<script>alert('".$re['message']."');window.open('seller.php?action=editstore&id={$id}','_self')</script>";
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
	case "export":
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
	default:
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
		$page_obj->setvar ( array ("status" => $status,"begin_time" => $begin_time,"end_time" => $end_time,"keyword" => $keyword,"org_user_id"=>$org_user_id) );
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
}
$tpl->output ();
?>