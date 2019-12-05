<?php 

/*
 * 
 *导出数据控制器
 *
 *
*/
  include_once './include/Classes/PHPExcel.php';
  include_once 'common.inc.php';
  include_once 'config/authority_model_audit.php';
  $user_obj = POCO::singleton ( 'pai_user_class' );
  $user_add_obj = POCO::singleton ( 'pai_model_add_class' );
  $model_audit_obj = POCO::singleton('pai_model_audit_class');
  $model_card_obj = POCO::singleton('pai_model_card_class');
  $where ="is_approval=1";
  $data = array();
	$list = $model_audit_obj->get_model_list(false, $where, 'audit_time DESC,add_time DESC', '0,1000');
  foreach($list as $k=>$val){
    $data[$k]['key']       = $k+1;
    $data[$k]['user_id']   = $val['user_id']; 
    $data[$k]['nickname']  = get_user_nickname_by_user_id($val ['user_id'] );
    $data[$k]['cellphone'] = $val['cellphone'];     
    $data[$k]['add_time'] = date("Y-m-d",$val['add_time']);    
    $data[$k]['user_icon'] = str_replace("_86","_100",get_user_icon($val ['user_id'], 86));
    $is_complete =  $model_card_obj->check_input_is_complete($val ['user_id']);
    $data[$k]['is_complete'] = !empty($is_complete)? '是':'否';
    $data[$k]['is_set']      = $user_add_obj->get_user_inputer_name_by_user_id($val['user_id']);
    $data[$k]['audit_name']  = get_user_nickname_by_user_id($val['audit_user_id']);
    $data[$k]['audit_time'] = !empty($val['audit_time']) ? date("Y-m-d", $val['audit_time']) : '';  
}
$fileName = "yueyue_excel";
  $headArr = array("序号","用户ID","昵称","手机","注册时间","U头像","资料是否完整","是否入库","操作者","操作时间");
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