<?php 

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

	function change_text_by_state($state)
	{
		switch ($state) 
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
              default:
                $p_state = '';
              break;
		}
		return $p_state;
	}
	/*
     * ���ת��
     *@param string $style
     * return int $style_id
	*/
	function change_style_int($style)
	{
		$style_id = 0;
		switch ($style) 
		{
			case 'ŷ��':
				$style_id = 0;
				break;
			case '����':
				$style_id = 1;
				break;
			case '����':
				$style_id = 2;
				break;
			case '����':
				$style_id = 3;
				break;
			case '��ϵ':
				$style_id = 4;
				break;
			case '��ϵ':
				$style_id = 5;
				break;
			case '�Ը�':
				$style_id = 6;
				break;
			case '����':
				$style_id = 7;
				break;
			case '��Ƭ':
				$style_id = 8;
				break;
			case '��ҵ':
				$style_id = 9;
				break;
		}
		return $style_id;
	}

	/*
	 * �ֱ�ת��
     * @param int cup_id
     * return int $tmp_id
	*/
	function change_into_text_by_cup_id($cup_id_id)
	{ 
		switch ($cup_id_id) 
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
			default:
			   $cup_id = 0;
			   break;
		}
		return $cup_id;
	}

	/*
	 * �ֱ�ת��
     * @param int cup_id
     * return int $tmp_id
	*/
	function change_into_id_by_cup_id($cup_id)
	{
		switch ($cup_id) 
		{
			case 28:
				$tmp_id = 1;
				break;
			case 30:
				$tmp_id = 2;
				break;
			case 32:
				$tmp_id = 3;
				break;
			case 34:
				$tmp_id = 4;
				break;
			case 36:
				$tmp_id = 5;
				break;
			case 38:
				$tmp_id = 6;
				break;
			default:
			   $tmp_id = 0;
			   break;
		}
		return $tmp_id;
	}

	/*
	 * �ֱ�ת��
     * @param int cup_a_id
     * return string $cup_a
	*/
	function get_cup_text_by_cup_a_id($cup_a_id)
	{
		switch ($cup_a_id) 
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
			default:
			    $cup_a = '';
			    break;
		}
		return $cup_a;
	}
	/*
	 * �ֱ���λת��
     *@param string $cup_a
     * return int $tmp_id
	*/
	function change_into_id($cup_a)
	{
		$tmp_id = 0;
		switch ($cup_a) 
		{
			case 'A':
				$tmp_id = 1;
				break;
			case 'B':
				$tmp_id = 2;
				break;
			case 'C':
				$tmp_id = 3;
				break;
			case 'D':
				$tmp_id = 4;
				break;
			case 'E':
				$tmp_id = 5;
				break;
			case 'F':
				$tmp_id = 6;
				break;
			default :
			   $tmp_id = 0;
			   break;
		}
		return $tmp_id;
	}

	/*
     * �ж��Ƿ�Ϊmd5����
     *
	*/
	function is_md5($password) 
	{ 
        return preg_match("/^[a-z0-9]{32}$/", $password); 
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
   * ����
  */
  function select_sort($sort)
    {
        $sort_tmp;
        switch ($sort) {
            //case 'uid_desc':
            //    $sort_tmp = "inputer_time DESC"; 
            //    break;
            case 'ptime_desc':
                $sort_tmp = "inputer_time DESC";
                break;
            case 'ptime_asc':
                $sort_tmp = "inputer_time ASC";
                break;
            default:
                $sort_tmp = "inputer_time DESC";
                break;
        }
        return $sort_tmp;
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

	


 ?>