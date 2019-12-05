<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
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
	$task_goods_obj->set_debug();
}
$status_name = pai_mall_load_config('goods_status');
switch($action)
{
	case "add":
		$store_id = (int)$_INPUT['store_id'];
		$type_id = (int)$_INPUT['type_id'];
		if(!($store_id and $type_id))
		{
			echo "<script>alert('参数错误');</script>";
			exit;
		}
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
			$data['img'] = $img_data;
			$in = $task_goods_obj->add_goods($data);
			$mess = '添加失败';
			if($in['result'] > 0)
			{
				$task_goods_obj->change_goods_status($in['result'],1);
				$mess = '添加成功';
			}
			echo "<script>alert('{$mess}');window.close();</script>";
			exit;
		}		
		$goods = $task_goods_obj->show_goods_data($type_id);
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_add.tpl.htm" );
        ///////////////////////
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
        $tpl->assign ( 'global_header_html', $global_header_html );
        ///////////////////////
		$tpl->assign ( 'store_id', $store_id );
		$tpl->assign ( 'type_id', $type_id );
		$tpl->assign ( 'default_data', $goods['default_data'] );
		$tpl->assign ( 'system_data', $goods['system_data'] );
		$tpl->assign ( 'diy_data', $goods['diy_data'] );
		$tpl->assign ( 'prices_data', $goods['prices_data'] );
		$tpl->assign ( 'prices_data_list', $goods['prices_data_list'] );
	    $tpl->assign ( 'image_data', $goods['image_data'] );
	break;
	case "chstatus"://审核
	    $id = (int)$_INPUT['id'];
	    $status = (int)$_INPUT['status'];
		if($_GET['note']!='show')
		{
			$note = iconv('utf-8','gbk',$_INPUT['note']);
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			$re = $task_goods_obj->change_goods_status($id,$status);
			$re['result'] == 1?$task_log_obj->add_log($yue_login_id,2001,2,$_INPUT,$note,$id):"";
			$re['message']=iconv('gbk','utf-8',$re['message']);
			echo json_encode($re);
			exit;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_change_status.tpl.htm" );
        $tpl->assign('id', $id );
        $tpl->assign('status',$status);
        $tpl->assign('type','status');
	break;
	case "chshow"://上下架
	    $id = (int)$_INPUT['id'];
	    $status = (int)$_INPUT['status'];
		if($_GET['note']!='show')
		{
			$note = iconv('utf-8','gbk',$_INPUT['note']);
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			$re = $task_goods_obj->change_goods_show_status($id,$status);
			$re['result'] == 1?$task_log_obj->add_log($yue_login_id,2002,2,$_INPUT,$note,$id):"";
			$re['message']=iconv('gbk','utf-8',$re['message']);
			echo json_encode($re);
			exit;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_change_status.tpl.htm" );
        $tpl->assign('id', $id );
        $tpl->assign('status',$status);
        $tpl->assign('type','show');
	break;
	case "edit":
		$id = (int)$_INPUT['id'];
		$goods = $task_goods_obj->get_goods_info($id);
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
			$data['img'] = $img_data;
			//print_r($data);
			$re = $task_goods_obj->update_goods($data['goods_id'],$data);
			if($re['result'] == 1)
			{
				////////////////////////////////////临时处理状态,上下架
				if($goods['goods_data']['status'] == 1)
				{
					$return = $task_goods_obj->change_goods_status($id,1);
				}
				elseif($goods['goods_data']['status'] == 2)
				{
					$return = $task_goods_obj->change_goods_status($id,0);
				}
				////////////////////////////////////				
			}
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			$task_log_obj->add_log($yue_login_id,2004,2,$_INPUT,'',$id);
			echo "<script>alert('".$re['message']."');window.open('?action=edit&id={$id}','_self')</script>";
			exit;
		}		
		if(!$goods)
		{
			echo "<script>alert('没有该商品信息');window.open('goods.php','_self')</script>";
			exit;
		}
        ///////////////////////
        foreach($goods['image_data']['value'] as $key => $value)
        {
            $goods['image_data']['value'][$key]['data_mark'] = 0;
        }
        $img_list_count = count($goods['image_data']['value']);
        $fit_swf = false;//判定图片数是否满额
        if($img_list_count>=20)
        {
            $fit_swf =   true;
        }
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
        ljl_dump(TASK_TEMPLATES_ROOT."goods_test_song_add_del.tpl.htm");
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_test_song_add_del.tpl.htm" );
        $tpl->assign ( 'global_header_html', $global_header_html );
        $tpl->assign('action',$action);
        $tpl->assign('fit_swf',$fit_swf);
        ///////////////////////
		$task_log_obj = POCO::singleton('pai_task_admin_log_class');
		$log_list = $task_log_obj->get_log_by_type(array('action_type'=>2,'action_id'=>$id));
		if($log_list)
		{
			foreach($log_list as $key => $val)
			{
				$log_list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
				$log_list[$key]['user_name'] = get_user_nickname_by_user_id($val['admin_id']);
			}
		}
		foreach($goods['default_data'] as $key => $val)
		{
			if($val['key']=='location_id')
			{
				$location_id = explode(',',$val['value']);
				$location_name = '';
				foreach($location_id as $val_de)
				{
					$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
				}
				$goods['default_data'][$key]['data'] = $location_name;
			}
		}
		///////////////////////
		$tpl->assign ( 'id', $id);
		$tpl->assign ( 'default_data', $goods['default_data'] );
		$tpl->assign ( 'system_data', $goods['system_data'] );
		$tpl->assign ( 'diy_data', $goods['diy_data'] );
		$tpl->assign ( 'prices_data', $goods['prices_data'] );
		$tpl->assign ( 'prices_data_list', $goods['prices_data_list'] );
	    $tpl->assign ( 'image_data', $goods['image_data'] );
	    $tpl->assign ( 'log_list', $log_list );
	break;
	case "export":
		$store_id = (int)$_INPUT['store_id'];
		$status = (int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_list = $type_obj->get_type_cate(2);
		
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		if($_set_debug)
		{
			$seller_obj->set_debug();
		}
		$store_info = $seller_obj->get_store_info($store_id);
		$show_type = explode(',',$store_info[0]['type_id']);
		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['show'] = in_array($val['id'],$show_type)?true:false;
			$type_list[$key]['selected'] = $val['id']==$type_id?true:false;
			$type_list_name[$val['id']] = $val;
		}
		$show_status = pai_mall_load_config("goods_show");
		foreach($show_status as $key => $val)
		{
			$show_list[] = array(
								'key'=>$key,
								'name'=>$val,
								'selected'=>$key==$show?true:false,
							    );
		}
		////////////
		$goods_status_list[]=array(
			                              'key' => 10,
										  'name' => "全部",
										  'selected' => $status===10?true:false,
										  );
		foreach($status_name as $key => $val)
		{
			$goods_status_list[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?true:false,
										  );
		}
		////////////
		$where = 1;	
		$where .= $store_id?" AND store_id='{$store_id}'":"";		
		$where .= $status != 10?" AND status = '{$status}'":"";
		$where .= $show?" AND is_show = '{$show}'":"";
		$where .= $type_id?" AND type_id = '{$type_id}'":"";
		$where .= $keyword?" AND titles like '%".pai_mall_change_str_in($keyword)."%'":"";
		if($status!=0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
		}
		$page = (int)$_INPUT['page'];
		$start_num = 0;
		if($page>=2)
		{
			$start_num = 1000*($page-1);
		}
		$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", "$start_num,1000");
		if(!$list)
		{
			exit('无相关数据信息');
		}
		$output_data = array();
		$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		$type_name_list = $type_obj -> get_type_attribute_cate(0);
		$type_name = array();
		foreach($type_name_list as $val)
		{
			$type_name[$val['id']] = $val;
		}
		foreach($list as $val)
		{
			$val['type_name'] = $type_list_name[$val['type_id']]['name'];
			$val['status_name'] = $status_name[$val['status']];
			$val['show_name'] = $show_status[$val['is_show']];
			$val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$val['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',$val['location_id']);
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"\n":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
			}
			$prices_list_de = unserialize($val['prices_list']);
			$val['prices_list_de']=array();
			if($prices_list_de)
			{
				foreach($prices_list_de as $key_de => $val_de)
				{
					$val['prices_list_de'][] = $val_de."/".$type_name[$key_de]['name'];
				}
			}
			$val['location_name'] = $location_name;			
			$ex_val = array(
							$val['user_id'],
							$val['goods_id'],
							$val['titles'],
							$val['location_name'],
							$val['prices'],
							$val['stock_num'],
							$val['type_name'],
							$val['add_time'],
							$val['audit_time'],
							$val['show_name'],
							$val['status_name'],
							implode("\n",$val['prices_list_de']),
							);
			$output_data[] = $ex_val;
		}
		//print_r($output_data);
		//exit;
		$headArr =array(
						'商家用户ID',
					    '商品ID',
					    '商品名称',
					    '城市',
					    '价格',
					    '库存',
					    '类型',
					    '添加时间',
					    '审核时间',
					    '上下架状态',
					    '状态',
					    '详细价格',
						);
		$select_name = "商品";
		getExcel($select_name, $headArr, $output_data,$select_name.'列表');
		exit;
	break;
	case "usergoodslist":
		$store_id = (int)$_INPUT['store_id'];
		$status = (int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_list = $type_obj->get_type_cate(2);
		
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		if($_set_debug)
		{
			$seller_obj->set_debug();
		}
		$store_info = $seller_obj->get_store_info($store_id);
		$show_type = explode(',',$store_info[0]['type_id']);
		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['show'] = in_array($val['id'],$show_type)?true:false;
			$type_list[$key]['selected'] = $val['id']==$type_id?true:false;
			$type_list_name[$val['id']] = $val;
		}
		$show_status = pai_mall_load_config("goods_show");
		foreach($show_status as $key => $val)
		{
			$show_list[] = array(
								'key'=>$key,
								'name'=>$val,
								'selected'=>$key==$show?true:false,
							    );
		}
		////////////
		$goods_status_list[]=array(
			                              'key' => 10,
										  'name' => "全部",
										  'selected' => $status===10?true:false,
										  );
		foreach($status_name as $key => $val)
		{
			$goods_status_list[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?true:false,
										  );
		}
		////////////		
		$where = "store_id='{$store_id}'";		
		$where .= $status != 10?" AND status = '{$status}'":"";
		$where .= $show?" AND is_show = '{$show}'":"";
		$where .= $type_id?" AND type_id = '{$type_id}'":"";
		$where .= $keyword?" AND titles like '%".pai_mall_change_str_in($keyword)."%'":"";
		if($status!=0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
		}
		$total_count = $task_goods_obj->get_goods_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("action" => "usergoodslist","begin_time" => $begin_time,"end_time" => $end_time,"status" => $status,"store_id" => $store_id,"show" => $show,"type_id" => $type_id,"keyword" => $keyword) );
		$page_obj->set ( $show_count, $total_count );		
		$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", $page_obj->limit());
		foreach($list as $key => $val)
		{
			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
			$list[$key]['status_name'] = $status_name[$val['status']];
			$list[$key]['show_name'] = $show_status[$val['is_show']];
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',$val['location_id']);
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
			}
			$list[$key]['location_name'] = $location_name;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_usergoods_list.tpl.htm" );
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'goods_status_list', $goods_status_list );
		$tpl->assign ( 'store_id', $store_id );
		$tpl->assign ( 'show', $show );
		$tpl->assign ( 'keyword', $keyword );
		$tpl->assign ( 'type_id', $type_id );
		$tpl->assign ( 'type_list', $type_list );
		$tpl->assign ( 'show_list', $show_list );
		$tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
	break;
	default:
		
		$status = (int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		
		$task_seller_obj = POCO::singleton('pai_mall_seller_class');		
		$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
		
		if($_POST)
		{
			//dump($_POST);
			
			foreach($_POST['detail'] as $k => $v)
			{
				if($v)
				{
					$parent_one = $task_goods_type_attribute_obj->get_property_info($k);
					$son_one = $task_goods_type_attribute_obj->get_property_info($v);
					if($v == 0)
					{
						$son_one['name'] = '请选择';
					}
					$compare_data[] = array(
											'info'=>"{$parent_one['name']}:{$son_one['name']}",
											'type_id'=>$k . "|" . $v,
											'type_md5'=>md5(md5($k)."|".md5($v)),
											);
				}
			}
		}
		
		$json_post_data = json_encode($_POST);
		
		$data = $task_goods_type_obj->get_first_cate();
		$json_data = json_encode($data);
		
		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$type_list[$key]['show'] = true;
			$type_list[$key]['selected'] = $val['id']==$type_id?true:false;
			$type_list_name[$val['id']] = $val;
		}
		$show_status = pai_mall_load_config("goods_show");
		foreach($show_status as $key => $val)
		{
			$show_list[] = array(
								'key'=>$key,
								'name'=>$val,
								'selected'=>$key==$show?true:false,
							    );
		}
		////////////
		$goods_status_list[]=array(
								  'key' => 10,
								  'name' => "全部",
								  'selected' => $status===10?true:false,
								  );
		foreach($status_name as $key => $val)
		{
			$goods_status_list[] = array(
			                              'key' => $key,
										  'name' => $val,
										  'selected' => $status===$key?true:false,
										  );
		}
		
