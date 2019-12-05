<?php 

/* 
 *模特拓展
 *xiao xiao
 * 2014-2-13
*/
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $model_report_obj = POCO::singleton('pai_model_report_class');


  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $tpl = new SmartTemplate("model_report_list.tpl.htm");
  $month          = $_INPUT['month'] ? $_INPUT['month'] : date('Y-m', time()-24*3600);

  $tablename = $model_report_obj->get_tablename_by_month($month);
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
     $total_count = $model_report_obj->get_model_report_list($tablename, true, $where_str);
     //列表
     if ($act == 'list') 
     {
      /* $page_obj->setvar (
        array
       (
           'start_add_time' => $start_add_time,
           'end_add_time'   => $end_add_time,
           'add_id'         => $add_id,
           'type'           => $type
       )
       );*/
       //$page_obj->set ( $show_count, $total_count );
       $list = $model_report_obj->get_model_report_list($tablename, false , $where_str, 'add_time DESC,id DESC', "0,{$total_count}", '*');
       $tmp_month =  date('Ym', strtotime($month) );
       $sql_str = "SELECT * FROM yueyue_stat_db.model_business_tbl_{$tmp_month} ORDER BY add_time DESC";
       $business_list = db_simple_getdata($sql_str, FALSE, 22);

        if ($list)
       {
           $ret = array();
           $regedit_uv_up    = 0;
           $audit_pass_up    = 0;
           $audit_no_pass_up = 0;
           $audit_wait       = 0;
           $login_uv_up      = 0;
           $login_pv_up      = 0;
           $pay_success_up   = 0;
           $cancel_up        = 0;
           $confirm_up       = 0;
           $model_agree_up   = 0;
           $model_refuse_up  = 0;
           $order_cancel_up  = 0;
           $order_completion_up = 0;
           //日均
           $i = 0;
           foreach ($list as $key => $vo) 
           {
              $list[$key]['add_time']      = date('m月d日', strtotime($vo['add_time']));
              $regedit_uv_up    += $vo['regedit_uv'];
              $audit_pass_up    += $vo['audit_pass'];
              $audit_no_pass_up += $vo['audit_no_pass'];
              $audit_wait_up    += $vo['audit_wait'];
              $login_uv_up      += $vo['login_uv'];
              $login_pv_up      += $vo['login_pv'];
              //$pay_success_up   += $vo['pay_success'];
             /* $cancel_up        += $vo['cancel'];
              $confirm_up       += $vo['confirm'];*/

               $list[$key]['pay_success_uv']  = $business_list[$key]['pay_success_uv'];
               $list[$key]['pay_success']  = $business_list[$key]['pay_success'];
               $list[$key]['model_agree']  = $business_list[$key]['model_agree'];
               $list[$key]['model_refuse']  = $business_list[$key]['model_refuse'];
               $list[$key]['order_cancel']  = $business_list[$key]['order_cancel'];
               $list[$key]['order_completion']  = $business_list[$key]['order_completion'];
               $pay_success_up      += $business_list[$key]['pay_success'];
               $pay_success_uv_up   += $business_list[$key]['pay_success_uv'];
               $model_agree_up      +=  $business_list[$key]['model_agree'];
               $model_refuse_up     +=  $business_list[$key]['model_refuse'];
               $order_cancel_up     +=  $business_list[$key]['order_cancel'];
               $order_completion_up +=  $business_list[$key]['order_completion'];
               $i++;
           }
           $ret['regedit_uv_up']       = $regedit_uv_up;
           $ret['audit_pass_up']       = $audit_pass_up;
           $ret['audit_no_pass_up']    = $audit_no_pass_up;
           $ret['audit_wait_up']       = $audit_wait_up;
           $ret['login_uv_up']         = $login_uv_up;
           $ret['login_pv_up']         = $login_pv_up;
           $ret['pay_success_up']      = $pay_success_up;
           $ret['pay_success_uv_up']   = $pay_success_uv_up;
          /* $ret['cancel_up']           = $cancel_up;
           $ret['confirm_up']          = $confirm_up;*/
           $ret['model_agree_up']      = $model_agree_up;
           $ret['model_refuse_up']     = $model_refuse_up;
           $ret['order_cancel_up']     = $order_cancel_up;
           $ret['order_completion_up'] = $order_completion_up;
           //echo $i;
           //日均
           $ret['regedit_uv_day']    = sprintf('%.2f',$regedit_uv_up/$i);
           $ret['audit_pass_day']    = sprintf('%.2f',$audit_pass_up/$i);
           $ret['audit_no_pass_day'] = sprintf('%.2f',$audit_no_pass_up/$i);
           $ret['audit_wait_day']    = sprintf('%.2f',$audit_wait_up/$i);
           $ret['login_uv_day']      = sprintf('%.2f',$login_uv_up/$i);
           $ret['login_pv_day']      = sprintf('%.2f',$login_pv_up/$i);
           $ret['pay_success_day']   = sprintf('%.2f',$pay_success_up/$i);
           $ret['pay_success_uv_day']   = sprintf('%.2f',$pay_success_uv_up/$i);
          /* $ret['cancel_day']        = sprintf('%.2f',$cancel_up/$i);
           $ret['confirm_day']       = sprintf('%.2f',$confirm_up/$i);*/
           $ret['model_agree_day']   = sprintf('%.2f',$model_agree_up/$i);
           $ret['model_refuse_day']   = sprintf('%.2f',$model_refuse_up/$i);
           $ret['order_cancel_day']   = sprintf('%.2f',$order_cancel_up/$i);
           $ret['order_completion_day']   = sprintf('%.2f',$order_completion_up/$i);
       }
     }
     //导出
     elseif ($act == 'export') 
     {
       $list = $model_report_obj->get_model_report_list($tablename, false , $where_str, 'add_time DESC', "0,{$total_count}", '*');
       $sql_str = "SELECT * FROM yueyue_stat_db.model_business_tbl_{$tmp_month} ORDER BY add_time DESC";
       $business_list = db_simple_getdata($sql_str, FALSE, 22);
       $data = array();
       if ($list) 
       {
          $i = 0;
          foreach ($list as $key => $vo) 
          {
             $data[$key]['add_time']         = date('m月d日', strtotime($vo['add_time']));
             $data[$key]['regedit_uv']       = $vo['regedit_uv'];
             $data[$key]['audit_pass']       = $vo['audit_pass'];
             $data[$key]['audit_no_pass']    = $vo['audit_no_pass'];
             $data[$key]['audit_wait']       = $vo['audit_wait'];
             $data[$key]['login_uv']         = $vo['login_uv'];
             $data[$key]['login_pv']         = $vo['login_pv'];
             $data[$key]['pay_success_uv']   = $business_list[$key]['pay_success_uv'];
             $data[$key]['pay_success']      = $business_list[$key]['pay_success'];
             $data[$key]['model_agree']      = $business_list[$key]['model_agree'];
             $data[$key]['model_refuse']     = $business_list[$key]['model_refuse'];
             $data[$key]['order_cancel']     = $business_list[$key]['order_cancel'];
             $data[$key]['order_completion'] = $business_list[$key]['order_completion'];
             //累计
             $regedit_uv_up       += $vo['regedit_uv'];
             $audit_pass_up       += $vo['audit_pass'];
             $audit_no_pass_up    += $vo['audit_no_pass'];
             $audit_wait_up       += $vo['audit_wait'];
             $login_uv_up         += $vo['login_uv'];
             $login_pv_up         += $vo['login_pv'];
             $pay_success_uv_up   += $business_list[$key]['pay_success_uv'];
             $pay_success_up      += $business_list[$key]['pay_success'];
             $model_agree_up      += $business_list[$key]['model_agree'];
             $model_refuse_up     += $business_list[$key]['model_refuse'];
             $order_cancel_up     += $business_list[$key]['order_cancel'];
             $order_completion_up += $business_list[$key]['order_completion'];
             $i++;
          }
           $ret['regedit_uv_up']       = $regedit_uv_up;
           $ret['audit_pass_up']       = $audit_pass_up;
           $ret['audit_no_pass_up']    = $audit_no_pass_up;
           $ret['audit_wait_up']       = $audit_wait_up;
           $ret['login_uv_up']         = $login_uv_up;
           $ret['login_pv_up']         = $login_pv_up;
           $ret['pay_success_uv_up']   = $pay_success_uv_up;
           $ret['pay_success_up']      = $pay_success_up;
           $ret['model_agree_up']      = $model_agree_up;
           $ret['model_refuse_up']     = $model_refuse_up;
           $ret['order_cancel_up']     = $order_cancel_up;
           $ret['order_completion_up'] = $order_completion_up;
          //日均
           $ret_avg['regedit_uv_day']       = sprintf('%.2f',$regedit_uv_up/$i);
           $ret_avg['audit_pass_day']       = sprintf('%.2f',$audit_pass_up/$i);
           $ret_avg['audit_no_pass_day']    = sprintf('%.2f',$audit_no_pass_up/$i);
           $ret_avg['audit_wait_day']       = sprintf('%.2f',$audit_wait_up/$i);
           $ret_avg['login_uv_day']         = sprintf('%.2f',$login_uv_up/$i);
           $ret_avg['login_pv_day']         = sprintf('%.2f',$login_pv_up/$i);
           $ret_avg['pay_success_uv_day']   = sprintf('%.2f',$pay_success_uv_up/$i);
           $ret_avg['pay_success_day']      = sprintf('%.2f',$pay_success_up/$i);
           $ret_avg['model_agree_day']      = sprintf('%.2f',$model_agree_up/$i);
           $ret_avg['model_refuse_day']     = sprintf('%.2f',$model_refuse_up/$i);
           $ret_avg['order_cancel_day']     = sprintf('%.2f',$order_cancel_up/$i);
           $ret_avg['order_completion_day'] = sprintf('%.2f',$order_completion_up/$i);
           $fileName = "模特拓展报表";
           $title    = "模特拓展";
          //var_dump($data);
           $headArr = array('日期', '[商品]模特注册uv', '[商品]审核通过uv', '[商品]审核不通过uv','[商品]总计待审核uv', '[商品]模特登录uv', '[商品]模特登录pv','[用户]支付成功uv','[系统]订单生成pv','[商品]模特同意pv','[商品]模特拒绝uv','[系统]退款订单pv','[系统]订单完成pv');
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