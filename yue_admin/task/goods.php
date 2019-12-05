<?php
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
//require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';
$task_goods_obj = POCO::singleton('pai_mall_goods_class');
$goods_statistical_obj = POCO::singleton('pai_mall_statistical_class');
$service_obj = POCO::singleton("pai_mall_certificate_service_class");
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
	//$task_goods_obj->set_debug();
}
$status_name = pai_mall_load_config('goods_status');
switch($action)
{
    case "copty_goods_id_to_user_id"://(ok)
        if($_POST)
        {
            $goods_id = (int)$_INPUT['goods_id'];
            $user_id = (int)$_INPUT['user_id'];
            if( ! $goods_id || ! $user_id )
            {
                exit('<script>alert("商品与用户id不能为空");window.location.href="goods.php?action=copty_goods_id_to_user_id"</script>');
            }
            $type_id_insert_goods_obj = POCO::singleton("pai_mall_type_id_goods_data_class");
            $rs = $type_id_insert_goods_obj->copy_goods_info_to_user_id($goods_id,$user_id);
            
        }
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."copy_goods_id_to_user_id.tpl.htm" );
    break;
    case "add"://(ok)
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
			if($_INPUT['combination_data'])
			{
				foreach($_INPUT['combination_data'] as $key => $val)
				{
					$_INPUT['combination_data'][$key]['images'] = $_POST['combination_data'][$key]['images'];
				}
			}
			if($_INPUT['prices_diy'])
			{
				foreach($_INPUT['prices_diy'] as $key => $val)
				{
					if($_POST['prices_diy'][$key]['detail'])
					{
						$_INPUT['prices_diy'][$key]['detail'] = $_POST['prices_diy'][$key]['detail'];
					}
				}
			}
			$data = $_INPUT;
			$img_data = array();
			foreach($img as $val)
			{
				$img_data[] = array('img_url'=>$val);
			}
			$data['img'] = $img_data;
            
            ljl_dump($data,true);
			//$re=$task_goods_obj->update_goods_prices_detail_for_42(2124194,$data,109650);
			//$re=$task_goods_obj->add_mall_goods_check(2124194,$data);
			//print_r($re);
			//exit;
			$in = $task_goods_obj->add_goods($data);
			print_r($in);
			exit;
			
			
			//print_r($in);
			//exit;
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
		//print_r($goods);
		foreach($goods['system_data'] as $key => $val)
		{
			if(in_array($val['key'],array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
			{
				$att_key = unserialize($val['value']);
				if($att_key)
				{
					foreach($att_key as $key_de => $val_de)
					{
						if($att_key[$key_de]['child_data'])
						{
							$att_de_val = array();
							foreach($att_key[$key_de]['child_data'] as $key_de_val => $val_de_val)
							{
								$att_de_val = array(
								                      'name' => $val_de_val['name'],
								                      'is_select' => $val_de_val['name']==$val_de['value']?true:false,
													  );
								$att_key[$key_de]['child_data'][$key_de_val] = $att_de_val;
							}
						}
					}
				}
				$goods['system_data'][$key]['child_data'] = $att_key;
			}			
		}
		
		//print_r($goods);
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_add.tpl.htm" );
        ///////////////////////
        $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
        $tpl->assign ( 'global_header_html', $global_header_html );
        ///////////////////////
		$tpl->assign ( 'store_id', $store_id );
		$tpl->assign ( 'type_id', $type_id );
		$tpl->assign ( 'type_stock_type', $goods['type_stock_type'] );		
		$tpl->assign ( 'default_data', $goods['default_data'] );
		$tpl->assign ( 'system_data', $goods['system_data'] );
		$tpl->assign ( 'diy_data', $goods['diy_data'] );
		$tpl->assign ( 'prices_data', $goods['prices_data'] );
		$tpl->assign ( 'prices_data_list', $goods['prices_data_list'] );
	    $tpl->assign ( 'image_data', $goods['image_data'] );
	break;
	case "copy"://ok
	    if($_POST and $_POST['user_str'] and $_POST['goods_id'])
		{
			$user_id_arr = explode(',',$_POST['user_str']);
			$goods = $task_goods_obj->get_goods_info($_POST['goods_id']);
			$obj = POCO::singleton('pai_user_class');
			$seller_obj = POCO::singleton('pai_mall_seller_class');
			$_INPUT['type_id'] = $goods['goods_data']['type_id'];
			$img = $_INPUT['upload_imgs_0'];
			if($_INPUT['combination_data'])
			{
				foreach($_INPUT['combination_data'] as $key => $val)
				{
					$_INPUT['combination_data'][$key]['images'] = $_POST['combination_data'][$key]['images'];
				}
			}
			$data = $_INPUT;
			$img_data = array();
			foreach($img as $val)
			{
				$img_data[] = array('img_url'=>$val);
			}
			$data['img'] = $img_data;
			
			foreach($user_id_arr as $val)
			{
				$user_id = '';
				$_INPUT['store_id'] = '';				
				//$user_id = $val;				
				$user_id = $val;//电话获取user_id
				//$user_id = $obj->get_user_id_by_phone($val);//电话获取user_id
				//echo $user_id.",";
				$store_id = $seller_obj->get_first_store_id_by_user_id($user_id);
				if($store_id)
				{
					$data['store_id'] = $store_id;
					$in = $task_goods_obj->add_goods($data);
					$task_goods_obj->change_goods_status($in['result'],1);
					$task_goods_obj->change_goods_show_status($in['result'],1);
					echo "phone:".$val."-->goods_id:".$_POST['goods_id']."-->"."user_id:".$user_id."-->"."new_goods_id:".$in['result']."-->".($in['result']>0?"ok":"bad")."<br>";
				}
			}
			//print_r($data);
			echo time().'ok';
			exit;
		}		
	break;
    case "do_edit_status"://ok
         $goods_id = $_INPUT['goods_id'];
         $edit_status = $_INPUT['edit_status'];
         if( ! $goods_id || ! $edit_status)
         {
             exit;
         }
         $rs = $task_goods_obj->update_goods_edit_status($goods_id,$edit_status);
         if($rs['result'] == 1)
         {
			 $task_log_obj = POCO::singleton('pai_task_admin_log_class');
			 $log_ty = $edit_status == 2?2010:2011;
			 $task_log_obj->add_log($yue_login_id,$log_ty,2,$_INPUT,'',$goods_id);
             echo json_encode(1);
         }
         exit;
    break;
    case "edit_status":
        $goods_id = $_INPUT['goods_id'];
        $rs = $task_goods_obj->get_mall_goods_check($goods_id);
//        ljl_dump('旧:');
//        ljl_dump($task_goods_obj->get_goods_info_by_sql(2128740,127361));
//        ljl_dump("新:");
//        ljl_dump($task_goods_obj->get_mall_goods_check(2128740));
        if($rs['default_data']['location_id'])
        {
            $rs['default_data']['location_text'] = get_poco_location_name_by_location_id($rs['default_data']['location_id']);
        }else
        {
            $rs['default_data']['location_text'] = '全国';
        }
        
        $topic_array = array(
            'd947bf06a885db0d477d707121934ff8'=>'摄影外拍',
            '24b16fede9a67c9251d3e7c7161c83ac'=>'美食活动',
            'ffd52f3c7e12435a724a8f30fddadd9c'=>'宠物活动',
            'ad972f10e0800b49d76fed33a21f6698'=>'亲子活动',
            'f61d6947467ccd3aa5af24db320235dd'=>'旅游户外',
            '142949df56ea8ae0be8b5306971900a4'=>'培训活动',
            'd34ab169b70c9dcd35e62896010cd9ff'=>'综合类活动',
        );
        
        $topic_info_array = array(
            '41f1f19176d383480afa65d325c06ed0'=>'室内',
            '8bf1211fd4b7b94528899de0a43b9fb3'=>'室外',
            'a02ffd91ece5e7efeb46db8f10a74059'=>'旅拍',
            'bca82e41ee7b0833588399b1fcd177c7'=>'模特活动'
            
        );
        
        include_once(TASK_TEMPLATES_ROOT."goods_edit_status_tpl.php");
        
    break;
	case "chstatus"://审核
        
        $goods_not_pass_config = pai_mall_load_config('goods_not_pass');
        $id = (int)$_INPUT['id'];
	    $status = (int)$_INPUT['status'];
		if($_GET['note']!='show')
		{
			$note = iconv('utf-8','gbk',$_INPUT['note']);
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			$re = $task_goods_obj->change_goods_status($id,$status);
			$log_ty = $status == 1?2006:2007;
			$re['result'] == 1?$task_log_obj->add_log($yue_login_id,$log_ty,2,$_INPUT,$note,$id):"";
			$re['message']=iconv('gbk','utf-8',$re['message']);
			echo json_encode($re);
			exit;
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_change_status.tpl.htm" );
        
        $tpl->assign('goods_not_pass_config',$goods_not_pass_config);
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
			$log_ty = $status == 1?2008:2009;
			$re['result'] == 1?$task_log_obj->add_log($yue_login_id,$log_ty,2,$_INPUT,$note,$id):"";
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
		$goods = $task_goods_obj->get_goods_info_by_sql($id);
		if($goods['goods_data']['user_id'] == 109650)
		{
			print_r($goods);
		}
        if($_POST)
		{
			if(in_array($goods['goods_data']['type_id'],array(5,43)) and $yue_login_id != 109650)
			{
				echo "<script>alert('暂停修改功能');window.open('goods.php','_self')</script>";
				exit;
			}
			//$img = explode("\r\n",$_POST['img']);
			$img = $_INPUT['upload_imgs_0'];
			if($_INPUT['combination_data'])
			{
				foreach($_INPUT['combination_data'] as $key => $val)
				{
					$_INPUT['combination_data'][$key]['images'] = $_POST['combination_data'][$key]['images'];
				}
			}
			if($_INPUT['prices_diy'])
			{
				foreach($_INPUT['prices_diy'] as $key => $val)
				{
					if($_POST['prices_diy'][$key]['detail'])
					{
						$_INPUT['prices_diy'][$key]['detail'] = $_POST['prices_diy'][$key]['detail'];
					}
				}
			}
			$data = $_INPUT;
			$img_data = array();
			foreach($img as $val)
			{
				$img_data[] = array('img_url'=>$val);
			}
			$data['img'] = $img_data;

/*			$data['type_id']=41;
			$data['store_id']=1;
			$data['cacke_id']=12365478934;
			$task_goods_obj->set_goods_data_for_temp($data);
			$re = $task_goods_obj->show_goods_data_for_temp($data['cacke_id']);
			print_r($re);
			exit;
*/
			$task_log_obj = POCO::singleton('pai_task_admin_log_class');
			if($goods['goods_data']['type_id'] == 42)
			{
				$re = $task_goods_obj->update_goods_for_42($id,$data);
				$mess = $re['message']."||";
				//修改价格
				foreach($data['prices_diy'] as $key => $val)
				{
					//$val['buy_num'] = 5;
					$prices_de['prices_diy'][$key] = $val;
					//print_r($prices_de);
					$re_de = $task_goods_obj->update_goods_prices_detail_for_42($id,$prices_de,$goods['goods_data']['user_id'],true);
					$mess .= $re_de['message']."|"; 
				}				
				//
				echo "<script>alert('".$mess."');window.open('?action=edit&id={$id}','_self')</script>";				
                $task_log_obj->add_log($yue_login_id,2004,2,$_INPUT,'',$id);
				exit;
			}
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
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_edit.tpl.htm" );
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
			if(in_array($val['input'],array(1,2)) and $val['child_data'])
			{
				$select_val = $val['input']==1?$val['value']:explode(',',$val['value']);
				foreach($val['child_data'] as $key_de => $val_de)
				{
					$goods['default_data'][$key]['child_data'][$key_de]['is_select'] = '';
					if($val['input']==1 and $select_val == $val_de['val'])
					{
						$goods['default_data'][$key]['child_data'][$key_de]['is_select'] = true;
					}
					elseif($val['input']==2 and in_array($val_de['val'],$select_val) )
					{
						$goods['default_data'][$key]['child_data'][$key_de]['is_select'] = true;
					}
				}
			}
		}
		foreach($goods['system_data'] as $key => $val)
		{
			if(in_array($val['key'],array('758874998f5bd0c393da094e1967a72b','ad13a2a07ca4b7642959dc0c4c740ab6','3fe94a002317b5f9259f82690aeea4cd','550a141f12de6341fba65b0ad0433500','67f7fb873eaf29526a11a9b7ac33bfac','1a5b1e4daae265b790965a275b53ae50')))
			{
				$att_key = unserialize($val['value']);
				if($att_key)
				{
					foreach($att_key as $key_de => $val_de)
					{
						if($att_key[$key_de]['child_data'])
						{
							$att_de_val = array();
							foreach($att_key[$key_de]['child_data'] as $key_de_val => $val_de_val)
							{
								$att_de_val = array(
								                      'name' => $val_de_val['name'],
								                      'is_select' => $val_de_val['name']==$val_de['value']?true:false,
													  );
								$att_key[$key_de]['child_data'][$key_de_val] = $att_de_val;
							}
						}
					}
				}
				$goods['system_data'][$key]['child_data'] = $att_key;
			}			
		}
		foreach((array)$goods['prices_data_list'] as $key => $val)
		{
			$goods['prices_data_list'][$key]['time_s'] = $val['time_s']?date("Y-m-d H:i:s",$val['time_s']):'';
			$goods['prices_data_list'][$key]['time_e'] = $val['time_e']?date("Y-m-d H:i:s",$val['time_e']):'';
			$goods['prices_data_list'][$key]['has_num'] = $val['stock_num_total']-$val['stock_num'];
		}
        
        if( ! empty($goods['image_data']['value']) )
        {
            foreach($goods['image_data']['value'] as $k => &$v)
            {
                $v['big_img_url'] = yueyue_resize_act_img_url($v['img_url'], $size = '');
            }
        }
        
        $seller_info = array();
        if( ! empty($goods) )
        {
            $seller_obj = POCO::singleton('pai_mall_seller_class');
            $seller_info = $seller_obj->get_seller_info($goods['goods_data']['seller_id']);
            if( ! empty($seller_info) )
            {
                $goods['goods_data']['seller_status'] = $seller_info['seller_data']['status'];
                //新的品类还在测试,传false时,拿的品类数据状态，最全，true为默认线上
                
                if($goods['goods_data']['type_id'] == 42)
                {
                    $service_status = $service_obj->check_user_activity_open_or_not($goods['goods_data']['user_id']);
                }else
                {
                    $service_status = $service_obj->get_service_open_or_not($goods['goods_data']['user_id'],$goods['goods_data']['type_id'],false);
                }
                
                if( ! $service_status )
                {
                    $goods['goods_data']['seller_status'] = 0;
                }
            }
        }
//		if($goods['goods_data']['user_id'] == 109650)
//		{
//			print_r($goods);
//		}
		///////////////////////
		$tpl->assign ( 'id', $id);
        $tpl->assign('goods',$goods);
		$tpl->assign ( 'stock_type', $goods['goods_data']['stock_type']);
		$tpl->assign ( 'default_data', $goods['default_data'] );
		$tpl->assign ( 'system_data', $goods['system_data'] );
		$tpl->assign ( 'diy_data', $goods['diy_data'] );
		$tpl->assign ( 'combination_data', $goods['combination_data'] );
		$tpl->assign ( 'contact_data', $goods['contact_data'] );
		$tpl->assign ( 'prices_data', $goods['prices_data'] );
		$tpl->assign ( 'prices_data_list', $goods['prices_data_list'] );
	    $tpl->assign ( 'image_data', $goods['image_data'] );
	    $tpl->assign ( 'log_list', $log_list );
		$tpl->assign ( 'admin_ac', in_array($yue_login_id,array(109650))?true:false );
	break;
	case "goods_export":
		$store_id = (int)$_INPUT['store_id'];
		$status = (int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:'';
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:'';
		
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
		$where .= $keyword?" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR user_id = '".(int)$keyword."')":"";
		if($status!=0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
		}
        
        $is_black = 10;
        if( isset($_INPUT['is_black']) )
        {
            $is_black = $_INPUT['is_black'];
            if($is_black != 10)
            {
                $where .= " AND is_black= '{$is_black}'";
            }
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
        //Excel_v2::start($headArr,$output_data,$select_name,'s3');
		exit;
	break;
	case "usergoodslist":
		$store_id = (int)$_INPUT['store_id'];
		$status = $_INPUT ['status']==''?10:(int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
		
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
		elseif($status==0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND add_time >= {$begin_time_s} AND add_time <= {$end_time_s}";
		}
		$total_count = $task_goods_obj->get_goods_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("action" => "usergoodslist","begin_time" => $begin_time,"end_time" => $end_time,"status" => $status,"store_id" => $store_id,"show" => $show,"type_id" => $type_id,"keyword" => $keyword) );
		$page_obj->set ( $show_count, $total_count );		
		$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", $page_obj->limit());
		$task_seller_obj = POCO::singleton('pai_mall_seller_class');
		$service_obj = POCO::singleton("pai_mall_certificate_service_class");
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
			$list[$key]['seller_status'] = $task_seller_obj->get_seller_status($val['seller_id']);
			//
			$service_status = $service_obj->get_service_open_or_not($val['user_id'],$val['type_id'],false);
			if(!$service_status )
			{
				$list[$key]['seller_status'] = 0;
			}
			//
		}
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_usergoods_list.tpl.htm" );
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'goods_status_list', $goods_status_list );
		$tpl->assign ( 'store_id', $store_id );
		$tpl->assign ( 'show', $show );
		$tpl->assign ( 'keyword', $keyword );
		$tpl->assign ( 'user_id', $yue_login_id );
		$tpl->assign ( 'type_id', $type_id );
		$tpl->assign ( 'type_list', $type_list );
		$tpl->assign ( 'show_list', $show_list );
		$tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
	break;
	
	// 以下功能为 管理场次订单
	case "order_management":
		// 以下获取 待付款 待签到 信息
		$task_order_obj = POCO::singleton('pai_mall_order_class');
		
		$id = (int)$_INPUT['id'];
		$activity_id = (int)$_INPUT['activity_id'];
		$stage_id = (int)$_INPUT['stage_id'];
		
		if ( !empty($activity_id) and !empty($stage_id) )
		{
			$ret = 'false';
			$ret = $task_order_obj->close_order_for_stage($activity_id, $stage_id);
					    
			pai_log_class::add_log(array('activity_id'=>$activity_id, 'ret'=>$ret), 'lose_order_for_stage', 'yue_admin_task');
			echo json_encode($ret);
			exit;
		}
		else
		{
			
		$goods = $task_goods_obj->get_goods_info($id);

		// 用户 ID
		$user_id = $goods['goods_data']['user_id'];
		// 商品 ID
		$activity_id = $goods['goods_data']['goods_id'];		
		foreach ($goods['prices_data_list'] as $key => &$v ) 
		{			
			$stage_id = $v['type_id'];  // 场次 ID
			$order_info = $task_order_obj->get_order_number_by_stage_for_seller($user_id, $activity_id , $stage_id);
			$v['order_status'] = $order_info;
			
			
			$v['order_is_show'] = 0;
			if ($order_info['wait_pay']>0 || $order_info['wait_sign']>0)
			{
				$v['order_is_show'] = 1;
			}
		}		
	    $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."order_management.tpl.htm" );
		$tpl->assign ( 'goods_data', $goods['goods_data']);
		// 服务地址
		$tpl->assign ( 'goods_att', $goods['goods_att']['272']);
		$tpl->assign ( 'prices_data_list', $goods['prices_data_list']);		
		}
		
	    break;
	    
	default:
		$status = (int)$_INPUT ['status'];
		$edit_status = (int)$_INPUT ['edit_status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keyword = $_INPUT['keyword'];
		$orderby = $_INPUT['orderby']?$_INPUT['orderby']:0;		
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:'';
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:'';
        
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_list = $type_obj->get_type_cate(2);
		$search_type_list = $type_obj->get_first_cate();

		$type_list_name = array();
		foreach($type_list as $key => $val)
		{
			$val['id']!=42?$type_list[$key]['show'] = true:"";
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
		
		$where = "type_id != '42'";		
		$where .= $status != 10?" AND status = '{$status}'":"";
		$where .= $show?" AND is_show = '{$show}'":"";
		$where .= $type_id?" AND type_id = '{$type_id}'":"";
		$where .= $keyword?" AND (titles like '%".pai_mall_change_str_in($keyword)."%' OR user_id = '".(int)$keyword."' OR goods_id = '".(int)$keyword."')":"";
		if($status!=0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND audit_time >= {$begin_time_s} AND audit_time <= {$end_time_s}";
		}
		elseif($status==0 and $begin_time and $end_time)
		{
			$begin_time_s = strtotime($begin_time);
			$begin_time_s = $begin_time_s?$begin_time_s:0;
			$end_time_s = strtotime($end_time);
			$end_time_s = $end_time_s?$end_time_s+86400:0;
			$where .= " AND add_time >= {$begin_time_s} AND add_time <= {$end_time_s}";
		}
        $is_black = 10;
        if( isset($_INPUT['is_black']) )
        {
            $is_black = $_INPUT['is_black'];
            if($is_black != 10)
            {
                $where .= " AND is_black= '{$is_black}'";
            }
        }
		//$post = mall_set_post_for_search($_POST);
		//echo $post;
		//print_r(mall_get_post_for_search($post));
		$total_count = $task_goods_obj->get_goods_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array ("status" => $status,"begin_time" => $begin_time,"end_time" => $end_time,"show" => $show,"type_id" => $type_id,"keyword" => $keyword,'is_black'=>$is_black) );
		$page_obj->set ( $show_count, $total_count );		
		$list = $task_goods_obj->get_goods_list(false, $where, "goods_id DESC", $page_obj->limit());
		//print_r($list);
		$task_seller_obj = POCO::singleton('pai_mall_seller_class');
        $add_time_color = '';
		
		foreach($list as $key => $val)
		{
			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
			$list[$key]['status_name'] = $status_name[$val['status']];
			$list[$key]['show_name'] = $show_status[$val['is_show']];
            
            if( $val['update_time'] > 0)
            {
                $compare_time = $val['update_time'];
            }else if($val['add_time'] > 0)
            {
                $compare_time = $val['add_time'];
            }
            
            $now = time();
            
            if( ($now-$compare_time) > 24*3600 && $val['status'] == 0 )
            {
                $add_time_color = 'rgb(245, 60, 60);';
            }
            
            $list[$key]['add_time_color'] = $add_time_color;
            $list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['update_time'] = $val['update_time']>0?date("Y-m-d H:i:s",$val['update_time']):"未修改";
			$list[$key]['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',$val['location_id']);
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
			}
			$list[$key]['location_name'] = $location_name;
			$list[$key]['seller_status'] = $task_seller_obj->get_seller_status($val['user_id'],2);
            
			////活动还在测试，所以拿状态时，传个false,默认是true代表线上
            if($val['type_id'] == 42)
            {
                $service_status = $service_obj->check_user_activity_open_or_not($val['user_id']);
            }else
            {
               $service_status = $service_obj->get_service_open_or_not($val['user_id'],$val['type_id'],false);
            }
			
			if(!$service_status )
			{
				$list[$key]['seller_status'] = 0;
			}
			////
            unset($add_time_color);
            
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
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_list.tpl.htm" );
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
        
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'orderby', $orderby );
		
        $tpl->assign('is_black',$is_black);
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'goods_status_list', $goods_status_list );
		$tpl->assign ( 'show', $show );
		$tpl->assign ( 'keyword', $keyword );
		$tpl->assign ( 'type_id', $type_id );
		//$tpl->assign ( 'user_id', $user_id );
		$tpl->assign ( 'type_list', $type_list );
		$tpl->assign( 'search_type_list' , json_encode($search_type_list));
		$json_post_data = json_encode($_POST);
		$tpl->assign('post', $json_post_data );
		$tpl->assign ( 'show_list', $show_list );
		$tpl->assign ( 'begin_time', $begin_time );
		$tpl->assign ( 'end_time', $end_time );
		$tpl->assign ( 'edit_status', $edit_status );		
	break;
}
if( is_object($tpl) )
{
    $tpl->output ();
}

?>