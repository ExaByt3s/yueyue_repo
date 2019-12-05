<?php
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');
include_once 'common.inc.php';

$fileName = "date";
$headArr = array ("������","Ʒ��", "����״̬", "����ʱ��", "���ʱ��", "�Ƽ�ģ��ʱ��", "����������", "��ϵ�绰", "�̼�����", "��ϵ�绰", "�ͻ�Ԥ��", "ʵ��֧�����", "ʵ���������ʱ��", "������Դ","ȡ��ԭ��","����","����/���","ʱ��","�ͻ���ע","�Ƽ���","�Ƽ���" );

$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );

$user_obj = POCO::singleton ( 'pai_user_class' );

$list_status = $_INPUT ['list_status'] ? $_INPUT ['list_status'] : 'wait';
$order_id = ( int ) $_INPUT ['order_id'];
$order_status = $_INPUT ['order_status'];
$begin_time = $_INPUT ['begin_time'];
$end_time = $_INPUT ['end_time'];
$cameraman_phone = ( int ) $_INPUT ['cameraman_phone'];
$source = ( int ) $_INPUT ['source'];
$requirement = ( int ) $_INPUT ['requirement'];
$type_id =  $_INPUT ['type_id'];

$where = yue_oa_list_status ( $oa_role, $list_status );


if($requirement)
{
	$where .= " AND source=4";
}
else
{
	$where .= " AND source IN (1,2,3,5)";
}

if ($order_id)
{
	$where .= " AND order_id={$order_id}";
}

if ($order_status)
{
	$where .= " AND order_status='{$order_status}'";
}

if ($begin_time && $end_time)
{
	$bt = strtotime ( $begin_time );
	$et = strtotime ( $end_time )+86400;
	$where .= " AND add_time BETWEEN {$bt} AND {$et}";
}

if ($cameraman_phone)
{
	$where .= " AND cameraman_phone={$cameraman_phone}";
}

if ($source)
{
	$where .= " AND source={$source}";
}

if($type_id)
{
    $where .= " AND ".type_where($type_id);
}

$list = $model_oa_order_obj->get_order_list ( false, $where, 'order_id DESC', "" );

