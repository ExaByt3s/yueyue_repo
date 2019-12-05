<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';


$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$style = $_INPUT['style'];

$fileName = "price";
$headArr = array("�û�ID","�ǳ�","���","ԭ�۸�","�޸ĺ�۸�");

$alert_price_obj = POCO::singleton('pai_alter_price_class');
$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
$alert_price_log_obj = POCO::singleton('pai_alter_price_log_class');

$topic_list = $alert_price_obj->get_topic_list(false,"del_status=0");

$topic_info = $alert_price_obj->get_topic_info($alter_topic_id);


$list = $alert_price_user_obj->get_user_list(false, "alter_topic_id={$alter_topic_id}", 'user_id ASC');

$i=0;

foreach($list as $k=>$val){
	$style_arr = $model_style_v2_obj->get_model_style_by_user_id($val['user_id']);
	$j=0;
	foreach($style_arr as $style_val)
	{
		$new_list[$i]['user_id'] = $style_val['user_id'];
		$new_list[$i]['nickname'] = get_user_nickname_by_user_id($style_val['user_id']);
		$new_list[$i]['style'] = $style_val['style'];
		$new_list[$i]['old_price'] = $style_val['price'].'Ԫ/'.$style_val['hour'].'Сʱ';
		
		$log_info = $alert_price_log_obj->get_log_id_info($val['alter_topic_id'],$val['user_id'],$style_val['style']);
		
		if($log_info['log_id'])
		{
			$alter_type = $log_info['alter_type'];
			if($alter_type=='discount_price')
			{
				$alter_price = sprintf ( "%.2f", $style_val['price']*$log_info['type_value']*0.1);
				$alter_price = $alter_price.'Ԫ/'.$style_val['hour'].'Сʱ';
			}
			elseif($alter_type=='alter_price')
			{
				$alter_price = $log_info['type_value'].'Ԫ/2Сʱ';
			}
			elseif($alter_type=='reduce_price')
			{
				$reduce_p = $style_val['price']-$log_info['type_value'];
				if($reduce_p<1)
				{
					$reduce_p = 1;
				}
				$alter_price = $reduce_p.'Ԫ/'.$style_val['hour'].'Сʱ';
			}
			$new_list[$i]['alter_price'] = $alter_price;
			
		}
		else
		{
			$new_list[$i]['alter_price'] = '';
			
		}
		unset($alter_price);
		$i++;
		$j++;
	}
}

getExcel ( $fileName, $headArr, $new_list );


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