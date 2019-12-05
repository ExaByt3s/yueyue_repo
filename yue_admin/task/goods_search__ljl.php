<?php
set_time_limit(0);
ini_set('memory_limit', '256M');

include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/Excel_v2.class.php');
include_once 'common.inc.php';

$head = array(
    '列1',
    '列2',
);
$one_data = array(
    '2014-04-06 12:12:15',
    '2014-04-06 12:12:15你好樂',
    
);

for($i=1;$i<=100;$i++)
{
    $data[] = $one_data;
}

Excel_v2::start($head,$data,'手机','s3');

exit;





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
	//$task_goods_obj->set_debug();
}
$status_name = pai_mall_load_config('goods_status');
switch($action)
{
	case "export":
		$store_id = (int)$_INPUT['store_id'];
		$status = (int)$_INPUT ['status'];
		$show = (int)$_INPUT ['show'];
		$type_id = (int)$_INPUT ['type_id'];
		$keywords = $_INPUT['keywords'];
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
		
		$page = (int)$_INPUT['page'];
        $per_page = 1000;
		$start_num = 0;
		if($page>=2)
		{
			$start_num = $per_page*($page-1);
		}
        
        $list = $task_goods_obj->search_goods_list_by_fulltext($_INPUT,"{$start_num},{$per_page}");
        
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
		foreach($list['data'] as $val)
		{
            
            $val['type_name'] = $type_list_name[$val['type_id']]['name'];
			$val['status_name'] = $status_name[$val['goods_status']];
            $val['score'] = $val['review_times']?sprintf('%.2f',$val['total_overall_score']/$val['review_times']):0;
			$val['show_name'] = $show_status[$val['is_show']];
			$val['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$val['audit_time'] = $val['audit_time']>0?date("Y-m-d H:i:s",$val['audit_time']):"未审核";
			$location_id = explode(',',$val['location_id']);
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"\n":"").($val_de?get_poco_location_name_by_location_id($val_de):"全国");
			}
			$prices_list_de = unserialize($val['priceslist']);
			$val['prices_list_de']=array();
			if($prices_list_de)
			{
				foreach($prices_list_de as $key_de => $val_de)
				{
					$val['prices_list_de'][] = $val_de."/".$type_name[$key_de]['name'];
				}
			}
			$val['location_name'] = $location_name;
           
            !$profile_data[$val['profile_id']]?$profile_data[$val['profile_id']] = $seller_obj->get_seller_profile($val['profile_id']):"";
			$val['profile_data'] = $profile_data[$val['profile_id']];
            
            $goods_data = $task_goods_obj->get_goods_info($val['goods_id']);
            $val['goods_att'] = $goods_data['goods_att'];
            
            $val['belong_user'] = $goods_data['goods_data']['belong_user']?get_user_nickname_by_user_id($goods_data['goods_data']['belong_user']):"无";
            
            
			$ex_val = array
            (
                $val['user_id'],
                get_user_nickname_by_user_id($val['user_id']),
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
            
            if($type_id == 3)
            {
                $ex_val[] = str_replace("<br>","", $val['goods_att']['152']);
            }else if($type_id == 31)
            {
                $ex_val[] = $val['belong_user'];
                $ex_val[] = $val['profile_data']['0']['att_data_format']['m_height']['value'];
                $ex_val[] = $val['profile_data']['0']['att_data_format']['m_cups']['value'].' '.$val['profile_data']['0']['att_data_format']['m_cup']['value'];
            }else if($type_id == 40)
            {
                $ex_val[] = $val['profile_data']['0']['att_data_format']['p_experience']['value'];
                $ex_val[] = $val['profile_data']['0']['att_data_format']['p_goodat']['value'];
                $ex_val[] = $val['goods_att']['90'];
                $ex_val[] = $val['goods_att']['95'];
                $ex_val[] = $val['goods_att']['96'];
                $ex_val[] = $val['goods_att']['97'];
                $ex_val[] = $val['goods_att']['98'];
                $ex_val[] = str_replace("<br>", "", $val['goods_att']['99']);
                $ex_val[] = str_replace("<br>", "", $val['goods_att']['106']);
                $ex_val[] = str_replace("<br>", "", $val['goods_att']['102']);
            }else if($type_id == 12)
            {
                $ex_val[] = str_replace("<br>","", $val['goods_att']['20']);
                $ex_val[] = $val['goods_att']['19'];
            }else if($type_id == 5)
            {
                $ex_val[] = $val['profile_data']['0']['att_data_format']['t_teacher']['value'];
                $ex_val[] = $val['profile_data']['0']['att_data_format']['t_experience']['value'];
                $ex_val[] = str_replace("<br>", "", $val['goods_att']['62']);
                $ex_val[] = str_replace("<br>", "", $val['goods_att']['133']);
            }
            
            $output_data[] = $ex_val;
		}
        
        $headArr =array(
						'商家用户ID',
                        '昵称',
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
        if($type_id == 3)
        {
            $headArr[] = '跟妆类型';
        }else if($type_id == 31)
        {
            $headArr[] = '管理员';
            $headArr[] = '身高';
            $headArr[] = '罩杯';
           
        }else if($type_id == 40)
        {
            $headArr[] = '擅长类型';
            $headArr[] = '从业年限';
            $headArr[] = '拍摄类型';
            $headArr[] = '拍摄时长';
            $headArr[] = '底片张数';
            $headArr[] = '精修张数';
            $headArr[] = '服装数目';
            $headArr[] = '化妆';
            $headArr[] = '相册';
            $headArr[] = '原片';
        }else if($type_id == 12)
        {
            $headArr[] = '背景';
            $headArr[] = '面积';
        }else if($type_id == 5)
        {
            $headArr[] = '老师类型';
            $headArr[] = '培训经验';
            $headArr[] = '授课方式';
            $headArr[] = '课程类型';
        }
		$select_name = "商品";
        
        Excel_v3::start($headArr,$output_data,'手机','s3');
        
		//getExcel($select_name, $headArr, $output_data,$select_name.'列表');
		exit;
	break;
	default:
		$p = $_INPUT['p'] ? (int)$_INPUT['p'] : 1;
		$status = (int)$_INPUT ['status'];
		$is_show = (int)$_INPUT ['is_show'];
		$_INPUT ['type_id'] = in_array((int)$_INPUT ['type_id'],array(3,5,12,31,40))?(int)$_INPUT ['type_id']:31;
		$_INPUT ['user_id'] = (int)$_INPUT ['user_id'];
		$user_id = $_INPUT ['user_id']?$_INPUT ['user_id']:"";
		$type_id = $_INPUT ['type_id'];
		$keywords = $_INPUT['keywords'];
		//$begin_time = $_INPUT ['begin_time']?$_INPUT ['begin_time']:date('Y-m-d', strtotime('-7 day'));
		//$end_time = $_INPUT ['end_time']?$_INPUT ['end_time']:date('Y-m-d');
		$begin_time = $_INPUT ['begin_time'];
		$end_time = $_INPUT ['end_time'];
		
		$task_seller_obj = POCO::singleton('pai_mall_seller_class');		
		$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
		$task_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');	
		
        $type_list = $task_goods_type_obj->get_type_cate(2);
        
        $detail_data = array();
        $third_data = array();
        if( ! empty($_INPUT['detail']) && $_INPUT['for_page'] == 1 )
        {
            $_INPUT['detail'] = urldecode($_INPUT['detail']);
            $detail_ll = explode('_',$_INPUT['detail']);
            
            if( ! empty($detail_ll) )
            {
                foreach($detail_ll as $k => $v)
                {
                    $detail_parent_child_ll = explode(',',$v);
                    if( ! empty($detail_parent_child_ll) )
                    {
                        $detail_data[$detail_parent_child_ll['0']] = $detail_parent_child_ll['1'];
                    }
                } 
            } 
           
           $_INPUT['detail'] = $detail_data;
        }
        
        if( ! empty($_INPUT['third']) && $_INPUT['for_page'] == 1 )
        {
            $_INPUT['third'] = urldecode($_INPUT['third']);
            $third_ll = explode('_',$_INPUT['third']);
            if( ! empty($third_ll) )
            {
                foreach($third_ll as $k => $v)
                {
                    $third_parent_child_ll = explode(',',$v);
                    if( ! empty($third_parent_child_ll) )
                    {
                        $third_data[$third_parent_child_ll['0']] = $third_parent_child_ll['1'];
                    }
                } 
            } 
           
           $_INPUT['third'] = $third_data;
        }
        
		$json_post_data = json_encode($_INPUT);
		
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
								'selected'=>$key==$is_show?true:false,
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
		
        $_INPUT['type_id'] = $_POST['type_id']?$_POST['type_id']:$_GET['type_id'];
        $_INPUT['begin_time'] = $begin_time;
        $_INPUT['end_time'] = $end_time;
		$show_count = 20;
		$offect = ($p-1)*$show_count;
		$limit = "{$offect},{$show_count}";
		
		if($_set_debug)
		{
			$_INPUT['debug'] = true;
		}
        
        $price_list = array();
        
        if($type_id == 31 || $type_id == 12)
        {
            $price_list_arys = $task_goods_obj->get_goods_prices_scope_list(31);
            
            $price_list_val = (int)$_INPUT['price_list'];
            
            foreach($price_list_arys as $k => &$v)
            {
                if($v['id'] == $price_list_val)
                {
                    $v['selected_one'] = 1;
                    break;
                }
            }
            
        }
        
        dump($price_list_val);
        dump($price_list_arys);
        
        $goods_list = $task_goods_obj->search_goods_list_by_fulltext($_INPUT,$limit);
	    
		$list = $goods_list['data'];
		$page_obj = new show_page ();
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
        
        
        //分页的数据处理
        $detail = $third = '';
        if( ! empty($_INPUT['detail']) )
        {
            foreach($_INPUT['detail'] as $k => $v)
            {
                $detail .= "$k,$v". '_';
            }
            
            $detail = substr($detail, 0, -1);
            $_INPUT['detail'] = $detail;
            
            unset($detail);
            $_INPUT['for_page'] = 1;
            
        }
        
        if( ! empty($_INPUT['third']))
        {
            foreach($_INPUT['third'] as $k => $v)
            {
                $third .= "$k,$v". '_';
            }
            
            $third = substr($third, 0, -1);
            $_INPUT['third'] = $third;
            unset($third);
        }
        
		$page_obj->setvar($_INPUT);
		$page_obj->set($show_count, $goods_list['total'] );		
		$profile_data = array();
		foreach($list as $key => $val)
		{
            $list[$key]['user_name'] = $val['user_id']."</br>"."(".get_user_nickname_by_user_id($val['user_id']).")";
			$list[$key]['type_name'] = $type_list_name[$val['type_id']]['name'];
			$list[$key]['status_name'] = $status_name[$val['goods_status']];
			$list[$key]['score'] = $val['review_times']?sprintf('%.2f',$val['total_overall_score']/$val['review_times']):0;
			$list[$key]['show_name'] = $show_status[$val['is_show']];
			$list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
			$list[$key]['onsale_time'] = $val['onsale_time']>0?date("Y-m-d H:i:s",$val['onsale_time']):"未上线";
			$location_id = explode(',',trim($val['location_id'],','));
			$location_name = '';
			foreach($location_id as $val_de)
			{
				$location_name .= ($location_name?"<br>":"").($val_de?get_poco_location_name_by_location_id($val_de):($val_de==0?"全国":""));
			}
			$list[$key]['location_name'] = $location_name;
			$list[$key]['location_name'] = $location_name;
			
			!$profile_data[$val['profile_id']]?$profile_data[$val['profile_id']] = $task_seller_obj->get_seller_profile_for_search($val['profile_id']):"";
			$list[$key]['profile_data'] = $profile_data[$val['profile_id']];
			$goods_data = $task_goods_obj->get_goods_info_for_search($val['goods_id']);
			$list[$key]['belong_user'] = $goods_data['goods_data']['belong_user']?get_user_nickname_by_user_id($goods_data['goods_data']['belong_user']):"无";
			$list[$key]['goods_att'] = $goods_data['goods_att'];			
		}
		
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."goods_list_test.tpl.htm" );
		
		
		$tpl->assign ( 'page', $page_obj->output ( 1 ) );
		$tpl->assign ( 'list', $list );
		$tpl->assign ( 'status', $status );
		$tpl->assign ( 'goods_status_list', $goods_status_list );
		$tpl->assign ( 'is_show', $is_show );
		$tpl->assign ( 'keywords', $keywords );
		$tpl->assign ( 'type_id', $type_id );
		$tpl->assign ( 'user_id', $user_id );
		$tpl->assign ( 'type_list', $type_list );
		
		$tpl->assign('post', $json_post_data );
		
		
		$tpl->assign('post_org',$_INPUT);
		$tpl->assign('compare_data',$compare_data);
		$tpl->assign('json_data',$json_data);
		$tpl->assign('type_info_profile',$type_info_profile);
		
		$tpl->assign('add_time_s',$_INPUT['add_time_s']);
		$tpl->assign('add_time_e',$_INPUT['add_time_e']);
		$tpl->assign('admin_id',$yue_login_id);
		$tpl->assign('city',$_INPUT['city']);
		$tpl->assign('location_id',$_INPUT['location_id']);
		$tpl->assign('m_height',$_INPUT['m_height']);
		$tpl->assign('m_cups',$_INPUT['m_cups']);
		$tpl->assign('m_cup',$_INPUT['m_cup']);
		$tpl->assign('p_experience',$_INPUT['p_experience']);
		$tpl->assign('p_goodat',$_INPUT['p_goodat']);
		$tpl->assign('t_teacher',$_INPUT['t_teacher']);
		$tpl->assign('t_experience',$_INPUT['t_experience']);
		$tpl->assign('area',$_INPUT['area']);
		$tpl->assign('level',$_INPUT['level']);
		$tpl->assign('total_point',$_INPUT['total_point']);
		$tpl->assign('total_times',$_INPUT['total_times']);
		$tpl->assign('total_money',$_INPUT['total_money']);
        $tpl->assign('price_s',$_INPUT['price_s']);
        $tpl->assign('price_e',$_INPUT['price_e']);
		
		
		$tpl->assign ('post_dataformat',$post_data);
		
        $tpl->assign('price_list_arys',$price_list_arys);
		$tpl->assign ('show_list', $show_list );
		$tpl->assign ('begin_time', $begin_time );
		$tpl->assign ('end_time', $end_time );
	break;
}
$tpl->output ();
?>