/*		$where = 1;		
		$where .= $status != 10?" AND status = '{$status}'":"";
		$where .= $show?" AND is_show = '{$show}'":"";
		$where .= $type_id?" AND type_id = '{$type_id}'":"";
		$where .= $keyword?" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR user_id = '".(int)$keyword."')":"";
		if($status!=0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
		}
*/
        $_POST['type_id'] = $_POST['type_id']?$_POST['type_id']:$_GET['type_id'];
        $_POST['limit'] = '0,20';
		
		
		
		$goods_list = $task_goods_obj->search_goods_list_by_fulltext($_POST);
	
		$list = $goods_list['data'];
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar($_POST);
		$page_obj->set($show_count, $goods_list['total'] );		
		$profile_data = array();
		foreach($list as $key => $val)
		{
			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
			$list[$key]['status_name'] = $status_name[$val['status']];
			$list[$key]['show_name'] = $show_status[$val['is_show']];
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',$val['location_id']);
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
			}
			$list[$key]['location_name'] = $location_name;
			$list[$key]['location_name'] = $location_name;
			!$profile_data[$val['profile_id']]?$profile_data[$val['profile_id']] = $task_seller_obj->get_seller_profile($val['profile_id']):"";
			$list[$key]['profile_data'] = $profile_data[$val['profile_id']];
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_list_test.tpl.htm" );
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'goods_status_list', $goods_status_list );
		$tpl->assign ( 'show', $show );
		$tpl->assign ( 'keyword', $keyword );
		$tpl->assign ( 'type_id', $type_id );
		//$tpl->assign ( 'user_id', $user_id );
		$tpl->assign ( 'type_list', $type_list );
		
		$tpl->assign('post', $json_post_data );
		
		
		$tpl->assign('post_org',$_POST);
		$tpl->assign('compare_data',$compare_data);
		$tpl->assign('json_data',$json_data);
		$tpl->assign('type_info_profile',$type_info_profile);
		
		$tpl->assign('city',$_POST['city']);
		$tpl->assign('location_id',$_POST['location_id']);
		$tpl->assign('m_height',$_POST['m_height']);
		$tpl->assign('m_cups',$_POST['m_cups']);
		$tpl->assign('m_cup',$_POST['m_cup']);
		$tpl->assign('p_experience',$_POST['p_experience']);
		$tpl->assign('p_goodat',$_POST['p_goodat']);
		$tpl->assign('t_teacher',$_POST['t_teacher']);
		$tpl->assign('t_experience',$_POST['t_experience']);
		$tpl->assign('area',$_POST['area']);
		$tpl->assign('level',$_POST['level']);
		$tpl->assign('total_point',$_POST['total_point']);
		$tpl->assign('total_times',$_POST['total_times']);
		$tpl->assign('total_money',$_POST['total_money']);
		
		
		$tpl->assign ( 'show_list', $show_list );
		$tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
	break;
}
$tpl->output ();
?>