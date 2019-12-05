<?php 

/*
 * 
 *导出数据控制器
 *
 *
*/
	 include_once './include/Classes/PHPExcel.php';
	 include_once 'common.inc.php';
   //地区引用
   include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
   check_authority(array('model'));
	 $model_add_obj  = POCO::singleton('pai_model_add_class');
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
		$info_data  = $model_add_obj->get_model_info($id);
    //print_r($info_data);
		//职业
		$prof_data  = $model_add_obj->get_model_profession($id);
		//风格及风格价格
		$style_data = $model_add_obj->list_style($id);
		//其他信息
		$other_data = $model_add_obj->get_model_other($id);
		//身材信息
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
        //职业选择
        $p_state  = '';
        $p_school = '';
        if (!empty($prof_data) && is_array($prof_data)) 
        {
          switch ($prof_data['p_state']) 
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
        }
        $p_school = $prof_data['p_school'];
      }
      $data[$key]['p_state'] = $p_state;
      //风格和价格
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
                    $style_name  = '欧美';
                    break;
                case 1:
                    $style_name = '情绪';
                    break;
                case 2:
                    $style_name = '清新';
                    break;
                case 3:
                    $style_name = '复古';
                    break;
                case 4:
                    $style_name = '韩系';
                    break;
                case 5:
                    $style_name = '日系';
                    break;
                case 6:
                    $style_name = '性感';
                    break;
                case 7:
                    $style_name = '街拍';
                    break;
                case 8:
                    $style_name = '胶片';
                    break;
                case 9:
                    $style_name = '商业';
                    break;
                default:
                    # code...
                    break;
            }
           $twoh_price  = $style_name.":".$vo['twoh_price']."/2小时";
           $fourh_price = $style_name.":".$vo['fourh_price']."/4小时";
           $addh_price  = $style_name.":".$vo['addh_price']."/小时(加时)";
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
      //学校
      $data[$key]['p_school']  = $p_school;
      //来源
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
      //城市(名称/性别/年龄有问题)
      $data[$key]['city']         = $city;
      $data[$key]['inputer_name'] = $info_data['inputer_name'];
      $data[$key]['inputer_time'] = $info_data['inputer_time'];
      $data[$key]['alipay_info']  = $other_data['alipay_info'];
      //性别
      $sex = '女';
      if (!empty($stature_data) && is_array($stature_data)) 
      {
          switch ($stature_data['sex']) 
          {
              case 0:
                  $sex = "女";
                  break;
               case 1:
                  $sex = "男";
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
  $headArr = array("序号","姓名","昵称","微信名称","论坛名称","POCO用户名","APP昵称","手机号码","微信","QQ","邮箱","POCOID","职业状态","学校名称","来源","官方活动", "常驻城市", "录入者", "录入时间", "支付宝账号", "性别", "年龄","身高", "体重", "罩杯", "三围" );
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
    $objPHPExcel->getActiveSheet()->setTitle(iconv('GB2312', 'utf-8',  '模特列表'));
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