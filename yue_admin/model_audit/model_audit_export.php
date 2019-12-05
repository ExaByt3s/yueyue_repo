<?php 

/*
 * 
 *�������ݿ�����
 *
 *
*/
  include_once './include/Classes/PHPExcel.php';
  include_once 'common.inc.php';
  include_once 'config/authority_model_audit.php';
  $user_obj = POCO::singleton ( 'pai_user_class' );
  $user_add_obj = POCO::singleton ( 'pai_model_add_class' );
  $model_audit_obj = POCO::singleton('pai_model_audit_class');
  $model_card_obj = POCO::singleton('pai_model_card_class');
  $where ="is_approval=1";
  $data = array();
	$list = $model_audit_obj->get_model_list(false, $where, 'audit_time DESC,add_time DESC', '0,1000');
  foreach($list as $k=>$val){
    $data[$k]['key']       = $k+1;
    $data[$k]['user_id']   = $val['user_id']; 
    $data[$k]['nickname']  = get_user_nickname_by_user_id($val ['user_id'] );
    $data[$k]['cellphone'] = $val['cellphone'];     
    $data[$k]['add_time'] = date("Y-m-d",$val['add_time']);    
    $data[$k]['user_icon'] = str_replace("_86","_100",get_user_icon($val ['user_id'], 86));
    $is_complete =  $model_card_obj->check_input_is_complete($val ['user_id']);
    $data[$k]['is_complete'] = !empty($is_complete)? '��':'��';
    $data[$k]['is_set']      = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);
    $data[$k]['audit_name']  = get_user_nickname_by_user_id($val['audit_user_id']);
    $data[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d", $val['audit_time']) : '';  
}
$fileName = "yueyue_excel";
  $headArr = array("���","�û�ID","�ǳ�","�ֻ�","ע��ʱ��","Uͷ��","�����Ƿ�����","�Ƿ����","������","����ʱ��");
	//$headArr = array("����","�ǳ�","΢������","��̳����","POCO�û���","APP�ǳ�","�ֻ�����","΢��","QQ","����","POCOID","ְҵ״̬","������","����","ѧУ����","��Դ","�ٷ��", "��פ����", "¼����", "¼��ʱ��", "֧�����˺�", "�Ա�", "����","���", "����");

getExcel($fileName,$headArr,$data);
function getExcel($fileName,$headArr,$data){
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        exit;
    }
    $date = date("Y_m_d",time());
    $fileName .= "_{$date}.xls";
 
    //�����µ�PHPExcel����
    $objPHPExcel = new PHPExcel();
    $objProps = $objPHPExcel->getProperties(); 
    //���ñ�ͷ
    $key = ord("A");
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet->getRowDimension('1')->setRowHeight(22);
    foreach($headArr as $v){
        $colum = chr($key);
        $objActSheet->getColumnDimension($colum)->setWidth(20);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $v = iconv('GB2312','utf-8', $v);
        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
        $key += 1;
    }
    //exit;
    $column = 2;
    //$objActSheet = $objPHPExcel->getActiveSheet();
    foreach($data as $key => $rows){ //��д��
        $span = ord("A");
        foreach($rows as $keyName=>$value){// ��д��
            $j = chr($span);
            $objActSheet->getColumnDimension($j)->setWidth(20);
            $value = iconv('GB2312', 'utf-8',  $value);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }
 
    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //��������
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //��������Ӧ
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  'ģ���б�'));
    //���û��ָ������һ����,����Excel�����ǵ�һ����
    $objPHPExcel->setActiveSheetIndex(0);
    //������ض���һ���ͻ���web�����(Excel2007)
    //ob_end_clean();//���������,��������
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
       /*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //�ļ�ͨ�����������
        }else{
          $objWriter->save($fileName); //�ű���ʽ���У������ڵ�ǰĿ¼
        }*/
    $objWriter->save('php://output');
  exit;
 
}





 ?>