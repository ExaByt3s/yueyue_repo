<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';

$fileName = "enroll";
$headArr = array ("�绰","����", "Ʒ��", "����ʱ��");

$topic_obj = POCO::singleton('pai_topic_class');

$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];

$where = 1;

if ($begin_time && $end_time)
{
	$bt = strtotime ( $begin_time );
	$et = strtotime ( $end_time )+86400;
	$where .= " AND add_time BETWEEN {$bt} AND {$et}";
}


$list = $topic_obj->get_temp_enroll_list ( false, $where, 'add_time DESC', "" );


foreach ( $list as $k => $val )
{
    $out_list [$k] ['phone'] = $val ['phone'];
    $out_list [$k] ['name'] = $val['name'];
    $out_list [$k] ['type_name'] = $val['type_name'];
    $out_list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
}



getExcel ( $fileName, $headArr, $out_list );

function getExcel($fileName, $headArr, $data)
{
	if (empty ( $data ) || ! is_array ( $data ))
	{
		die ( "û����" );
	}
	if (empty ( $fileName ))
	{
		exit ();
	}
	$date = date ( "Y_m_d_H_i_s", time () );
	$fileName .= "_{$date}.xlsx";
	
	//�����µ�PHPExcel����
	$objPHPExcel = new PHPExcel ();
	$objProps = $objPHPExcel->getProperties ();
	//���ñ�ͷ
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
	{ //��д��
		$span = ord ( "A" );
		foreach ( $rows as $keyName => $value )
		{ // ��д��
			$j = chr ( $span );
			$objActSheet->getColumnDimension ( $j )->setWidth ( 20 );
			$value = iconv ( 'GBK', 'utf-8', $value );
			$objActSheet->setCellValue ( $j . $column, $value );
			$span ++;
		}
		$column ++;
	}
	
	//$fileName = iconv("utf-8", "gb2312", $fileName);
	//��������
	//$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //��������Ӧ
	$objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GBK', 'utf-8', 'ģ���б�' ) );
	//���û��ָ������һ����,����Excel�����ǵ�һ����
	$objPHPExcel->setActiveSheetIndex ( 0 );
	//������ض���һ���ͻ���web�����(Excel2007)
	//ob_end_clean();//���������,��������
	header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
	header ( "Content-Disposition: attachment; filename=\"$fileName\"" );
	header ( 'Cache-Control: max-age=0' );
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
	/*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //�ļ�ͨ�����������
        }else{
          $objWriter->save($fileName); //�ű���ʽ���У������ڵ�ǰĿ¼
        }*/
	$objWriter->save ( 'php://output' );
	exit ();

}

?>