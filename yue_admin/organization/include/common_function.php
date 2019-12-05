<?php 

/*
 *xiao xiao 
 *机构模特 常用函数
 *
 *
*/

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



 ?>