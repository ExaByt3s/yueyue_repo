<?php 
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';

$fileName = "date";
$headArr = array ("订单号","订单状态", "收款状态", "摄影师名称", "摄影师ID", "应收金额","应付金额", "真实姓名", "支付方式", "支付账号", "流水号", "支付时间", "添加时间","导入系统时间","付款备注" );


$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$user_obj = POCO::singleton ( 'pai_user_class' );
$import_order_obj = POCO::singleton ( 'pai_oa_import_order_class' );
$date_obj = POCO::singleton ( 'event_date_class' );

$order_status = $_INPUT ['order_status'];
$payment_status = $_INPUT ['payment_status'];
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];
$select_time = $_INPUT ['select_time'];

$where = "order_status IN ('pay_confirm','close','refund','wait_shoot','wait_close')";


if ($order_status)
{
	$where .= " AND order_status='{$order_status}'";
}

if ($payment_status)
{
	$where .= " AND payment_status='{$payment_status}'";
}

if ($select_time && $begin_time && $end_time)
{
	$bt = strtotime ( $begin_time );
	$et = strtotime ( $end_time );
	if($select_time=='add_order')
	{
		$where .= " AND add_time BETWEEN {$bt} AND {$et}";
	}elseif($select_time=='pay_confrim')
	{
		$where .= " AND pay_time BETWEEN {$bt} AND {$et}";
	}
}


$list = $model_oa_order_obj->get_order_list ( false, $where, 'order_id DESC', "" );

foreach ( $list as $k => $val )
{
	$out_list [$k] ['order_id'] = $val['order_id'];
	
	$out_list [$k] ['order_status'] = yue_oa_order_status ( $val ['order_status'] );
	
	if($val['payment_status']=='wait')
	{
		$payment_status = "未收款";
	}
	elseif($val['payment_status']=='done')
	{
		$payment_status = "已收款";
	}
	
	$out_list [$k] ['payment_status'] = $payment_status;
	
	$out_list [$k] ['cameraman_nickname'] =$val['cameraman_nickname'];
	
	$out_list [$k] ['user_id'] = (int)$user_obj->get_user_id_by_phone($val ['cameraman_phone']);
	
	$out_list [$k] ['receivable_amount'] = $val ['receivable_amount'];
	
	$out_list [$k] ['payable_amount'] = $val ['payable_amount'];
	
	$out_list [$k] ['cameraman_realname'] = $val['cameraman_realname'];
	
	if($val['pay_type']=='manual_alipay')
	{
		$pay_type = "支付宝";
	}
	elseif($val['pay_type']=='manual_wx')
	{
		$pay_type = "微信支付";
	}
	elseif($val['pay_type']=='manual_bank')
	{
		$pay_type = "银行转账";
	}
	
	$out_list [$k] ['pay_type'] = $pay_type;
	
	$out_list [$k] ['pay_account'] = $val['pay_account'];
	
	$out_list [$k] ['running_number'] = $val['running_number'];


	$out_list [$k] ['pay_time'] = date ( "Y-m-d H:i", $val ['pay_time'] );
	
	$out_list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
	
	$import_list = $import_order_obj->get_order_list ( false, 'order_id='.$val ['order_id'], 'id ASC', '0,1' );
	
	$date_info = get_date_info($import_list[0] ['date_id']);
	
	$out_list [$k] ['import_time'] = date ( "Y-m-d H:i", $date_info ['add_time'] );
	
	$out_list [$k] ['payment_remark'] = $val['payment_remark'];

}


getExcel ( $fileName, $headArr, $out_list );

function getExcel($fileName, $headArr, $data)
{
	if (empty ( $data ) || ! is_array ( $data ))
	{
		die ( "data must be a array" );
	}
	if (empty ( $fileName ))
	{
		exit ();
	}
	$date = date ( "Y_m_d", time () );
	$fileName .= "_{$date}.xlsx";
	
	//创建新的PHPExcel对象
	$objPHPExcel = new PHPExcel ();
	$objProps = $objPHPExcel->getProperties ();
	//设置表头
	$key = ord ( "A" );
	$objActSheet = $objPHPExcel->getActiveSheet ();
	$objActSheet->getRowDimension ( '1' )->setRowHeight ( 22 );
	foreach ( $headArr as $v )
	{
		$colum = chr ( $key );
		$objActSheet->getColumnDimension ( $colum )->setWidth ( 20 );
		$objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
		$objActSheet->getStyle ( $colum )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::VERTICAL_CENTER );
		$v = iconv ( 'GB2312', 'utf-8', $v );
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $colum . '1', $v );
		$key += 1;
	}
	//exit;
	$column = 2;
	//$objActSheet = $objPHPExcel->getActiveSheet();
	foreach ( $data as $key => $rows )
	{ //行写入
		$span = ord ( "A" );
		foreach ( $rows as $keyName => $value )
		{ // 列写入
			$j = chr ( $span );
			$objActSheet->getColumnDimension ( $j )->setWidth ( 20 );
			$value = iconv ( 'GBK', 'utf-8', $value );
			$objActSheet->setCellValue ( $j . $column, $value );
			$span ++;
		}
		$column ++;
	}
	
	//$fileName = iconv("utf-8", "gb2312", $fileName);
	//重命名表
	//$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //内容自适应
	$objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GBK', 'utf-8', '模特列表' ) );
	//设置活动单指数到第一个表,所以Excel打开这是第一个表
	$objPHPExcel->setActiveSheetIndex ( 0 );
	//将输出重定向到一个客户端web浏览器(Excel2007)
	//ob_end_clean();//清除缓冲区,避免乱码
	header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
	header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
	header ( 'Cache-Control: max-age=0' );
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
	/*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //文件通过浏览器下载
        }else{
          $objWriter->save($fileName); //脚本方式运行，保存在当前目录
        }*/
	$objWriter->save ( 'php://output' );
	exit ();

}


?>