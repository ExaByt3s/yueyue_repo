<?php
set_time_limit(3600);
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$user_obj = POCO::singleton ( 'pai_user_class' );
$model_audit_obj = POCO::singleton('pai_model_audit_class');
$hot_model_obj = POCO::singleton('pai_hot_model_class');
$model_card_obj = POCO::singleton('pai_model_card_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );
$user_add_obj = POCO::singleton ( 'pai_model_add_class' );


$fileName = "yueyue";
$headArr = array("�û�ID","�ǳ�","�ֻ�","��ӰʦҪ��","���","��Χ","�ֱ�","��Χ","��Χ","���","����","��Ƭ����","�����ܽ��","����","¼����","��ǩ");
$where = "is_approval=1";
$list = $model_audit_obj->get_model_list(false, $where, 'audit_time DESC,add_time DESC', '0,1000');

foreach($list as $k=>$val){
	$data[$k]['user_id'] = $val['user_id'];   	
	$data[$k]['nickname'] = get_user_nickname_by_user_id($val ['user_id'] );
	
	$user_info = $user_obj->get_user_info($val ['user_id']);
	$cellphone = $user_info["cellphone"];
	
	$data[$k]['cellphone'] = $cellphone;

	$model_style = $model_style_v2_obj->get_model_style_list(false, 'user_id='.$val ['user_id'], 'id DESC');
	foreach($model_style as $style)
	{
		$style_str .= "���".$style['style']."  �۸�".$style['price']."  ʱ����".$style['hour']."  ���ӣ�".$style['continue_price']."\n";
	}
	
	$model_card_info = $model_card_obj->get_model_card_info($val ['user_id']);
	$data[$k]['level_require'] = $model_card_info['level_require'];
	$data[$k]['style'] =$style_str;
	
	$data[$k]['chest'] = $model_card_info['chest'];
	$data[$k]['cup'] = $model_card_info['cup'];
	$data[$k]['waist'] = $model_card_info['waist'];
	$data[$k]['hip'] = $model_card_info['hip'];
	$data[$k]['height'] = $model_card_info['height'];
	$data[$k]['weight'] = $model_card_info['weight'];
    
	$sql = "select count(*) as num from event_db.event_date_tbl where date_status='confirm' and pay_status=1 and to_date_id=".$val['user_id'];
    $count_date_arr = db_simple_getdata($sql,true);
    
	$data[$k]['times'] = $count_date_arr['num'];
	
	$sql = "select sum(date_hour*date_price) as price from event_db.event_date_tbl where date_status='confirm' and to_date_id=".$val['user_id'];
	$date_arr = db_simple_getdata($sql,true);
	$total_price = $date_arr['price'];
	$data[$k]['total'] = (float)$total_price;
	
	$inputer_name = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);
	
	if($inputer_name)
	{
		$data[$k] ['inputer'] = $inputer_name;
	}
	else
	{
		$data[$k] ['inputer'] = "";
	}
	
	
	$label_list  = $user_add_obj->get_label_list("label!='' and uid=".$val['user_id'],'0,1', 'uid DESC', 'DISTINCT(label)');
	
	$data[$k] ['label'] = $label_list[0]['label'];
	
	unset($style_str);
	
}

getExcel ( $fileName, $headArr, $data );


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
			$value = iconv ( 'GB2312', 'utf-8', $value );
			$objActSheet->setCellValue ( $j . $column, $value );
			$span ++;
		}
		$column ++;
	}
	
	//$fileName = iconv("utf-8", "gb2312", $fileName);
	//��������
	//$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //��������Ӧ
	$objPHPExcel->getActiveSheet ()->setTitle ( iconv ( 'GB2312', 'utf-8', 'ģ���б�' ) );
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