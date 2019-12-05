<?php 

/*
 *xiao xiao 
 *ģ�� ���ú���
 *
*/

/*
  * һάת��ά
  * ������ѯר��
  */
  function change_assoc_arr($data, $b_select = false)
  {
    $arr = array();
    if (!empty($data) && is_array($data)) 
    {
      # code...
      $i = 0;
      foreach ($data as $key => $vo) 
      {   if ($b_select == true) 
        {
          $arr[$i]['c_id']   = $key;
            $arr[$i]['c_name'] =  iconv("GB2312", "UTF-8" , $vo); 
          }
          else
          {
            $arr[$i]['c_id']   = $key;
            $arr[$i]['c_name'] = $vo; 
          }
        $i++;
      }
    }
    return $arr;
  }

  /*
   * һάά��������ת��keyΪ��ά����
   *@param array $data
   *@param string $val
   *return array $tmp_data
  */
  function array_change_up($data, $val)
  {
    $tmp_data = array();
    $i = 0;
    foreach ($data as $key => $vo) 
    {
      $tmp_data[$i][$val] = $vo;
      $i++;
    }
    return $tmp_data;
  }
  
  function getExcel($fileName,$title,$headArr,$data){
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
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  $title));
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


  /*
   * ��ά��������ת��keyΪһά����
   *@param array $data
   *@param string $val
   *return array $tmp_data
  */
  function array_change_by_val($data, $val)
  {
    $tmp_data = array();
    foreach ($data as $key => $vo) 
    {
      $tmp_data[] = $vo[$val];
    }
    return $tmp_data;
  }

  /*
     * �ж��Ƿ�Ϊmd5����
     *
  */
  function is_md5($password) 
  { 
        return preg_match("/^[a-z0-9]{32}$/", $password); 
  }

  /*������ѹ��*/
  function compressed($str)
  {
        $mtstr = base64_encode(gzdeflate($str, 9));
        //$mtstr = base64_encode($mtstr);
        return $mtstr;
  }
  /*�����ݽ�ѹ��*/
  function uncompressed($str)
  {
      $mtstr = gzinflate(base64_decode($str));
      return $mtstr;
  }

  /*
   * �����ַ���
   * @param int $min  ��Сֵ
   * @param int $max  ���ֵ
   * @param int $count ���ɸ�������
  */

    function makerand($min = 0 , $max = 9, $count = 10)
    {
        $str = '';
        for ($i = 0; $i < $count; $i++) 
        { 
            $str .= rand($min, $max);
        }
        return $str;
    }



 ?>