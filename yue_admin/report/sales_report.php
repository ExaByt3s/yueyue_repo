<?php 

/* 
 *销售概况
 *xiao xiao
 * 2014-2-11
*/
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  include('common.inc.php');
  include('include/common_function.php');
  $sales_report_obj = POCO::singleton('pai_sales_report_class');

  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  //echo time();
  $tpl = new SmartTemplate("sale_report_list.tpl.htm");

  $month          = $_INPUT['month'] ? $_INPUT['month'] : date('Y-m', time()-24*3600);


  $tablename = $sales_report_obj->get_tablename_by_month($month);
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
     $total_count = $sales_report_obj->get_sale_report_report_list($tablename, true, $where_str);
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
       $list = $sales_report_obj->get_sale_report_report_list($tablename, false , $where_str, 'add_time DESC', "0,{$total_count}", '*');
       if ($list) 
       {
           $ret = array();
           $order_total_up   = 0;
           $order_num_up     = 0;
           $order_avg_up     = 0;
           $examine_uv_up    = 0;
           $sold_uv_up       = 0;
           $normal_refund_up = 0;
           $force_refund_up  = 0;
           //日均
           $i = 0;
           foreach ($list as $key => $vo) 
           {
              $list[$key]['add_time']  = date('m月d日', strtotime($vo['add_time']));
              $order_avg = sprintf('%.2f', $vo['order_total']/$vo['order_num']);
              $list[$key]['order_avg']  = $order_avg;
              $list[$key]['sold_per']   = sprintf('%.4f' ,$vo['sold_uv']/$vo['examine_uv'])*100;
              $order_total_up          += $vo['order_total'];
              $order_num_up            += $vo['order_num'];
              $order_avg_up            += $order_avg;
              $examine_uv_up           += $vo['examine_uv'];
              $sold_uv_up              += $vo['sold_uv'];
              $normal_refund_up        += $vo['normal_refund'];
              $force_refund_up         += $vo['force_refund'];
              $i++;
           }
           $ret['order_total_up']   = $order_total_up;
           $ret['order_num_up']     = $order_num_up;
           $ret['order_avg_up']     = $order_avg_up;
           $ret['examine_uv_up']    = $examine_uv_up;
           $ret['sold_uv_up']       = $sold_uv_up;
           $ret['normal_refund_up'] = $normal_refund_up;
           $ret['force_refund_up']  = $force_refund_up;
           $ret['sold_per_up']    = sprintf('%.4f' ,$sold_uv_up/$examine_uv_up)*100;
           //echo $i;
           //日均
           $ret['order_total_day']   = sprintf('%.2f',$order_total_up/$i);
           $ret['order_num_day']     = sprintf('%.2f',$order_num_up/$i);
           $ret['order_avg_day']     = sprintf('%.2f',$order_avg_up/$i);
           $ret['examine_uv_day']    = sprintf('%.2f',$examine_uv_up/$i);
           $ret['sold_uv_day']       = sprintf('%.2f',$sold_uv_up/$i);
           $ret['normal_refund_day'] = sprintf('%.2f',$normal_refund_up/$i);
           $ret['force_refund_day']  = sprintf('%.2f',$force_refund_up/$i);
           $ret['sold_per_day']    = sprintf('%.4f' ,$ret['sold_uv_day']/$ret['examine_uv_day'])*100;
       }
     }
     //导出
     elseif ($act == 'export') 
     {
       $list = $sales_report_obj->get_sale_report_report_list($tablename, false , $where_str, 'add_time DESC', "0,{$total_count}", '*');
       $data = array();
       if ($list) 
       {
          $order_total_up   = 0;
          $order_num_up     = 0;
          $order_avg_up     = 0;
          $examine_uv_up    = 0;
          $sold_uv_up       = 0;
          $normal_refund_up = 0;
          $force_refund_up  = 0;
          $i = 0;
          foreach ($list as $key => $vo) 
          {
             $data[$key]['add_time']      = date('m月d日', strtotime($vo['add_time']));
             $data[$key]['order_total']   = $vo['order_total'];
             $data[$key]['order_num']     = $vo['order_num'];
             $data[$key]['normal_refund'] = $vo['normal_refund'];
             $data[$key]['force_refund']  = $vo['force_refund'];
             $order_avg = sprintf('%.2f', $vo['order_total']/$vo['order_num']);
             $data[$key]['order_avg']  = $order_avg;
             $data[$key]['examine_uv'] = $vo['examine_uv'];
             $data[$key]['sold_uv']    = $vo['sold_uv'];
             $data[$key]['sold_per']   = (sprintf('%.4f' ,$vo['sold_uv']/$vo['examine_uv'])*100).'%';
             //累计
             $order_total_up            += $vo['order_total'];
             $order_num_up              += $vo['order_num'];
             $order_avg_up              += $order_avg;
             $examine_uv_up             += $vo['examine_uv'];
             $sold_uv_up                += $vo['sold_uv'];
             $normal_refund_up          += $vo['normal_refund'];
             $force_refund_up           += $vo['force_refund'];
             $i++;
          }
          $ret['order_total_up']   = $order_total_up;
          $ret['order_num_up']     = $order_num_up;
          $ret['normal_refund_up'] = $normal_refund_up;
          $ret['force_refund_up']  = $force_refund_up;
          $ret['order_avg_up']     = $order_avg_up;
          $ret['examine_uv_up']    = $examine_uv_up;
          $ret['sold_uv_up']       = $sold_uv_up;
          $ret['sold_per_up']    = (sprintf('%.4f' ,$sold_uv_up/$examine_uv_up)*100).'%';
          //日均
          $ret_avg['order_total_day']     = sprintf('%.2f',$order_total_up/$i);
          $ret_avg['order_num_day']       = sprintf('%.2f',$order_num_up/$i);
          $ret_avg['normal_refund_day']   = sprintf('%.2f',$normal_refund_up/$i);
          $ret_avg['force_refund_up_day'] = sprintf('%.2f',$force_refund_up/$i);
          $ret_avg['order_avg_day']       = sprintf('%.2f',$order_avg_up/$i);
          $ret_avg['examine_uv_day']      = sprintf('%.2f',$examine_uv_up/$i);
          $ret_avg['sold_uv_day']         = sprintf('%.2f',$sold_uv_up/$i);
          $ret_avg['sold_per_day']    = (sprintf('%.4f' ,$ret_avg['sold_uv_day']/$ret_avg['examine_uv_day'])*100).'%';
          /*$fileName = "序号";
          $headArr = array('日期', '[系统]订单成交金额', '[系统]订单成交pv', '订单平均金额', '[商品]审核通过uv', '[商品]模特售出uv');
           $title = "模特库数据";
           getExcel($fileName,$title,$headArr,$data);*/
          $fileName = "销售概况";
          $title    = "销售概况";
          //var_dump($data);
          $headArr = array('日期', '[系统]订单成交金额', '[系统]订单成交pv','[系统]普通退款订单pv','[系统]强制退款订单pv', '订单平均金额', '[商品]审核通过uv', '[商品]模特售出uv','动销率');
          getExcel($fileName,$title,$headArr,$data,$ret,$ret_avg);
          exit;
       }
     }
  }
  //echo $month;
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