<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
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
	$mall_obj->set_debug();
}

switch($action)
{
	case "export":
		$page = (int)$_INPUT['page'];
		$type_id = (int)$_INPUT['type_id'];
		$status = (int)$_INPUT ['status'];
		$keyword = $_INPUT ['keyword'];
		$seller_status = pai_mall_load_config("seller_status");
		$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
        
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
        
		$show_count = 1;
		$offect = ($page-1)*$show_count;
		$limit = "{$offect},{$show_count}";
        
		$list = $mall_obj->search_seller_list_by_fulltext($_INPUT,$limit);
        
		foreach($list['data'] as $key => &$val)
		{
			$val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$val['status_name'] = $seller_status[$val['seller_status']];
			$val['user_name'] = $val['seller_add_user']?get_user_nickname_by_user_id($val['seller_add_user']):"批量";
			$val['location_name'] = $val['location_id']?get_poco_location_name_by_location_id($val['location_id']):"全国";
            
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
                        $val['service_long'] = get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de]) ? get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de]) : '无';
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
            
			$ex_val = array(
                $val['user_id'],
                $val['name'],
                $val['total_overall_score'],
                $val['seller_bill_finish_num'],
                $val['prices'],
                $val['location_name'],
                $val['user_name'],
                $val['add_time'],
                $val['status_name'],
                $val['onsale_num']."/".$val['goods_num'],
                ! empty($type_name[3]) ? $type_name[3]['type_name'] : '',
                ! empty($type_name[5]) ? $type_name[5]['type_name'] : '',
                ! empty($type_name[12]) ? $type_name[12]['type_name'] : '',
                ! empty($type_name[31]) ? $type_name[31]['type_name'] : '',
                ! empty($type_name[40]) ? $type_name[40]['type_name'] : '',
                $val['service_long'],
                
            );
            
			$output_data[] = $ex_val;
		}

        
        $headArr =array(
            '商家用户ID',
            '商家名称',
            '综合评分',
            '总交易次数',
            '总交易金额',
            '地区',
            '审核人',
            '审核时间',
            '状态',
            '上架/总数',
            '化妆服务',
            '摄影培训服务',
            '影棚租赁服务',
            '模特服务',
            '摄影服务',
            '管理人员'
        );
        $select_name = "商家";
		getExcel($select_name, $headArr, $output_data,$select_name.'列表');
		exit;
	break;
	default:
		$p = $_INPUT['p'] ? (int)$_INPUT['p'] : 1;
        $status = (int)$_INPUT ['status'];
        $keywords = $_INPUT ['keywords'];
        $type_id = (int)$_INPUT['type_id'];
        
        //数据组装
        if( ! empty($_INPUT['detail']) )
        {
            foreach($_INPUT['detail'] as $k => $v)
            {
                $_INPUT[$k] = $v;
            }
            unset($_INPUT['detail']);
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
		$post_data = mall_query_str($_INPUT);
			
		$page_obj = new show_page ();

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
			$list[$key]['location_name'] = $val['location_id']?get_poco_location_name_by_location_id($val['location_id']):"全国";
			$seller_info = $mall_obj->get_seller_info($val['seller_id']);
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
				}
				$type_name[] = $t_name.($seller_info['seller_data']['service_belong'][$val_de]?"[".get_user_nickname_by_user_id($seller_info['seller_data']['service_belong'][$val_de])."]":"").($att_name?"<br>( {$att_name} )":"");
			}
			$list[$key]['type_name'] = implode('<br>',$type_name);
			$list[$key]['store_id'] = $seller_info['seller_data']['company'][0]['store'][0]['store_id'];
		}
		//print_r($list);
        $tpl->assign('type_id',$type_id);
      
        $tpl->assign('add_time_s',$_INPUT['add_time_s']);
        $tpl->assign('add_time_e',$_INPUT['add_time_e']);
		$tpl->assign('city',$_INPUT['city']);
		$tpl->assign('location_id',$_INPUT['location_id']);
        
        
		$tpl->assign('m_height',$_INPUT['m_height']);
		$tpl->assign('m_cups',$_INPUT['m_cups']);
		$tpl->assign('m_cup',$_INPUT['m_cup']);
		$tpl->assign('p_experience',$_INPUT['p_experience']);
		$tpl->assign('p_goodat',$_INPUT['p_goodat']);
		$tpl->assign('t_teacher',$_INPUT['t_teacher']);
		$tpl->assign('t_experience',$_INPUT['t_experience']);

		$tpl->assign ('post_dataformat',$post_data);
        
		$tpl->assign('level',$_INPUT['level']);
		$tpl->assign('total_point',$_INPUT['total_point']);
		$tpl->assign('total_times_s',$_INPUT['total_times_s']);
		$tpl->assign('total_times_e',$_INPUT['total_times_e']);
		$tpl->assign('total_money_s',$_INPUT['total_money_s']);
		$tpl->assign('total_money_e',$_INPUT['total_money_e']);
        $tpl->assign('price_s',$_INPUT['price_s']);
        $tpl->assign('price_e',$_INPUT['price_e']);
        
        
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
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