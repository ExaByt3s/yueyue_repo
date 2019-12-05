<?php 

/*
 *xiao xiao 
 *模特 常用函数
 *
*/

/*
  * 一维转二维
  * 地区查询专用
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
  * 排序转换
  *@param string $sort
 */
 function change_sort($sort)
 {
 	$tmp_sort = 'inputer_time DESC';
 	switch ($sort) 
 	{
 		case 'ptime_desc':
 			$tmp_sort = 'inputer_time DESC';
 			break;
 		case 'ptime_asc':
 			$tmp_sort = 'inputer_time ASC';
 			break;
 	}
 	return $tmp_sort;
 }

/*
 * 到账状态转换
 * @param int status
 * @param string $tmp_status
*/
 function payment_status_change($status)
 {
 	if ($status == 0) 
 	{
 		$tmp_status = '待提现';
 	}
 	elseif ($status == 1) 
 	{
 		$tmp_status = '已提现';
 	}
 	else
 	{
 		$tmp_status = '已取消';
 	}
 	return $tmp_status;
 }

  /*
   * 机构提现排序
   *@param string $sort
   *return sting $tmp_sort
  */
  function payment_sort_by_sort($sort)
  {
  	 switch ($sort) 
  	 {
  	 	case 'withdraw_id':
  	 		$tmp_sort = "withdraw_id DESC";
  	 		break;
  	 	case 'withdraw_time':
  	 		$tmp_sort = "withdraw_time DESC";
  	 		break;
  	 	case 'add_time':
  	 		$tmp_sort = "add_time DESC";
  	 		break;
  	 	default:
  	 		$tmp_sort = "withdraw_id DESC";
  	 		break;
  	 }
  	 return $tmp_sort;
  }

  function change_text_by_state($state)
  {
    switch ($state) 
    {
      case 1:
                $p_state = '兼职';
                break;
              case 2:
                $p_state = '全职';
                break;
              case 3:
                $p_state = '学生';
                break;
              default:
                $p_state = '';
              break;
    }
    return $p_state;
  }
  /*
   * 罩杯转化
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
   * 罩杯转化
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


  function getExcel($fileName,$title,$headArr,$data){
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        exit;
    }
    $date = date("Y_m_d",time());
    $fileName .= "_{$date}.xls";
 
    //创建新的PHPExcel对象
    $objPHPExcel = new PHPExcel();
    $objProps = $objPHPExcel->getProperties(); 
    //设置表头
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
    foreach($data as $key => $rows){ //行写入
        $span = ord("A");
        foreach($rows as $keyName=>$value){// 列写入
            $j = chr($span);
            $objActSheet->getColumnDimension($j)->setWidth(20);
            $value = iconv('GB2312', 'utf-8',  $value);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }
 
    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //内容自适应
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  $title));
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex(0);
    //将输出重定向到一个客户端web浏览器(Excel2007)
    //ob_end_clean();//清除缓冲区,避免乱码
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
       /*   if(!empty($_GET['excel'])){
            $objWriter->save('php://output'); //文件通过浏览器下载
        }else{
          $objWriter->save($fileName); //脚本方式运行，保存在当前目录
        }*/
    $objWriter->save('php://output');
  exit;
 
}


  /*
   * 二维索引数组转化key为一维数据
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
     * 判断是否为md5加密
     *
  */
  function is_md5($password) 
  { 
        return preg_match("/^[a-z0-9]{32}$/", $password); 
  }

  /*
     * 风格转化
     *@param string $style
     * return int $style_id
  */
  function change_style_int($style)
  {
    $style_id = 0;
    switch ($style) 
    {
      case '欧美':
        $style_id = 0;
        break;
      case '情绪':
        $style_id = 1;
        break;
      case '清新':
        $style_id = 2;
        break;
      case '复古':
        $style_id = 3;
        break;
      case '韩系':
        $style_id = 4;
        break;
      case '日系':
        $style_id = 5;
        break;
      case '性感':
        $style_id = 6;
        break;
      case '街拍':
        $style_id = 7;
        break;
      case '胶片':
        $style_id = 8;
        break;
      case '商业':
        $style_id = 9;
        break;
    }
    return $style_id;
  }

  /*
     * 风格转化
     *@param string $style
     * return int $style_id
  */
  function change_style_val($style)
  {
    $style_val = '';
    switch ($style) 
    {
      case 0:
        $style_val = '欧美';
        break;
      case 1:
        $style_val = '情绪';
        break;
      case 2:
        $style_val = '清新';
        break;
      case 3:
        $style_val = '复古';
        break;
      case 4:
        $style_val = '韩系';
        break;
      case 5:
        $style_val = '日系';
        break;
      case 6:
        $style_val = '性感';
        break;
      case 7:
        $style_val = '街拍';
        break;
      case 8:
        $style_val = '胶片';
        break;
      case 9:
        $style_val = '商业';
        break;
    }
    return $style_val;
  }



 ?>