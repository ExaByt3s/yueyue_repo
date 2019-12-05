<?php
/**
* @file seller_scalping.php
* @synopsis 刷单商家管理
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
$add_by_enum = array('system'=>'系统', 'manual'=>'手动',);
$status_enum = array('0'=>'待确认', '7'=>'商家没有刷单', '8' => '确认商家刷单');

$action = trim($_INPUT['action']);

if ($action=='get_seller_name') {
    $user_id = intval($_INPUT['user_id']);
    if ($user_id < 1) {
        die();
    }

	$seller_info =  $mall_seller_obj->get_seller_info($user_id, 2);
    echo $seller_info['seller_data']['name'];
    die();
}
if ($action=='scalping_info') {
    $user_id = intval($_INPUT['user_id']);

    $seller = $pai_risk_obj->get_scalping_seller_info($user_id);
	$seller_info =  $mall_seller_obj->get_seller_info($user_id, 2);

    $seller['name'] = $seller_info['seller_data']['name'];
    $seller['remark'] = nl2br($seller['remark']);
    $seller['status_date'] = $seller['status_time']>0?date('Y-m-d H:i:s', $seller['status_time']):'— — — —';

    $tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_info.tpl.htm");
    $tpl->assign('seller', $seller);
    $tpl->output ();
    die();
}
if ($action=='add_seller') {
    $user_id = intval($_INPUT['user_id']);
    $remark = trim($_INPUT['remark']);
    $status = intval($_INPUT['status']);
    $status_time = $status===0?0:strtotime($_INPUT['status_time']);//待处理时不需要填处理时间

	$seller_info =  $mall_seller_obj->get_seller_info($user_id, 2);
    $seller_status = $seller_info['seller_data']['status'];
    if (strlen($remark)<1 || !in_array($status, array(0, 7, 8))) {
        echo "<script>alert('参数错误');</script>";
        die();
    }
    $remark = date('Y-m-d H:i')." 手动 {$remark} ".urldecode($_COOKIE['yue_nickname']);
    $data = array(
        'remark' => $remark, 
        'add_by' => 'manual', 
        'status' => $status, 
        'status_time' => $status_time, 
    );
    $add_rst = $pai_risk_obj->add_scalping_seller($user_id, $data);
    if ($add_rst>0) {
        echo "
            <script>alert('添加成功');
                window.parent.location.href='seller_scalping.php?user_id={$user_id}';
            </script>
        ";
        die();
    }
    echo "<script>alert('添加失败');window.parent.Shadowbox.close();self.close();</script>";
    die();
}
if ($action=='edit_seller') {
    $user_id = intval($_INPUT['user_id']);
    $remark = trim($_INPUT['remark']);
    $status = intval($_INPUT['status']);
    $status_time = $status===0?0:strtotime($_INPUT['status_time']);//待处理时不需要填处理时间

	$seller_info =  $mall_seller_obj->get_seller_info($user_id, 2);
    $seller_status = $seller_info['seller_data']['status'];
    if (strlen($remark)<1 || !in_array($status, array(0, 7, 8))) {
        echo "
            <script>
                alert('参数错误');
            </script>
            ";
        die();
    }
    $remark = date('Y-m-d H:i')." 手动 {$remark} ".urldecode($_COOKIE['yue_nickname']);
    $data = array(
        'remark' => $remark, 
        'add_by' => 'manual', 
        'status' => $status, 
        'status_time' => $status_time, 
    );

    $edit_rst = $pai_risk_obj->edit_scalping_seller($user_id, $data);
    if ($edit_rst>0) {
        echo "
            <script>
                alert('处理成功');
                window.parent.location.href='seller_scalping.php?user_id={$user_id}';
            </script>
            ";
        die();
    }
    echo "
        <script>
            alert('处理失败');
            window.parent.location.href='seller_scalping.php?user_id={$user_id}';
        </script>
        ";
    die();
}

$user_id = intval($_INPUT['user_id']);
$type_id = intval($_INPUT['type_id']);
$status = trim($_INPUT['status']);
$add_by = trim($_INPUT['add_by']);
$change_time_start = trim($_INPUT['change_time_start']);
$change_time_end = trim($_INPUT['change_time_end']);

$search_var = array();
$search_var['type_id'] = $type_id;
$where_str = '1 ';
if ($user_id>0) {
    $search_var['user_id'] = $user_id; 
    $where_str .= " AND user_id={$user_id}";
}
if (strlen($status)>0) {
    $status = intval($status);
    $search_var['status'] = $status; 
    $where_str .= " AND status={$status}";
}
if (strlen($add_by)>0) {
    $search_var['add_by'] = $add_by; 
    $where_str .= " AND add_by='{$add_by}'";
}
if (strlen($change_time_start)>0) {
    $search_var['change_time_start'] = $change_time_start; 
    $change_time_start = strtotime($change_time_start);
    $where_str .= " AND change_time>={$change_time_start}";
}
if ($change_time_end>0) {
    $search_var['change_time_end'] = $change_time_end; 
    $change_time_end = strtotime($change_time_end) + 86400;
    $where_str .= " AND change_time<={$change_time_end}";
}

$seller_list = $pai_risk_obj->get_scalping_seller_list(false, $where_str, 'change_time DESC, user_id', '0,99999999');

foreach ($seller_list as $key => &$seller) {
    $seller_info = $mall_seller_obj->get_seller_info($seller['user_id'], 2);

    //商家认证服务列表
    $seller_type_arr = explode(',', $seller_info['seller_data']['profile'][0]['type_id']);
    if ($type_id>0&&!in_array($type_id, $seller_type_arr)) {
        unset($seller_list[$key]);
        continue;
    }
    $type_list = array();
    foreach ($seller_type_arr as $type_id_temp) {
        $type_info = $mall_goods_type_obj->get_type_info($type_id_temp);
        $type_list[] = array(
            'id' => $type_id,
            'name' => $type_info['name'],
        );
    }
    unset($type_id_temp);

    $seller['seller_name'] = $seller_info['seller_data']['name'];
    $seller['remark'] = nl2br($seller['remark']);
    $seller['type_list'] = $type_list;//商家开通的服务列表
    $seller['change_date'] = $seller['change_time']>0?date('Y-m-d H:i:s', $seller['change_time']):'— — — —';
    $seller['status_str'] = $status_enum[$seller['status']];
    $seller['add_by_str'] = $add_by_enum[$seller['add_by']];
    $seller['status_date'] = $seller['status_time']>0?date('Y-m-d H:i:s', $seller['status_time']):'— — — —';
    $seller['add_date'] = date('Y-m-d H:i:s', $seller['add_time']);
}
unset($seller);
$seller_list = array_values($seller_list);

if ($action=='export') {

    $active_sheet_obj = $excel_obj->setactivesheetindex(0);
    $active_sheet_obj->setTitle( iconv('gbk', 'utf-8', '刷单商家列表') );

    $row = 1;
    $active_sheet_obj->setCellValue("A{$row}", iconv('gbk', 'utf-8', '商家ID'));
    $active_sheet_obj->setCellValue("B{$row}", iconv('gbk', 'utf-8', '商家名称'));
    $active_sheet_obj->setCellValue("C{$row}", iconv('gbk', 'utf-8', '认证服务'));
    $active_sheet_obj->setCellValue("D{$row}", iconv('gbk', 'utf-8', '处理状态'));
    $active_sheet_obj->setCellValue("E{$row}", iconv('gbk', 'utf-8', '添加来源'));
    $active_sheet_obj->setCellValue("F{$row}", iconv('gbk', 'utf-8', '更新时间'));
    $active_sheet_obj->setCellValue("G{$row}", iconv('gbk', 'utf-8', '详情'));
    $active_sheet_obj->setCellValue("H{$row}", iconv('gbk', 'utf-8', '添加时间'));

    $row++;
    foreach ($seller_list as $seller) {
        $remark_list = explode('<br />', $seller['remark']);
        $sub_row_end = $row + count($remark_list) - 1;

        $active_sheet_obj->mergeCells("A{$row}:A{$sub_row_end}");
        $active_sheet_obj->setCellValue("A{$row}", $seller['user_id']);
        $active_sheet_obj->mergeCells("B{$row}:B{$sub_row_end}");
        $active_sheet_obj->setCellValue("B{$row}", iconv('gbk', 'utf-8', $seller['seller_name']));
        $active_sheet_obj->mergeCells("C{$row}:C{$sub_row_end}");
        $type_name_arr_tmp = array_map('array_pop', $seller['type_list']);
        $active_sheet_obj->setCellValue("C{$row}", iconv('gbk', 'utf-8', implode(PHP_EOL, $type_name_arr_tmp) ) );
        $active_sheet_obj->getStyle("C{$row}")->getAlignment()->setWrapText(true);
        $active_sheet_obj->mergeCells("D{$row}:D{$sub_row_end}");
        $active_sheet_obj->setCellValue("D{$row}", iconv('gbk', 'utf-8', $seller['status_str']));
        $active_sheet_obj->mergeCells("E{$row}:E{$sub_row_end}");
        $active_sheet_obj->setCellValue("E{$row}", iconv('gbk', 'utf-8', $seller['add_by_str']));
        $active_sheet_obj->mergeCells("F{$row}:F{$sub_row_end}");
        $active_sheet_obj->setCellValue("F{$row}", iconv('gbk', 'utf-8', $seller['change_date']));
        $active_sheet_obj->mergeCells("H{$row}:H{$sub_row_end}");
        $active_sheet_obj->setCellValue("H{$row}", iconv('gbk', 'utf-8', $seller['add_date']));
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => trim($seller['color'], '#')),
            ), 
        );
        $active_sheet_obj->getStyle("A{$row}:F{$row}")->applyFromArray($styleArray);
        foreach ($remark_list as $remark) {
            $active_sheet_obj->setCellValue("G{$row}", iconv('gbk', 'utf-8', $remark));
            $row++;
        }
    }
    unset($type_name_arr_tmp);

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

	$filename = "刷单商家列表".date('YmdHis');
	header('Content-Type: application/vnd.ms-excel');
	header('Cache-Control: max-age=0');
	header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
	PHPExcel_IOFactory::createWriter($excel_obj, 'Excel5')->save('php://output');
    die();
}

$page_obj = new show_page();
$page_obj->setvar($search_var);
$page_obj->set(20, count($seller_list));		

$page_no = explode(',', $page_obj->limit());
$seller_list_page = array_slice($seller_list, $page_no[0], $page_no[1]);

$tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_list.tpl.htm");
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);

$tpl->assign('search', $search_var);
$tpl->assign('seller_list', $seller_list_page);
$tpl->assign('page', $page_obj->output(true));
$tpl->output();
?>
