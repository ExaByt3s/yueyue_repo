<?php
/**
* @file seller_scalping_check_log.php
* @synopsis 刷单检查记录
* @author wuhy@yueus.com
* @version null
* @date 2015-10-22
 */

include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod); 
$excel_obj = new PHPExcel();

ini_set('memory_limit', '512M');
define('G_SIMPLE_INPUT_CLEAN_VALUE', 1);
include_once 'common.inc.php';
$pai_risk_obj = POCO::singleton('pai_risk_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$mall_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$rules_list = $pai_risk_obj->rules_list;
$operator_enum = array('seller'=>'商家', 'buyer'=>'消费者', 'sys'=>'系统');

$action = trim($_INPUT['action']);

if( $action=='rule_info' )
{
    $seller_user_id = intval($_INPUT['seller_user_id']);
    $rule_code = trim($_INPUT['rule_code']);
    $rule_type_id = trim($_INPUT['rule_type_id']);
    $start_time = intval($_INPUT['start_time']);
    $end_time = intval($_INPUT['end_time']);
    $check_rst = $pai_risk_obj->check_rule($rule_code, $seller_user_id, $rule_type_id, $start_time, $end_time);

    if( $check_rst['code']==1 )
    {
        $order_list = $check_rst['more_info'];
        foreach( $order_list as &$order_info )
        {
            $type_info = $mall_goods_type_obj->get_type_info($order_info['type_id']);

            $order_info['buyer_reg_date'] = date('Y-m-d H:i:s', $order_info['buyer_reg_time']);
            $order_info['add_date'] = date('Y-m-d H:i:s', $order_info['add_time']);
            $order_info['pay_date'] = date('Y-m-d H:i:s', $order_info['pay_time']);
            $order_info['sign_date'] = date('Y-m-d H:i:s', $order_info['sign_time']);
            $order_info['is_auto_sign_str'] = $order_info['sign_by']=='sys'?'是':'否';
            $order_info['sign_op'] = $operator_enum[$order_info['sign_by']];
            $order_info['type_name'] = $type_info['name'];
        }
    }

	$seller_info =  $mall_seller_obj->get_seller_info($seller_user_id, 2);

    $tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_rule_info.tpl.htm");
    $tpl->assign('seller_info', $seller_info['seller_data']);
    $tpl->assign('rule_code', $rule_code);
    $tpl->assign('remark', $rules_list[$rule_code]['remark'][$rule_type_id]); 
    $tpl->assign('start_time', date('Y-m-d', $start_time));
    $tpl->assign('end_time', date('Y-m-d', $end_time));
    $tpl->assign('order_list', $order_list);
    $tpl->output ();
    die();
}

$seller_user_id = intval($_INPUT['seller_user_id']);
$service_type_id = trim($_INPUT['service_type_id']);
$rule_type_id = trim($_INPUT['rule_type_id']);
$rule_code = trim($_INPUT['rule_code']);
$log_id = trim($_INPUT['log_id']);

$check_start_time = strtotime($_INPUT['check_start_time']);
$check_end_time = strtotime($_INPUT['check_end_time']);

$search_var = array();
$where_str = '1 ';
if( $seller_user_id>0 )
{
    $search_var['seller_user_id'] = $seller_user_id; 
    $where_str .= " AND seller_user_id={$seller_user_id}";
}
if( $check_start_time>0 )
{
    $search_var['check_start_time'] = date('Y-m-d', $check_start_time); 
    $where_str .= " AND add_time>={$check_start_time}";
}

if( $check_end_time>0 )
{
    $search_var['check_end_time'] = date('Y-m-d', $check_end_time); 
    $check_end_time = $check_end_time+24*3600;
    $where_str .= " AND add_time<={$check_end_time}";
}

if( strlen($service_type_id)>0 )
{
    $search_var['service_type_id'] = $service_type_id; 
    $where_str .= " AND FIND_IN_SET('{$service_type_id}', `seller_type_id`)";
}

if( strlen($rule_type_id)>0 )
{
    $search_var['rule_type_id'] = $rule_type_id; 
    $where_str .= " AND rule_type_id={$rule_type_id}";
}

if( strlen($rule_code)>0 )
{
    $search_var['rule_code'] = $rule_code; 
    $where_str .= " AND FIND_IN_SET('{$rule_code}', `rule_code_m`)";
}

if( strlen($log_id)>0 )
{
    $search_var['log_id'] = $log_id; 
    $where_str .= " AND log_id={$log_id}";
}

$total_count = $pai_risk_obj->get_scalping_check_log_list(0, true, $where_str);
$page_obj = new show_page();
$page_obj->setvar($search_var);
$page_obj->set(20, $total_count);	
$limit = $page_obj->limit();
if ($action=='export') $limit = '0,99999999';
$check_log_list = $pai_risk_obj->get_scalping_check_log_list(0, false, $where_str , 'seller_user_id ASC,add_time DESC,log_id', $limit);

foreach( $check_log_list as $key => &$check_log )
{
    $seller_info = $mall_seller_obj->get_seller_info($check_log['seller_user_id'], 2);

    //商家认证服务列表
    $type_list = array();
    $seller_type_arr = explode(',', $check_log['seller_type_id']);
    foreach( $seller_type_arr as $type_id_temp )
    {
        $type_info = $mall_goods_type_obj->get_type_info($type_id_temp);
        $type_list[] = array(
            'id' => $type_id,
            'name' => $type_info['name'],
        );
    }

    $rule_type_tmp = '通用规则';
    $rule_type_id = $check_log['rule_type_id'];
    if($rule_type_id>0)
    {
        $type_info = $mall_goods_type_obj->get_type_info($rule_type_id);//检查范围
        $rule_type_tmp = $type_info['name']."专用规则";
    }

    $check_log['seller_name'] = $seller_info['seller_data']['name'];
    $check_log['type_list'] = $type_list;//商家开通的服务列表
    $check_log['rule_type'] = $rule_type_tmp; 
    $check_log['start_date'] = date('Y-m-d H:i:s', $check_log['start_time']);
    $check_log['end_date'] = date('Y-m-d H:i:s', $check_log['end_time']);
    $check_log['add_date'] = date('Y-m-d H:i:s', $check_log['add_time']);

    //商家命中规则列表
    $rules_rst = array();
    foreach( explode(',', $check_log['rule_code_m']) as $rule_code )
    {
        $rules_rst[] = array(
            'rule_code' => $rule_code, 
            'remark' => $rules_list[$rule_code]['remark'][$rule_type_id], 
        );
    }
    $check_log['rules_rst'] = $rules_rst;
}
unset($check_log);
if( $action=='export' )
{
    $active_sheet_obj = $excel_obj->setactivesheetindex(0);
    $active_sheet_obj->setTitle( iconv('gbk', 'utf-8', '商家刷单记录') );

    $row = 1;
    $active_sheet_obj->setCellValue("A{$row}", iconv('gbk', 'utf-8', '商家ID'));
    $active_sheet_obj->setCellValue("B{$row}", iconv('gbk', 'utf-8', '商家名称'));
    $active_sheet_obj->setCellValue("C{$row}", iconv('gbk', 'utf-8', '认证服务'));
    $active_sheet_obj->setCellValue("D{$row}", iconv('gbk', 'utf-8', '规则类型'));
    $active_sheet_obj->mergeCells("E{$row}:F{$row}");
    $active_sheet_obj->setCellValue("E{$row}", iconv('gbk', 'utf-8', '命中规则'));
    $active_sheet_obj->mergeCells("G{$row}:H{$row}");
    $active_sheet_obj->setCellValue("G{$row}", iconv('gbk', 'utf-8', '签到时段'));
    $active_sheet_obj->setCellValue("I{$row}", iconv('gbk', 'utf-8', '检查时间'));
    $active_sheet_obj->setCellValue("J{$row}", iconv('gbk', 'utf-8', '备注'));

    $row++;
    foreach( $check_log_list as $check_log )
    {
        $sub_row_end = $row + count($check_log['rules_rst']) - 1;

        $active_sheet_obj->mergeCells("A{$row}:A{$sub_row_end}");
        $active_sheet_obj->setCellValue("A{$row}", $check_log['seller_user_id']);

        $active_sheet_obj->mergeCells("B{$row}:B{$sub_row_end}");
        $active_sheet_obj->setCellValue("B{$row}", iconv('gbk', 'utf-8', $check_log['seller_name']));

        $type_name_arr_tmp = array_map('array_pop', $check_log['type_list']);
        $active_sheet_obj->mergeCells("C{$row}:C{$sub_row_end}");
        $active_sheet_obj->setCellValue("C{$row}", iconv('gbk', 'utf-8', implode(PHP_EOL, $type_name_arr_tmp) ) );
        $active_sheet_obj->getStyle("C{$row}")->getAlignment()->setWrapText(true);
        $active_sheet_obj->mergeCells("D{$row}:D{$sub_row_end}");
        $active_sheet_obj->setCellValue("D{$row}", iconv('gbk', 'utf-8', $check_log['rule_type']));
        $active_sheet_obj->mergeCells("G{$row}:G{$sub_row_end}");
        $active_sheet_obj->setCellValue("G{$row}", $check_log['start_date']);
        $active_sheet_obj->mergeCells("H{$row}:H{$sub_row_end}");
        $active_sheet_obj->setCellValue("H{$row}", $check_log['end_date']);
        $active_sheet_obj->mergeCells("I{$row}:I{$sub_row_end}");
        $active_sheet_obj->setCellValue("I{$row}", $check_log['add_date']);

        $active_sheet_obj->getRowDimension($row)->setRowHeight(-1);
        //命中规则列表
        foreach( $check_log['rules_rst'] as $rule_info )
        {
            $active_sheet_obj->setCellValue("E{$row}", $rule_info['rule_code']);
            $active_sheet_obj->setCellValue("F{$row}", iconv('gbk', 'utf-8', $rule_info['remark']));
            $row++;
        }
    }

    //样式
	$highestRow = $active_sheet_obj->getHighestRow();//取得有数据的最大行号，PHPexcel 1.8.0使用getHighestDataRow()将更严谨
	$highestColumn = $active_sheet_obj->getHighestColumn();//取得有数据的最大列标，PHPexcel 1.8.0使用getHighestDataColumn()将更严谨
    $sheet_head_style_arr = array(
        'font'  => array(
            'bold'  => true,
        ), 
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
	$active_sheet_obj->getStyle("A1:{$highestColumn}1")->applyFromArray($sheet_head_style_arr);
	$sheet_head_style_arr = array( 'alignment' => array(
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		),
	);
	$active_sheet_obj->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray($sheet_head_style_arr);

	$filename = "刷单检查记录".date('YmdHis');
	header('Content-Type: application/vnd.ms-excel');
	header('Cache-Control: max-age=0');
	header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
	PHPExcel_IOFactory::createWriter($excel_obj, 'Excel5')->save('php://output');
}
foreach($rules_list as $key => &$rule)
{
    $rule['code'] = $key;
}
unset($rule);

$tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_check_log_list.tpl.htm");
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);

$tpl->assign('search', $search_var);
$tpl->assign('rules_list', array_values($rules_list));
$tpl->assign('check_log_list', $check_log_list);
$tpl->assign('page', $page_obj->output(true));
$tpl->output();
?>
