<?php
/**
 * 
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-02-28 12:04:01
 * @version 1
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
      $i = 0;
      foreach ($data as $key => $vo) 
      {   if ($b_select == true) 
        {
          $arr[$i]['c_id']   = $key;
            $arr[$i]['c_name'] =  iconv("GBK", "UTF-8" , $vo); 
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
  /*长数据压缩*/
  function compressed($str)
  {
        $mtstr = base64_encode(gzdeflate($str, 9));
        //$mtstr = base64_encode($mtstr);
        return $mtstr;
  }
  /*长数据解压缩*/
  function uncompressed($str)
  {
      $mtstr = gzinflate(base64_decode($str));
      return $mtstr;
  }

  /**
   * 导出excel 数据
   * @param  [string] $fileName [文件名称]
   * @param  [string] $title    [excel 名称]
   * @param  [array] $headArr   [导出数据]
   * @param  [array] $data     [数据名称]
   * @return [void]           [description]
   */
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
        $v = iconv('GBK','utf-8', $v);
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
            $value = iconv('GBK', 'utf-8',  $value);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }
 
    //$fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    //$objActSheet->getColumnDimension( 'B')->setAutoSize(true);   //内容自适应
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GBK', 'utf-8',  $title));
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

/**
 * 获取后缀名
 * @param  [string] $filename [文件名]
 * @return [type]           [description]
 */
function getPrefix($filename)
{
   return substr($filename ,strrpos($filename, '.')); 
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

function getStr($len) 
{ 
  $arr = array( 
    "1", "2", "3", "4", "5", "6", "7", "8", "9",
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", 
    "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", 
    "H", "J", "K", "L", "M", "N", "P", "Q", "R",
    "S", "T", "U", "V", "W", "X", "Y", "Z", 
  ); 
  $charslen = count($arr) - 1;
  $outputstr = "";
  for ($i = 0 ; $i< $len; $i++)
  {
    $outputstr .= $arr[mt_rand(0, $charslen)]; 
  }
  return $outputstr; 
}


 //数组组合函数
function combine_arr2($arr1, $arr2, $val)
{
  $arr = $arr1;
  foreach ($arr1 as $key => $vo) 
  {
    foreach ($arr2 as $key2 => $vo2) 
    {
      if(in_array($vo2[$val], $vo))
      {
        $arr[$key] = array_merge($arr[$key], $vo2);
        unset($arr1[$key]);
        //unset($arr2[$key2]);
      }
    }
    # code...
  }
  return $arr;

}

if (!function_exists('combine_arr'))
{
  /**
   * 根据同一字段的值，合并两个一对一的数组
   */
  function combine_arr($arr1, $arr2, $same_filed)
  {
      foreach ($arr1 as $key => $val)
      {
          foreach ($arr2 as $k => $v)
          {
              if ($val[$same_filed] == $v[$same_filed]) {
                  $new_arr[$key] = array_merge((array)$val, (array)$v);
                  unset($arr2[$k]);
                  break;
              }
          }
          if (empty($new_arr[$key])) {
              $new_arr[$key] = $val;
          }
      }
      return $new_arr;
  }
}


  /**
   * GBK转成UTF-8
   * @param string|array $str
   */
  function gbk_to_utf8($str)
  {
    if( is_string($str) )
    {
      $str = iconv('gbk', 'utf-8', $str);
    }
    elseif( is_array($str) )
    {
      foreach ($str as $key=>$val)
      {
        $str[$key] = gbk_to_utf8($val);
      }
    }
    return $str;
  }
  
  /**
   * UTF-8转成GBK
   * @param string|array $str
   */
  function utf8_to_gbk($str)
  {
    if( is_string($str) )
    {
      $str = iconv('utf-8', 'gbk//IGNORE', $str);
    }
    elseif( is_array($str) )
    {
      foreach ($str as $key=>$val)
      {
        $str[$key] = utf8_to_gbk($val);
      }
    }
    return $str;
  }


 /**
  * 获取一个月的第一天
  * $month 年月 2015-4
  * $b_select_count 是否显示时间(默认为时间戳)
  * $b_select_count 是否显示小时,分秒(默认只显示到天)
  */
 if (!function_exists('beginMonth'))
 {
     function beginMonth($month,$b_select_count = false,$is_second = false)
     {
        $begin_time = mktime(0,0,0,date('m', strtotime($month)),1,date('Y',strtotime($month)));
        if($b_select_count == true)
        {
           $begin_time = $is_second == true ? date('Y-m-d H:i:s',$begin_time) : date('Y-m-d',$begin_time);
        }
        return $begin_time;
     }
 }
  
 /**
  * 获取一个月的最后一条
  * $month 年月 2015-4
  * $b_select_count 是否显示时间(默认为时间戳)
  * $b_select_count 是否显示小时,分秒(默认只显示到天)
  */
 if (!function_exists('endMonth'))
 {
    function endMonth($month,$b_select_count = false,$is_second = false)
    {

     $end_time = mktime(23,59,59,date('m', strtotime($month)),date('t',strtotime($month)),date('Y',strtotime($month)));
     if($b_select_count == true)
     {
        //$end_time = date('Y-m-d H:i:s',$end_time);
        $end_time = $is_second == true ? date('Y-m-d H:i:s',$end_time) : date('Y-m-d',$end_time);
     }
     return $end_time;
    }
 }
 