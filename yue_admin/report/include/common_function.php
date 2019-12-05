<?php 

/*
 *xiao xiao 
 *机构模特 常用函数
 *
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

  function getExcel($fileName,$title,$headArr,$data,$ret=array(),$ret_avg = array())
  {
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        echo "名称不能为空";
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

    //开始循环累计
    if ($ret) 
    {
      $j = chr(ord("A"));
      $objActSheet->getColumnDimension($j)->setWidth(20);
      $value = iconv('GB2312', 'utf-8',  '累计');
      $objActSheet->setCellValue($j.$column, $value);
      $span = ord('B');
      foreach ($ret as $key => $value) 
      {
        $j = chr($span);
        $objActSheet->getColumnDimension($j)->setWidth(20);
        $value = iconv('GB2312', 'utf-8',  $value);
        $objActSheet->setCellValue($j.$column, $value);
        $span++;
      }
      $column++;
      //echo $colum;
    }

    //exit;
    //日均
    if ($ret_avg) 
    {
      $j = chr(ord("A"));
      //$column++;
      $objActSheet->getColumnDimension($j)->setWidth(20);
      $value = iconv('GB2312', 'utf-8',  '日均');
      $objActSheet->setCellValue($j.$column, $value);
      $span = ord('B');
      foreach ($ret_avg as $key => $value) 
      {
        $j = chr($span);
        $objActSheet->getColumnDimension($j)->setWidth(20);
        $value = iconv('GB2312', 'utf-8',  $value);
        $objActSheet->setCellValue($j.$column, $value);
        $span++;
      }
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
   * 生成字符串
   * @param int $min  最小值
   * @param int $max  最大值
   * @param int $count 生成个数个数
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