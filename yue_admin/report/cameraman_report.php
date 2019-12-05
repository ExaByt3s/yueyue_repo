<?php 

/* 
 *摄影师拓展拓展
 *xiao xiao
 * 2014-2-16
*/
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
  $cameraman_report_obj = POCO::singleton('pai_cameraman_report_class');
  /*$page_obj               = new show_page ();
  $show_count             = 30;*/
  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  //echo time();
  $tpl = new SmartTemplate("cameraman_report_list.tpl.htm");

  $month          = $_INPUT['month'] ? $_INPUT['month'] : date('Y-m', time()-24*3600);

  $tablename = $cameraman_report_obj->get_tablename_by_month($month);
  if ($tablename) 
  {
     $where_str = "1";
     /*if ($start_add_time && $end_add_time) 
     {
        $where_str .= " AND add_time BETWEEN '{$start_add_time}' AND '{$end_add_time}'";
     }*/
     if ($add_id) 
     {
        $where_str .= " AND add_id = {$add_id}";
     }
     $total_count = $cameraman_report_obj->get_cameraman_report_list($tablename, true, $where_str);
     //列表
     if ($act == 'list') 
     {

       $list = $cameraman_report_obj->get_cameraman_report_list($tablename, false , $where_str, 'add_time DESC,id DESC', "0,{$total_count}", '*');
       if ($list) 
       {
           $ret = array();
           $regedit_uv_total_up     = 0;
           $app_regedit_up          = 0;
           $weixin_regedit_up       = 0;
           $pc_regedit_up           = 0;
           $same_reg_msg_uv_up      = 0;
           $msg_uv_up               = 0;
           $first_place_order_uv_up = 0;
           $first_statement_uv_up   = 0;
           //日均
           $i = 0;
           foreach ($list as $key => $vo) 
           {
               $regedit_uv_total = (int)($vo['app_regedit']+$vo['weixin_regedit']+$vo['pc_regedit']);
               $list[$key]['add_time']      = date('m月d日', strtotime($vo['add_time']));
               //$regedit_uv_total_up    += $vo['regedit_uv'];
               $list[$key]['regedit_uv_total'] = $regedit_uv_total;
               $regedit_uv_total_up     += $regedit_uv_total;
               $app_regedit_up          += $vo['app_regedit'];
               $weixin_regedit_up       += $vo['weixin_regedit'];
               $pc_regedit_up           += $vo['pc_regedit'];
               $same_reg_msg_uv_up      += $vo['same_reg_msg_uv'];
               $msg_uv_up               += $vo['msg_uv'];
               $first_place_order_uv_up += $vo['first_place_order_uv'];
               $first_statement_uv_up   += $vo['first_statement_uv'];
               $i++;
           }
           $ret['regedit_uv_total_up']     = $regedit_uv_total_up;
           $ret['app_regedit_up']          = $app_regedit_up;
           $ret['weixin_regedit_up']       = $weixin_regedit_up;
           $ret['pc_regedit_up']           = $pc_regedit_up;
           $ret['same_reg_msg_uv_up']      = $same_reg_msg_uv_up;
           $ret['msg_uv_up']               = $msg_uv_up;
           $ret['first_place_order_uv_up'] = $first_place_order_uv_up;
           $ret['first_statement_uv_up']   = $first_statement_uv_up;
           //echo $i;
           //日均
           $ret['regedit_uv_total_day']     = sprintf('%.2f',$regedit_uv_total_up/$i);
           $ret['app_regedit_day']          = sprintf('%.2f',$app_regedit_up/$i);
           $ret['weixin_regedit_day']       = sprintf('%.2f',$weixin_regedit_up/$i);
           $ret['pc_regedit_day']           = sprintf('%.2f',$pc_regedit_up/$i);
           $ret['same_reg_msg_uv_day']      = sprintf('%.2f',$same_reg_msg_uv_up/$i);
           $ret['msg_uv_day']               = sprintf('%.2f',$msg_uv_up/$i);
           $ret['first_place_order_uv_day'] = sprintf('%.2f',$first_place_order_uv_up/$i);
           $ret['first_statement_uv_day']   = sprintf('%.2f',$first_statement_uv_up/$i);
       }
     }
     //导出
     elseif ($act == 'export') 
     {
       $list = $cameraman_report_obj->get_cameraman_report_list($tablename, false , $where_str, 'add_time DESC', "0,{$total_count}", '*');
       $data = array();
       if ($list) 
       {
          $i = 0;
          foreach ($list as $key => $vo) 
          {
             $data[$key]['add_time']             = date('m月d日', strtotime($vo['add_time']));
             $regedit_uv_total                   = (int)($vo['app_regedit']+$vo['weixin_regedit']+$vo['pc_regedit']);
             $data[$key]['regedit_uv_total']     = $regedit_uv_total;
             $data[$key]['app_regedit']          = $vo['app_regedit'];
             $data[$key]['weixin_regedit']       = $vo['weixin_regedit'];
             $data[$key]['pc_regedit']           = $vo['pc_regedit'];
             $data[$key]['same_reg_msg_uv']      = $vo['same_reg_msg_uv'];
             $data[$key]['msg_uv']               = $vo['msg_uv'];
             $data[$key]['first_place_order_uv'] = $vo['first_place_order_uv'];
             $data[$key]['first_statement_uv']   = $vo['first_statement_uv'];
             //累计
             $regedit_uv_total_up     += $regedit_uv_total;
             $app_regedit_up          += $vo['app_regedit'];
             $weixin_regedit_up       += $vo['weixin_regedit'];
             $pc_regedit_up           += $vo['pc_regedit'];
             $same_reg_msg_uv_up      += $vo['same_reg_msg_uv'];
             $msg_uv_up               += $vo['msg_uv'];
             $first_place_order_uv_up += $vo['first_place_order_uv'];
             $first_statement_uv_up   += $vo['first_statement_uv'];
             $i++;
          }
           $ret['regedit_uv_total_up']     = $regedit_uv_total_up;
           $ret['app_regedit_up']          = $app_regedit_up;
           $ret['weixin_regedit_up']       = $weixin_regedit_up;
           $ret['pc_regedit_up']           = $pc_regedit_up;
           $ret['same_reg_msg_uv_up']      = $same_reg_msg_uv_up;
           $ret['msg_uv_up']               = $msg_uv_up;
           $ret['first_place_order_uv_up'] = $first_place_order_uv_up;
           $ret['first_statement_uv_up']   = $first_statement_uv_up;
           //日均
           $ret_avg['regedit_uv_total_day']     = sprintf('%.2f',$regedit_uv_total_up/$i);
           $ret_avg['app_regedit_day']          = sprintf('%.2f',$app_regedit_up/$i);
           $ret_avg['weixin_regedit_day']       = sprintf('%.2f',$weixin_regedit_up/$i);
           $ret_avg['pc_regedit_day']           = sprintf('%.2f',$pc_regedit_up/$i);
           $ret_avg['same_reg_msg_uv_day']      = sprintf('%.2f',$same_reg_msg_uv_up/$i);
           $ret_avg['msg_uv_day']               = sprintf('%.2f',$msg_uv_up/$i);
           $ret_avg['first_place_order_uv_day'] = sprintf('%.2f',$first_place_order_uv_up/$i);
           $ret_avg['first_statement_uv_day']   = sprintf('%.2f',$first_statement_uv_up/$i);
           $fileName = "摄影师拓展报表";
           $title    = "摄影师拓展";
          //var_dump($data);
           $headArr = array('日期', '[[用户]注册uv','[用户]app注册uv', '[用户]微信注册uv','[用户]PC注册uv', '[用户]私聊uv(多注册用户有发起私聊)', '[用户]私聊uv(发起私聊模特的次数)', '[用户]支付成功uv(下单uv)', '[用户]签到-点击uv(订单完成uv)');
          getExcel($fileName,$title,$headArr,$data,$ret,$ret_avg);
          exit;
       }
     }
  }
  //echo $month;
  //print_r($ret);exit;
  $tpl->assign($ret);
  $tpl->assign('list', $list);
  $tpl->assign('month', $month);
  /*$tpl->assign('start_add_time', $start_add_time);
  $tpl->assign('end_add_time', $end_add_time);*/
  $tpl->assign('add_id', $add_id);
  $tpl->assign('type', $type);
  $tpl->assign('total_count', $total_count);
  //$tpl->assign ( "page", $page_obj->output ( 1 ) );
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>