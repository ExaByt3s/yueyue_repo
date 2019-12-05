<?php 

/*
 * 
 *导出数据摄影师数据控制器
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
    	echo "<script type='text/javascript'>window.alert('您提交过来的数据有误');location.href='model_quick_search.php';</script>";
    	exit;
	}

	$data  = array();
  if (!empty($uid) && is_array($data)) 
  {
	 foreach ($uid as $key => $id) 
	 {
		//基本信息
		$info_data  = $cameraman_add_obj->get_cameraman_info($id);
    $data[$key]['id']          =  $key+1;
		$data[$key]['name']        = $info_data['name'];
		$data[$key]['weixin_name'] = $info_data['weixin_name'];
		$data[$key]['phone']       = $info_data['phone'];
		$data[$key]['weixin_id']   = $info_data['weixin_id'];
		$data[$key]['homepage']    = $info_data['homepage'];
    //个人信息
    $personal_data = $cameraman_add_obj->get_personal_info($id);
    //月花费
    $data[$key]['month_take']  = '';
    //attend_total
    $data[$key]['attend_total']= '';
    $data[$key]['is_studio']   = '有';
    $data[$key]['studio_name'] = '';
    if (!empty($personal_data) && is_array($personal_data)) 
    {
      if ($personal_data['is_studio'] == 0) 
      {
        $data[$key]['is_studio']   = '有';
        $data[$key]['studio_name'] = $personal_data['studio_name'];
      }
      else
      {
        $data[$key]['is_studio'] = '无';
        $data[$key]['studio_name'] = '';
      }
    }
		$data[$key]['photographic'] = $personal_data['photographic'];
    $data[$key]['is_fview'] = '愿意';
		if (!empty($personal_data) && is_array($personal_data)) 
    {
      if ($personal_data['is_fview'] == 0) 
      {
        $data[$key]['is_fview']   = '愿意';
      }
      else
      {
        $data[$key]['is_fview']   = '不愿意';
      }
    }
     $data[$key]['car_type'] = $personal_data['car_type'];
      //常驻城市
      $data[$key]['city'] = '广州';
      if (!empty($info_data) && is_array($info_data)) 
      {
        switch ($info_data['city']) 
        {
            case 0:
                $data[$key]['city'] = "广州";
                break;
            case 1:
                $data[$key]['city'] = "上海";
                break;
            case 2:
                $data[$key]['city'] = "北京";
                break;
            case 3:
                $data[$key]['city'] = "成都";
                break;
            case 4:
                $data[$key]['city'] = "武汉";
                break;
            case 5:
                $data[$key]['city'] = "深圳";
                break;
        }
          # code...
      }
       
	 }
  }
	$fileName = "yueyue_excel";
  $headArr = array("序号","姓名","微信名称","手机号码","微信","个人主页","月拍花费","约拍次数","是否有工作室","工作室名称","摄影器材","是否愿意出远景", "车 型", "常驻城市");
	//$headArr = array("姓名","昵称","微信名称","论坛名称","POCO用户名","APP昵称","手机号码","微信","QQ","邮箱","POCOID","职业状态","拍摄风格","报价","学校名称","来源","官方活动", "常驻城市", "录入者", "录入时间", "支付宝账号", "性别", "年龄","身高", "体重");

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
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  '摄影师列表'));
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





 ?>