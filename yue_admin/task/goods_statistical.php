<?php

include_once 'common.inc.php';
$goods_statistical_obj = POCO::singleton('pai_mall_statistical_class');

switch($action)
{
    case "show_goods_id_uv_params_list":
        $year_date = (int)$_INPUT['year_date'];
        $goods_id = (int)$_INPUT['goods_id'];
        $list = $goods_statistical_obj->get_month_params_uv_total($year_date,$goods_id);
        
        //查看某一天的时点
        if($_INPUT['date'])
        {
            $date = $_INPUT['date'];
            $hour_ary = array();
            for($i=0;$i<=24;$i++)
            {
                if(strlen($i)==1)
                {
                    $unit = '0'.$i;
                }else
                {
                    $unit = $i;
                }
                $hour_ary[] = $unit;
            }
            
            foreach($hour_ary as $hk => $hv)
            {
                $hour_list[] = (int)$list['hour'][$_INPUT['date']][$hv];
            }
            
            $rs_str = implode(',',$hour_list);
            
            include_once (TASK_TEMPLATES_ROOT."show_goods_id_uv_hour_list_tpl.php");
        }else
        {
           include_once (TASK_TEMPLATES_ROOT."show_goods_id_uv_params_list_tpl.php");
        }
        
        
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
    case "show_goods_id_uv_log":
        $goods_id = $_INPUT['goods_id'];
        $year_date = (int)$_INPUT['year_date'];
        $add_time_s = $_INPUT['add_time_s'];
        $add_time_e = $_INPUT['add_time_e'];
        
        if(strlen($year_date) != 6)
        {
           $year_date = date('Ym');
        }
        
        //where 条件
		$where = 1;
        
        if($goods_id)
        {
            $where .= " AND goods_id='{$goods_id}'";
        }
        
        if($add_time_s && $add_time_e)
        {
            $add_s = strtotime($add_time_s);
            $add_e = strtotime($add_time_e)+86400;
            $where .= " AND v_time > '$add_s' AND v_time < '$add_e'";
        }
        
		$total_count = $goods_statistical_obj->get_uv_list(true, $where , $year_date);		
		$page_obj = new show_page ();
		$show_count = 20;
		$page_obj->setvar ( array (
            'add_time_s'=>$add_time_s,
            'add_time_e'=>$add_time_e,
            'goods_id'=>$goods_id,
            'year_date'=>$year_date,
            'action'=>'show_goods_id_uv_log',
        ) );
		$page_obj->set ( $show_count, $total_count );		
        
		$list = $goods_statistical_obj->get_uv_list(false, $where, $year_date,"id desc", $page_obj->limit());
        foreach($list as $key => &$val)
		{
            $val['v_time'] = date('Y-m-d H:i:s',$val['v_time']);
        }
        
		$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."show_goods_id_uv_log.tpl.htm" );
        
        $tpl->assign ( 'page', $page_obj->output ( 1 ) );
        $tpl->assign('goods_id',$goods_id);
        $tpl->assign('year_date',$year_date);
        $tpl->assign('add_time_s',$add_time_s);
        $tpl->assign('add_time_e',$add_time_e);
        $tpl->assign ( 'list', $list );
    break;
    case "show_ip_visit_pages_total_detail":
        $year_date = (int)$_INPUT['year_date'];
        $ip = addslashes($_INPUT['ip']);
        $list = $goods_statistical_obj->get_ip_visit_pages_total_detail($year_date,$ip);
        
        if( ! empty($list['page']) )
        {
            foreach($list['page'] as $k => $v)
            {
                $new_list[] = array('goods_id'=>$k,'value'=>$v);
            }
            
            
        }
        
        //asc是升序 desc是降序
        function arr_sort($array,$key,$order="asc")
        {
            $arr_nums=$arr=array();

            foreach($array as $k=>$v)
            {
                $arr_nums[$k]=$v[$key];
            }

            if($order=='asc')
            {
                asort($arr_nums);
            }else
            {
                arsort($arr_nums);
            }

            foreach($arr_nums as $k=>$v)
            {
                $arr[$k]=$array[$k];
            }
            
            return $arr;
        }
        
        //排序后的数组
        $new_final_list = arr_sort($new_list,'value','desc');
        
        include_once (TASK_TEMPLATES_ROOT."show_ip_visit_pages_total_detail_tpl.php");
    break;    
    case "show_ip_visit_pages_total":
        $add_time_s = $_INPUT['add_time_s'];
        $add_time_e = $_INPUT['add_time_e'];
        
        $where = " where 1 ";
        if( $add_time_s && $add_time_e )
        {
            $add_s = strtotime($add_time_s);
            $add_e = strtotime($add_time_e);
            
            if(date('Ym',$add_s) != date('Ym',$add_e) )
            {
                js_pop_msg('只能是同一个月份的搜索');
            }
            
            $where .= " and v_time >= $add_s and v_time <= $add_e ";
            
        }else
        {
            $y=date("Y",time());
            $m=date("m",time());
            $d=date("d",time());
            $t0=date('t');           // 本月一共有几天
            $add_s=mktime(0,0,0,$m,1,$y);        // 创建本月开始时间 
            $add_e=mktime(23,59,59,$m,$t0,$y);       // 创建本月结束时间
            
            if(date('Ym',$add_s) != date('Ym',$add_e) )
            {
                js_pop_msg('只能是同一个月份的搜索');
            }
            
            $add_time_s = date('Y-m-d H:i:s',$add_s);
            $add_time_e = $y.'-'.$m.'-'.$t0." 23:59:59";
        }
        $year_date = date('Ym',$add_s);
        if(strlen($year_date) != 6)
        {
           $year_date = date('Ym');
        }
        $list = $goods_statistical_obj->get_ip_visist_total($year_date,$where);
        if( ! empty($list) )
        {
            foreach($list as $k => &$v)
            {
                if($v['user_id'] == 0)
                {
                    $v['user_id'] = '--';
                }
                $v['year_date'] = $year_date;
                $v['percent'] = ($v['total']/100)."%";
            }
        }
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."show_ip_visit_pages_total.tpl.htm" );
        $tpl->assign('list',$list);
        $tpl->assign('year_date',$year_date);
        $tpl->assign('add_time_s',$add_time_s);
        $tpl->assign('add_time_e',$add_time_e);
        
    break;
    default:
        $add_time_s = $_INPUT['add_time_s'];
        $add_time_e = $_INPUT['add_time_e'];
        
        $where = " where 1 ";
        if( $add_time_s && $add_time_e )
        {
            $add_s = strtotime($add_time_s);
            $add_e = strtotime($add_time_e);
            
            if(date('Ym',$add_s) != date('Ym',$add_e) )
            {
                js_pop_msg('只能是同一个月份的搜索');
            }
            
            $where .= " and v_time >= $add_s and v_time <= $add_e ";
            
        }else
        {
            $y=date("Y",time());
            $m=date("m",time());
            $d=date("d",time());
            $t0=date('t');           // 本月一共有几天
            $add_s=mktime(0,0,0,$m,1,$y);        // 创建本月开始时间 
            $add_e=mktime(23,59,59,$m,$t0,$y);       // 创建本月结束时间
            
            if(date('Ym',$add_s) != date('Ym',$add_e) )
            {
                js_pop_msg('只能是同一个月份的搜索');
            }
            
            $add_time_s = date('Y-m-d H:i:s',$add_s);
            $add_time_e = $y.'-'.$m.'-'.$t0." 23:59:59";
        }
        
        $year_date = date('Ym',$add_s);
        
        if(strlen($year_date) != 6)
        {
           $year_date = date('Ym');
        }
        $list = $goods_statistical_obj->get_month_uv_total($year_date,$where);
        if( ! empty($list) )
        {
            foreach($list as $k => &$v)
            {
                $v['year_date'] = $year_date;
                $v['percent'] = ($v['total']/100)."%";
            }
        }
        $tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."show_goods_id_uv_month_total.tpl.htm" );
        $tpl->assign('list',$list);
        $tpl->assign('year_date',$year_date);
        $tpl->assign('add_time_s',$add_time_s);
        $tpl->assign('add_time_e',$add_time_e);
        
    break;
	
}

if(is_object($tpl) )
{
   $tpl->output ();
}

?>