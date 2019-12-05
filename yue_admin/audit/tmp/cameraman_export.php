<?php 

/*
 * 
 *����������Ӱʦ���ݿ�����
 *
 *
*/
	 include_once './include/Classes/PHPExcel.php';
	 include_once 'common.inc.php';
   check_authority(array('cameraman'));
	 $cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $uid = $_INPUT['ids'] ? $_INPUT['ids'] : 0;
    if (empty($uid) || !is_array($uid)) 
    {
    	echo "<script type='text/javascript'>window.alert('���ύ��������������');location.href='model_quick_search.php';</script>";
    	exit;
	}

	$data  = array();
  if (!empty($uid) && is_array($data)) 
  {
	 foreach ($uid as $key => $id) 
	 {
		//������Ϣ
		$info_data  = $cameraman_add_obj->get_cameraman_info($id);
    $data[$key]['id']          =  $key+1;
		$data[$key]['name']        = $info_data['name'];
		$data[$key]['weixin_name'] = $info_data['weixin_name'];
		$data[$key]['phone']       = $info_data['phone'];
		$data[$key]['weixin_id']   = $info_data['weixin_id'];
		$data[$key]['homepage']    = $info_data['homepage'];
    //������Ϣ
    $personal_data = $cameraman_add_obj->get_personal_info($id);
    //�»���
    $data[$key]['month_take']  = '';
    //attend_total
    $data[$key]['attend_total']= '';
    $data[$key]['is_studio']   = '��';
    $data[$key]['studio_name'] = '';
    if (!empty($personal_data) && is_array($personal_data)) 
    {
      if ($personal_data['is_studio'] == 0) 
      {
        $data[$key]['is_studio']   = '��';
        $data[$key]['studio_name'] = $personal_data['studio_name'];
      }
      else
      {
        $data[$key]['is_studio'] = '��';
        $data[$key]['studio_name'] = '';
      }
    }
		$data[$key]['photographic'] = $personal_data['photographic'];
    $data[$key]['is_fview'] = 'Ը��';
		if (!empty($personal_data) && is_array($personal_data)) 
    {
      if ($personal_data['is_fview'] == 0) 
      {
        $data[$key]['is_fview']   = 'Ը��';
      }
      else
      {
        $data[$key]['is_fview']   = '��Ը��';
      }
    }
     $data[$key]['car_type'] = $personal_data['car_type'];
      //��פ����
      $data[$key]['city'] = '����';
      if (!empty($info_data) && is_array($info_data)) 
      {
        switch ($info_data['city']) 
        {
            case 0:
                $data[$key]['city'] = "����";
                break;
            case 1:
                $data[$key]['city'] = "�Ϻ�";
                break;
            case 2:
                $data[$key]['city'] = "����";
                break;
            case 3:
                $data[$key]['city'] = "�ɶ�";
                break;
            case 4:
                $data[$key]['city'] = "�人";
                break;
            case 5:
                $data[$key]['city'] = "����";
                break;
        }
          # code...
      }
       
	 }
  }
	$fileName = "yueyue_excel";
  $headArr = array("���","����","΢������","�ֻ�����","΢��","������ҳ","���Ļ���","Լ�Ĵ���","�Ƿ��й�����","����������","��Ӱ����","�Ƿ�Ը���Զ��", "�� ��", "��פ����");
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
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  '��Ӱʦ�б�'));
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