$i = 0;
foreach ( $list as $k => $val )
{
	$order_id = $val ['order_id'];
	$model_list = $model_list_obj->get_model_list ( false, "order_id={$order_id} and status=1", 'id DESC', '0,100' );

    //��ȡ��������
    if($val['type_id_str'])
    {
        //�̳Ƿ�������
        $type_name_arr = array('12'=>'Ӱ������','5'=>'��Ӱ��ѵ','3'=>'��ױ����','31'=>'ģ��Լ��','40'=>'��Ӱ����');
        $type_id_arr = explode(',',$val['type_id_str']);
        foreach($type_id_arr as $type_val)
        {
            $__type_name_arr[] = $type_name_arr[$type_val];
        }

        $type_name = implode("\r\n",$__type_name_arr);
        unset($__type_name_arr);
    }
    else
    {
        //TT��������
        $type_name_arr = array('1'=>'Ӱ������','2'=>'��Ӱ��ѵ','3'=>'��ױ����','7'=>'ģ��Լ��','0'=>'ģ��Լ��');
        $type_name = $type_name_arr[$val['service_id']];
    }
	
	if($model_list)
	{
		foreach ( $model_list as $m_val )
		{
			$out_list [$i] ['order_id'] = $val ['order_id'];
			$out_list [$i] ['type_name'] = $type_name;
			$out_list [$i] ['order_status'] = yue_oa_order_status ( $val ['order_status'] );
			$out_list [$i] ['date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['date_time'] ) );
			$out_list [$i] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
			$out_list [$i] ['recommend_add_time'] = date ( "Y-m-d H:i", $m_val ['add_time'] );
			$out_list [$i] ['cameraman_nickname'] = $val ['cameraman_nickname'];
			$out_list [$i] ['cameraman_phone'] = $val ['cameraman_phone'];
			$out_list [$i] ['model_nickname'] = get_user_nickname_by_user_id($m_val ['user_id']);
			$out_list [$i] ['model_phone'] = $user_obj->get_phone_by_user_id($m_val ['user_id']);
			
			if($requirement)
			{
				$out_list [$i] ['budget'] =$val ['question_budget'];
			}
			else
			{
				$out_list [$i] ['budget'] =$val ['budget'];
			}
			if($val ['payment_status']=='done')
			{
				$out_list [$i] ['total_price'] =$val ['receivable_amount'];
			}
			else
			{
				$out_list [$i] ['total_price'] ="";
			}
			
			$out_list [$i] ['fact_date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['fact_date_time'] ) );
			
			if($val['source']==1)
			{
				$source = "�绰";
			}
			elseif($val['source']==2)
			{
				$source = "΢��";
			}
			elseif($val['source']==3)
			{
				$source = "PC";
			}
            elseif($val['source']==5)
            {
                $source = "APP";
            }
			
			$out_list [$i] ['source'] =$source;
			$out_list [$i] ['cancel_reason'] =$val ['cancel_reason'];
			
			$out_list [$i] ['city_name'] = get_poco_location_name_by_location_id ( $val ['location_id'], false, false );

            $out_list [$i] ['model_num'] =$val ['model_num'];
            $out_list [$i] ['hour'] =$val ['hour'];
            $out_list [$i] ['date_remark'] = str_replace("<br rel=auto>","\r\n",$val ['date_remark']);

            $out_list [$i] ['recommend_user_id'] =$m_val ['recommend_user_id'];
            $out_list [$i] ['recommend_num'] =$model_list_obj->get_model_list(true,"order_id=".$val ['order_id']);

			$i++;
		}
	}
	else
	{
			$out_list [$i] ['order_id'] = $val ['order_id'];
            $out_list [$i] ['type_name'] = $type_name;
			$out_list [$i] ['order_status'] = yue_oa_order_status ( $val ['order_status'] );
			$out_list [$i] ['date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['date_time'] ) );
			$out_list [$i] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
			$out_list [$i] ['recommend_add_time'] = "";
			$out_list [$i] ['cameraman_nickname'] = $val ['cameraman_nickname'];
			$out_list [$i] ['cameraman_phone'] = $val ['cameraman_phone'];
			$out_list [$i] ['model_nickname'] = "";
			$out_list [$i] ['model_phone'] = "";
			
			if($requirement)
			{
				$out_list [$i] ['budget'] =$val ['question_budget'];
			}
			else
			{
				$out_list [$i] ['budget'] =$val ['budget'];
			}
			
			if($val ['payment_status']=='done')
			{
				$out_list [$i] ['total_price'] =$val ['receivable_amount'];
			}
			else
			{
				$out_list [$i] ['total_price'] ="";
			}
			
			$out_list [$i] ['fact_date_time'] = date ( "Y-m-d H:i", strtotime ( $val ['fact_date_time'] ) );
			
			if($val['source']==1)
			{
				$source = "�绰";
			}
			elseif($val['source']==2)
			{
				$source = "΢��";
			}
			elseif($val['source']==3)
			{
				$source = "PC";
			}
            elseif($val['source']==5)
            {
                $source = "APP";
            }
			
			$out_list [$i] ['source'] =$source;
			$out_list [$i] ['cancel_reason'] =$val ['cancel_reason'];
			
			$out_list [$i] ['city_name'] = get_poco_location_name_by_location_id ( $val ['location_id'], false, false );

            $out_list [$i] ['model_num'] =$val ['model_num'];

            $out_list [$i] ['hour'] =$val ['hour'];
            $out_list [$i] ['date_remark'] =str_replace(array("<br rel=auto>","<br>"),"\r\n",$val ['date_remark']);

            $out_list [$i] ['recommend_user_id'] ="��";
            $out_list [$i] ['recommend_num'] ="0";

			$i++;
	}
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