<?php

/**
 * 
 * @authors xiaoxiao (xiaojm@yueus.com)
 * @date    2015-02-26 09:18:52
 * @version 1
 *
 * 信息回复报表
 */
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $response_reply_obj = POCO::singleton('pai_response_reply_day_class');
  $page_obj               = new show_page ();
  $show_count             = 30;
  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $tpl = new SmartTemplate("response_reply_list.tpl.htm");
  $month          = $_INPUT['month'] ? $_INPUT['month'] : date('Y-m', time()-24*3600);
  //$month          = '2015-02';
  $tablename = $response_reply_obj->get_tablename_by_month($month);
  if(!$tablename){echo "该月份暂未出数据";exit;}
  $where_str = "user_id>100000 GROUP BY add_time";
  $list = $response_reply_obj->get_replay_list_v2($tablename ,false, $where_str, 'add_time DESC,5i DESC,10i DESC', '0,30', 'add_time,SUM(5i) as 5i,SUM(10i) as 10i,SUM(20i) as 20i,SUM(30i) as 30i,SUM(1h) as 1h,SUM(12h) as 12h,SUM(24h) as 24h,SUM(no_response) as no_response');
  if (!is_array($list) || empty($list)) {return false;}

  //列表
  if ($act == 'list') 
  {
  	$ret = array();
  	$i5_up    = 0;
  	$i10_up   = 0;
  	$i20_up   = 0;
  	$i30_up   = 0;
  	$h1_up    = 0;
  	$h12_up   = 0;
  	$h24_up   = 0;
  	$no_response_up = 0;
  	$pre_reply_up = 0;
  	$i = 0;
    $i5_up  = 0;
    $i10_up = 0;
    $i20_up = 0;
    $i30_up = 0;
    $h1_up  = 0;
    $h12_up = 0;
    $h24_up = 0;
    $no_response_up = 0;
  	foreach ($list as $key => $vo) 
  	{
  		//echo date('Y年m月', strtotime($vo['add_time']));
  		$list[$key]['add_time'] = date('m月d日', strtotime($vo['add_time']));
  		$total_count = $vo['5i'] + $vo['10i'] + $vo['20i'] + $vo['30i'] + $vo['1h'] + $vo['12h'] + $vo['24h'] + $vo['no_response'];
        $list[$key]['pre_5i']  = sprintf('%.2f',($vo['5i']/$total_count)*100);
        $list[$key]['pre_10i'] = sprintf('%.2f',($vo['10i']/$total_count)*100);
        $list[$key]['pre_20i'] = sprintf('%.2f',($vo['20i']/$total_count)*100);
        $list[$key]['pre_30i'] = sprintf('%.2f',($vo['30i']/$total_count)*100);
        $list[$key]['pre_1h']  = sprintf('%.2f',($vo['1h']/$total_count)*100);
        $list[$key]['pre_12h'] = sprintf('%.2f',($vo['12h']/$total_count)*100);
        $list[$key]['pre_24h'] = sprintf('%.2f',($vo['24h']/$total_count)*100);
        $list[$key]['pre_no_response'] = sprintf('%.2f',($vo['no_response']/$total_count)*100);
  		$list[$key]['pre_reply'] = sprintf('%.2f', (1-$vo['no_response']/$total_count)*100);
        //$list[$key]['pre_5i']
        $i5_up          += $vo['5i'];
        $i10_up         += $vo['10i'];
        $i20_up         += $vo['20i'];
        $i30_up         += $vo['30i'];
        $h1_up          += $vo['1h'];
        $h12_up         += $vo['12h'];
        $h24_up         += $vo['24h'];
        $no_response_up += $vo['no_response'];
        $pre_5i_up          += sprintf('%.2f',($vo['5i']/$total_count)*100);
        $pre_10i_up         += sprintf('%.2f',($vo['10i']/$total_count)*100);
        $pre_20i_up         += sprintf('%.2f',($vo['20i']/$total_count)*100);
        $pre_30i_up         += sprintf('%.2f',($vo['30i']/$total_count)*100);
        $pre_1h_up          += sprintf('%.2f',($vo['1h']/$total_count)*100);
        $pre_12h_up         += sprintf('%.2f',($vo['12h']/$total_count)*100);
        $pre_24h_up         += sprintf('%.2f',($vo['24h']/$total_count)*100);
        $pre_no_response_up += sprintf('%.2f',($vo['no_response']/$total_count)*100);
        //总比
        $pre_reply_up   += sprintf('%.2f', (1-$vo['no_response']/$total_count)*100);
        $i++;
  	}
    $ret['5i_up']          = $i5_up;
    $ret['10i_up']         = $i10_up;
    $ret['20i_up']         = $i20_up;
    $ret['30i_up']         = $i30_up;
    $ret['1h_up']          = $h1_up;
    $ret['12h_up']         = $h12_up;
    $ret['24h_up']         = $h24_up;
    $ret['no_response_up'] = $no_response_up;

    $ret['pre_5i_up']          = $pre_5i_up;
    $ret['pre_10i_up']         = $pre_10i_up;
    $ret['pre_20i_up']         = $pre_20i_up;
    $ret['pre_30i_up']         = $pre_30i_up;
    $ret['pre_1h_up']          = $pre_1h_up;
    $ret['pre_12h_up']         = $pre_12h_up;
    $ret['pre_24h_up']         = $pre_24h_up;
    $ret['pre_no_response_up'] = $pre_no_response_up;
    //总比
    $ret['pre_reply_up'] = $pre_reply_up;
	  //日均
    $ret['5i_day']          = sprintf('%.2f',$i5_up/$i);
    $ret['10i_day']         = sprintf('%.2f',$i10_up/$i);
    $ret['20i_day']         = sprintf('%.2f',$i20_up/$i);
    $ret['30i_day']         = sprintf('%.2f',$i30_up/$i);
    $ret['1h_day']          = sprintf('%.2f',$h1_up/$i);
    $ret['12h_day']         = sprintf('%.2f',$h12_up/$i);
    $ret['24h_day']         = sprintf('%.2f',$h24_up/$i);	
    $ret['no_response_day'] = sprintf('%.2f',$no_response_up/$i);

    $ret['pre_5i_day']          = sprintf('%.2f',$pre_5i_up/$i);
    $ret['pre_10i_day']         = sprintf('%.2f',$pre_10i_up/$i);
    $ret['pre_20i_day']         = sprintf('%.2f',$pre_20i_up/$i);
    $ret['pre_30i_day']         = sprintf('%.2f',$pre_30i_up/$i);
    $ret['pre_1h_day']          = sprintf('%.2f',$pre_1h_up/$i);
    $ret['pre_12h_day']         = sprintf('%.2f',$pre_12h_up/$i);
    $ret['pre_24h_day']         = sprintf('%.2f',$pre_24h_up/$i);
    $ret['pre_no_response_day'] = sprintf('%.2f',$pre_no_response_up/$i);
    //总比
    $ret['pre_reply_day']   = sprintf('%.2f',$pre_reply_up/$i);	
  }
  elseif ($act == 'export') 
  {
  	  	//$ret = array();
  	  	$i5_up    = 0;
  	  	$i10_up   = 0;
  	  	$i20_up   = 0;
  	  	$i30_up   = 0;
  	  	$h1_up    = 0;
  	  	$h12_up   = 0;
  	  	$h24_up   = 0;
  	  	$no_response_up = 0;
  	  	$pre_reply_up = 0;
  	  	$i = 0;
        $data = array();
  	  	foreach ($list as $key => $vo) 
  	  	{
  	  		//echo date('Y年m月', strtotime($vo['add_time']));
  	  		$data[$key]['add_time'] = date('m月d日', strtotime($vo['add_time']));
  	  		$total_count = $vo['5i'] + $vo['10i'] + $vo['20i'] + $vo['30i'] + $vo['1h'] + $vo['12h'] + $vo['24h'] + $vo['no_response'];
            $data[$key]['pre_5i']  = $vo['5i'].'('.sprintf('%.2f',($vo['5i']/$total_count)*100).'%)';
            $data[$key]['pre_10i'] = $vo['10i'].'('.sprintf('%.2f',($vo['10i']/$total_count)*100).'%)';
            $data[$key]['pre_20i'] = $vo['20i'].'('.sprintf('%.2f',($vo['20i']/$total_count)*100).'%)';
            $data[$key]['pre_30i'] = $vo['30i'].'('.sprintf('%.2f',($vo['30i']/$total_count)*100).'%)';
            $data[$key]['pre_1h']  = $vo['1h'].'('.sprintf('%.2f',($vo['1h']/$total_count)*100).')';
            $data[$key]['pre_12h'] = $vo['12h'].'('.sprintf('%.2f',($vo['12h']/$total_count)*100).'%)';
            $data[$key]['pre_24h'] = $vo['24h'].'('.sprintf('%.2f',($vo['24h']/$total_count)*100).'%)';
            $data[$key]['pre_no_response'] = $vo['no_response'].'('.sprintf('%.2f',($vo['no_response']/$total_count)*100).'%)';
  	  		$data[$key]['pre_reply'] = sprintf('%.2f', (1-$vo['no_response']/$total_count)*100).'%';
            $i5_up          += $vo['5i'];
            $i10_up         += $vo['10i'];
            $i20_up         += $vo['20i'];
            $i30_up         += $vo['30i'];
            $h1_up          += $vo['1h'];
            $h12_up         += $vo['12h'];
            $h24_up         += $vo['24h'];
            $no_response_up += $vo['no_response'];
            //百分比
            $pre_5i_up          += sprintf('%.2f',($vo['5i']/$total_count)*100);
            $pre_10i_up         += sprintf('%.2f',($vo['10i']/$total_count)*100);
            $pre_20i_up         += sprintf('%.2f',($vo['20i']/$total_count)*100);
            $pre_30i_up         += sprintf('%.2f',($vo['30i']/$total_count)*100);
            $pre_1h_up          += sprintf('%.2f',($vo['1h']/$total_count)*100);
            $pre_12h_up         += sprintf('%.2f',($vo['12h']/$total_count)*100);
            $pre_24h_up         += sprintf('%.2f',($vo['24h']/$total_count)*100);
            $pre_no_response_up += sprintf('%.2f',($vo['no_response']/$total_count)*100);
            //总比
            $pre_reply_up   += sprintf('%.2f', (1-$vo['no_response']/$total_count)*100);
            $i++;
  	  	}
        $ret['5i_up']          = $i5_up."(".$pre_5i_up."%)";
        $ret['10i_up']         = $i10_up."(".$pre_10i_up."%)";
        $ret['20i_up']         = $i20_up."(".$pre_20i_up."%)";
        $ret['30i_up']         = $i30_up."(".$pre_30i_up."%)";
        $ret['1h_up']          = $h1_up."(".$pre_1h_up."%)";
        $ret['12h_up']         = $h12_up."(".$pre_12h_up."%)";
        $ret['24h_up']         = $h24_up."(".$pre_24h_up."%)";
        $ret['no_response_up'] = $no_response_up."(".$pre_5i_up."%)";
        $ret['pre_reply_up']   = '/';
  		  //日均
  		  $ret_avg['5i_day']          = sprintf('%.2f',$i5_up/$i)."(".sprintf('%.2f',$pre_5i_up/$i)."%)";
  		  $ret_avg['10i_day']         = sprintf('%.2f',$i10_up/$i)."(".sprintf('%.2f',$pre_10i_up/$i)."%)";
  		  $ret_avg['20i_day']         = sprintf('%.2f',$i20_up/$i)."(".sprintf('%.2f',$pre_20i_up/$i)."%)";
  		  $ret_avg['30i_day']         = sprintf('%.2f',$i30_up/$i)."(".sprintf('%.2f',$pre_30i_up/$i)."%)";
  		  $ret_avg['1h_day']          = sprintf('%.2f',$h1_up/$i)."(".sprintf('%.2f',$pre_1h_up/$i)."%)";
  		  $ret_avg['12h_day']         = sprintf('%.2f',$h12_up/$i)."(".sprintf('%.2f',$pre_12h_up/$i)."%)";
  		  $ret_avg['24h_day']         = sprintf('%.2f',$h24_up/$i)."(".sprintf('%.2f',$pre_24h_up/$i)."%)";	
  		  $ret_avg['no_response_day'] = sprintf('%.2f',$no_response_up/$i)."(".sprintf('%.2f',$pre_no_response_up/$i)."%)";	
  		  $ret_avg['pre_reply_day']   = sprintf('%.2f',$pre_reply_up/$i).'%';	
  		  $fileName = "信息回复报表";
        $title    = "信息回复报表";
  		  $headArr = array('日期', '5分钟回复', '10分钟回复', '20分钟回复', '30分钟回复', '1小时回复','12小时回复','24小时回复','无回复','回复百分比');
        getExcel($fileName,$title,$headArr,$data,$ret,$ret_avg);
        exit;
  }
  
  $tpl->assign('list', $list);
  $tpl->assign('month', $month);
  $tpl->assign($ret);
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();