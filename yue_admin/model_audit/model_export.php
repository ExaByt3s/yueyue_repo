<?php 

/*
 * 
 *�������ݿ�����
 *
 *
*/
	 include_once './include/Classes/PHPExcel.php';
	 include_once 'common.inc.php';
   //��������
   include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
   check_authority(array('model'));
	 $model_add_obj  = POCO::singleton('pai_model_add_class');
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
		$info_data  = $model_add_obj->get_model_info($id);
    //print_r($info_data);
		//ְҵ
		$prof_data  = $model_add_obj->get_model_profession($id);
		//��񼰷��۸�
		$style_data = $model_add_obj->list_style($id);
		//������Ϣ
		$other_data = $model_add_obj->get_model_other($id);
		//�����Ϣ
		$stature_data       = $model_add_obj->get_model_stature($id);
    $data[$key]['id']          =  $key+1;
		$data[$key]['name']        = $info_data['name'];
		$data[$key]['nickname']    = $info_data['nick_name'];
		$data[$key]['weixin_name'] = $info_data['weixin_name'];
		$data[$key]['discuz_name'] = $info_data['discuz_name'];
		$data[$key]['poco_name']   = $info_data['poco_name'];
		$data[$key]['app_name']    = $info_data['app_name'];
		$data[$key]['phone']       = $info_data['phone'];
		$data[$key]['weixin_id']   = $info_data['weixin_id'];
		$data[$key]['qq']          = $info_data['qq'];
		$data[$key]['email']       = $info_data['email'];
		$data[$key]['poco_id']     = $info_data['poco_id'];
        //ְҵѡ��
        $p_state  = '';
        $p_school = '';
        if (!empty($prof_data) && is_array($prof_data)) 
        {
          switch ($prof_data['p_state']) 
          {
              case 1:
                $p_state = '��ְ';
                break;
              case 2:
                $p_state = 'ȫְ';
                break;
              case 3:
                $p_state = 'ѧ��';
                break;
        }
        $p_school = $prof_data['p_school'];
      }
      $data[$key]['p_state'] = $p_state;
      //���ͼ۸�
      /*$style_list  ='';
      $style_price = '';
      if (!empty($style_data) && is_array($style_data)) 
      {
        $style_name   = '';
        $twoh_price   = ''; 
        $fourh_price  = ''; 
        $addh_price   = ''; 
        foreach ($style_data as $key2 => $vo) 
        {
            switch ($vo['style']) 
            {
                case 0:
                    $style_name  = 'ŷ��';
                    break;
                case 1:
                    $style_name = '����';
                    break;
                case 2:
                    $style_name = '����';
                    break;
                case 3:
                    $style_name = '����';
                    break;
                case 4:
                    $style_name = '��ϵ';
                    break;
                case 5:
                    $style_name = '��ϵ';
                    break;
                case 6:
                    $style_name = '�Ը�';
                    break;
                case 7:
                    $style_name = '����';
                    break;
                case 8:
                    $style_name = '��Ƭ';
                    break;
                case 9:
                    $style_name = '��ҵ';
                    break;
                default:
                    # code...
                    break;
            }
           $twoh_price  = $style_name.":".$vo['twoh_price']."/2Сʱ";
           $fourh_price = $style_name.":".$vo['fourh_price']."/4Сʱ";
           $addh_price  = $style_name.":".$vo['addh_price']."/Сʱ(��ʱ)";
           if ($key2 != 0) 
           {
               $style_list  .= ',';
               $style_price .= ',';

           }
           $style_list  .= $style_name;
           $style_price .= "[".$twoh_price.",".$fourh_price.",".$addh_price."]";
        }
      }

      $data[$key]['style_list']  = $style_list;
      $data[$key]['style_price'] = $style_price;*/
      //ѧУ
      $data[$key]['p_school']  = $p_school;
      //��Դ
      $data[$key]['information_sources'] = $other_data['information_sources'];
      $data[$key]['activity'] = $other_data['activity'];
      $city = '';
      if (!empty($info_data) && is_array($info_data)) 
      {
        $location_id_info = get_poco_location_name_by_location_id ($info_data['location_id']);
        //print_r($location_id_info);exit;
        if (!empty($location_id_info)) 
        {
          $city = $location_id_info;
        }
      }
      //����(����/�Ա�/����������)
      $data[$key]['city']         = $city;
      $data[$key]['inputer_name'] = $info_data['inputer_name'];
      $data[$key]['inputer_time'] = $info_data['inputer_time'];
      $data[$key]['alipay_info']  = $other_data['alipay_info'];
      //�Ա�
      $sex = 'Ů';
      if (!empty($stature_data) && is_array($stature_data)) 
      {
          switch ($stature_data['sex']) 
          {
              case 0:
                  $sex = "Ů";
                  break;
               case 1:
                  $sex = "��";
                  break;
          }
      }
      $data[$key]['sex'] = $sex;
      $age = '';
      if (isset($stature_data['age']) && $stature_data['age'] != 0) 
      {
         $age = date('Y', time()) - substr($stature_data['age'], 0, 4);
        # code...
      }
      $data[$key]['age']    = $age;
      $data[$key]['height'] = $stature_data['height'];
      $data[$key]['weight'] = $stature_data['weight'];
      $cup_id = '';
      switch ($stature_data['cup_id']) 
      {
        case 1:
          $cup_id = 28;
          break;
        case 2:
          $cup_id = 30;
          break;
        case 3:
          $cup_id = 32;
          break;
        case 4:
          $cup_id = 34;
          break;
        case 5:
          $cup_id = 36;
          break;
        case 6:
          $cup_id = 38;
          break;
      }
      $cup_a = '';
      switch ($stature_data['cup_a']) 
      {
        case 1:
          $cup_a = 'A';
          break;
        case 2:
          $cup_a = 'B';
          break;
        case 3:
          $cup_a = 'C';
          break;
        case 4:
          $cup_a = 'D';
          break;
        case 5:
          $cup_a = 'E';
          break;
        case 6:
          $cup_a = 'F';
          break;
      }
      $data[$key]['cup']    = $cup_id.$cup_a;
      $data[$key]['chest']  = $stature_data['chest'].'/'.$stature_data['waist'].'/'.$stature_data['chest_inch'];
	 }
  }
	$fileName = "yueyue_excel";
  $headArr = array("���","����","�ǳ�","΢������","��̳����","POCO�û���","APP�ǳ�","�ֻ�����","΢��","QQ","����","POCOID","ְҵ״̬","ѧУ����","��Դ","�ٷ��", "��פ����", "¼����", "¼��ʱ��", "֧�����˺�", "�Ա�", "����","���", "����", "�ֱ�", "��Χ" );
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