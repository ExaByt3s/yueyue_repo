<?php

include_once 'common.inc.php';
$direct_order_obj = POCO::singleton('pai_mall_direct_order_class');
$task_goods_type_obj=POCO::singleton('pai_mall_goods_type_class');
$task_goods_obj = POCO::singleton('pai_mall_goods_class');


switch($action)
{
    case "info":
        
    break;
    case "show_goods_id_uv_info":
        $uv_id = (int)$_INPUT['uv_id'];
        $year_date = $_INPUT['year_date'];
        if(strlen($year_date) != 6)
        {
            $year_date = date('Ym');
        }
        $info = $goods_statistical_obj->get_uv_info($uv_id,$year_date);
        $info['v_time'] = date('Y-m-d H:i:s',$info['v_time']);
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."show_goods_id_uv_info.tpl.htm" );
        $tpl->assign('info',$info);
    break;
    case "save":
        
    break;
    case "add":       
        if($_POST)
        {     
        	 $id         =$_INPUT['id'];
        	 $time_s     =$_INPUT['time_s'];
        	 $time_e     =$_INPUT['time_e'];
        	 $num        =(int)$_INPUT['num'];
        	 $auto_sign  =$_INPUT['auto_sign'];
        	 $auto_accept=$_INPUT['auto_accept'];
             $goods_id   =(int)$_INPUT['goods_id'];
        	 $limit_times=(int)$_INPUT['limit_times'];
        	 $used_times =(int)$_INPUT['used_times']; 
             $location_id=$_INPUT['location_id']; 
             $address    =trim($_INPUT['address']);	 
             if($goods_id)
             {	 
        	    $goods_info  =$task_goods_obj->get_goods_info($goods_id);

        	    if($goods_info)
        	    {
        	       exit('<script>alert("该商品已经存在，请勿重复添加");window.location.href="direct_order.php?action=add";</script>');
        	    }
             } 
             $time_s=strtotime($time_s);
             $address=str_replace(array('<','>','"'),array('&lt;','&gt;','&quot;'),$address);
             $add_time=time();
            //组装数据
            $arr_insert=array(
            	  'id'            =>null,
            	  'title'         =>'',
            	  'type_id'       =>$id,
            	  'goods_id'      =>$goods_id,
            	  'location_id'   =>$location_id,
            	  'is_auto_accept'=>$auto_accept,	
            	  'is_auto_sign'  =>$auto_sign,	
            	  'limit_times'	  => $limit_times,  
            	  'used_times'    => $used_times,
            	  'num'           => $num,     
            	  'message'       =>'',
            	  'address'       => $address,
            	  'service_time'  =>$time_s,
                  'price_type'    =>'',
            	  'add_time'	  =>$add_time,
                  'is_del'        =>0
            );
            dump($arr_insert);
            die;
            $rs=$direct_order_obj->add_config($arr_insert);
            if($rs==false)
            {
            	exit('<script>alert("数据添加失败，请重新添加");window.location.href="direct_order.php?action=add";</script>');
            }
             else
            {
            	exit('<script>alert("添加成功");window.location.href="direct_order.php";</script>');
            } 		
        }	
    	$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."direct_order_add.tpl.htm" );
    	$list = $task_goods_type_obj->get_type_cate(2);
    	$tpl->assign ( 'list', $list );	 
    break; 	
    case "del":
        exit('no');
        $id = (int)$_INPUT['id'];
        if( ! $id )
        {
            exit('<script>alert("id不能为空;");window.location.href="direct_order.php";</script>');
        }
        //$rs = $direct_order_obj->del_config($id);
        if($rs)
        {
            exit('<script>alert("删除成功");window.location.href="direct_order.php";</script>');
        }else
        {
            exit('<script>alert("删除失败");window.location.href="direct_order.php";</script>');
        }
    break;
    case "add_goods_by_user_id":
        $user_id = $_INPUT['user_id'];
        if($_POST)
        {
            dump($_INPUT);
        }
        
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."direct_order_add_goods_by_user_id.tpl.htm" );
        
    break;
    default:
        $goods_id = $_INPUT['goods_id'];
        $type_id = $_INPUT['type_id'];
        $add_time_s = $_INPUT['add_time_s'];
        $add_time_e = $_INPUT['add_time_e'];
        $service_time_s = $_INPUT['service_time_s'];
        $service_time_e = $_INPUT['service_time_e'];
        $city = $_INPUT['city'];
        $location_id = $_INPUT['location_id'];
        
        //where 条件
		$where = 1;
        
        if($goods_id)
        {
            $where .= " AND goods_id='{$goods_id}'";
        }
        
        if($location_id)
        {
            $where .= " AND location_id='{$location_id}' ";
        }
        
        if($type_id)
        {
            $where .= " and type_id = '{$type_id}' ";
        }
        
        if($add_time_s && $add_time_e)
        {
            $add_s = strtotime($add_time_s);
            $add_e = strtotime($add_time_e)+86400;
            $where .= " AND add_time > '$add_s' AND add_time < '$add_e'";
        }
        
        if( $service_time_s && $service_time_e )
        {
            $servcie_s = strtotime($service_time_s);
            $service_e = strtotime($service_time_e)+86400;
            $where .= " AND service_time > '$service_s' and service_time < '$service_e'";
        }
        
		$total_count = $direct_order_obj->get_config_list(true, $where);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array (
            'add_time_s'=>$add_time_s,
            'add_time_e'=>$add_time_e,
            'servcie_time_s'=>$service_time_s,
            'service_time_e'=>$service_time_e,
            'goods_id'=>$goods_id,
            'type_id'=>$type_id,
            'city'=>$city,
            'location_id'=>$location_id,
        ) );
		$page_obj->set ( $show_count, $total_count );		
        
		$list = $direct_order_obj->get_config_list(false, $where,"id desc", $page_obj->limit());
        
        foreach($list as $key => &$val)
		{
            $val['service_time'] = date('Y-m-d H:i:s',$val['service_time']);
            $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
            $val['location_name'] = $val['location_id'] ? get_poco_location_name_by_location_id($val['location_id']) : '--';
        }
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."direct_order_list.tpl.htm" );
        
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
        $tpl->assign('goods_id',$goods_id);
        $tpl->assign('type_id',$type_id);
        $tpl->assign('city',$city);
        $tpl->assign('location_id',$location_id);
        $tpl->assign('add_time_s',$add_time_s);
        $tpl->assign('add_time_e',$add_time_e);
        $tpl->assign('service_time_s',$service_time_s);
        $tpl->assign('service_time_e',$service_time_e);
        $tpl->assign ( 'list', $list );
    break;
	
}

$tpl->output ();


?>