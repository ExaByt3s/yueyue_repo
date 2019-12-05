<?php

/**
 *举报控制器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 11:09:14
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("inform_list.tpl.htm");
 //举报类
 $log_inform_obj = POCO::singleton('pai_log_inform_class');
//屏蔽类
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');
 $page_obj = new show_page ();
 $show_count = 20;
 $start_time  = $_INPUT['start_time'] ? $_INPUT['start_time'] : '';
 $end_time    = $_INPUT['end_time']   ? $_INPUT['end_time'] : '';
 $to_informer = $_INPUT['to_informer'] ? (int)$_INPUT['to_informer'] : '';
 $by_informer = $_INPUT['by_informer'] ? (int)$_INPUT['by_informer'] : '';
 $where_str = "1";
 if ($start_time) 
 {
 	$where_str .= " AND DATE_FORMAT(add_time,'%Y-%m-%d') >= '{$start_time}'";
 }
 if ($end_time) 
 {
 	$where_str .= " AND DATE_FORMAT(add_time,'%Y-%m-%d') <= '{$end_time}'";
 }
 if ($to_informer) 
 {
 	$where_str .= " AND to_informer= {$to_informer}";
 }
 if ($by_informer) 
 {
 	$where_str .= " AND by_informer= {$by_informer}";
 }

 $total_count = $log_inform_obj->get_inform_list(true, $where_str);
 $page_obj->setvar (
        array
        (
          'start_time'  => $start_time,
          'end_time'    => $end_time,
          'to_informer' => $to_informer,
          'by_informer' => $by_informer
          )
      );
 $page_obj->set ( $show_count, $total_count );
 $list = $log_inform_obj->get_inform_list(false,$where_str,"id DESC", $page_obj->limit());
 if (is_array($list)) 
 {
    foreach ($list as $key => $vo) 
    {
      //$list[$key]['desc'] = poco_cutstr($vo['data_str'], 40, '....');
      $list[$key]['info'] = $inform_shield_obj->get_info_by_user_id($vo['to_informer']);
    }

 }
 $tpl->assign('start_time', $start_time);
 $tpl->assign('end_time', $end_time);
 $tpl->assign('to_informer', $to_informer);
 $tpl->assign('by_informer', $by_informer);
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
