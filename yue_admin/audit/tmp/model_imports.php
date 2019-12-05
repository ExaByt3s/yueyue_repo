<?php

   //数据导入
   include_once './include/Classes/PHPExcel.php';
   include_once 'common.inc.php';
   check_authority_by_list('exit_type',$authority_list,'model', 'is_insert');
   check_authority(array('model'));
   ini_set('memory_limit', '-1');
   //error_reporting(0);
   //ini_set('memory_limit','8M');
   $tpl = new SmartTemplate("model_imports.tpl.htm");
   $act = $_INPUT['act'] ? $_INPUT['act'] : 'set';
   switch ($act) 
   {
    	case 'set':
    		$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
            $tpl->output();
    		break;
    	case 'upload':
    		 $filename = $_FILES['inputExcel']['name'];
    		 $prefixName = getPrefix($filename);
    		 if ($prefixName != '.xls') 
             {
               echo "<script type='text/javascript'>window.alert('您上传文件格式有误!');location.href='model_imports.php';</script>";
               exit;
             }
            $tmp_name = $_FILES['inputExcel']['tmp_name'];
            $info = uploadFile($filename,$tmp_name);
           /* if ($info) 
            {
            	echo "<script type='text/javascript'>window.alert('您导入成功!');location.href='model_term_search.php';</script>";
               exit;
            }
            else
            {
            	echo "<script type='text/javascript'>window.alert('您导入失败,请检查你的的文件是否格式不对!');location.href='model_imports.php';</script>";
               exit;
            }*/
    	    break;
   } 
//返回后缀名xls
function getPrefix($filename)
{
   return substr($filename ,strrpos($filename, '.'));  
}
//导入Excel文件
function uploadFile($file,$filetempname) 
{
	     $user_obj     = POCO::singleton('pai_user_class');
	     $model_add_obj = POCO::singleton('pai_model_add_class');
       $model_card_obj = POCO::singleton ( 'pai_model_card_class' );
       setlocale(LC_ALL, 'zh_CN');
        $objReader     = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
        //$objReader->setInputEncoding('GB2312');
        $objPHPExcel   = $objReader->load($filetempname); 
        $sheet         = $objPHPExcel->getSheet(0); 
        $highestRow    = $sheet->getHighestRow();           //取得总行数 
        $highestColumn = $sheet->getHighestColumn(); //取得总列数
        $objWorksheet  = $objPHPExcel->getActiveSheet();
        $highestRow    = $objWorksheet->getHighestRow(); 
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
        //$headtitle = array(); 
        $arr = array();
        //提示成功数
        $successs = 0 ;
        //失败
        $fail = 0;
        $error_str = '';
        for ($row = 2;$row <= $highestRow; $row ++) 
        {
            $strs = array();
            //注意highestColumnIndex的列数索引从0开始
            for ($col = 0; $col < $highestColumnIndex ; $col++)
            {
                $strs[$col] = trim(iconv('utf-8','GB2312', $objWorksheet->getCellByColumnAndRow($col, $row)->getValue()));
            }
            $phone = (int)$strs[6];
            if (empty($phone) || !preg_match('/^1\d{10}$/isU',$phone)) 
            {
               $fail ++;
               $error_str .= "第{$row}行手机号码格式不正确:[{$phone}]\n";
               continue;
            }
            $user_id = $user_obj->get_user_id_by_phone($phone);
            //没有用户创建用户
            if (empty($user_id)) 
            {
                 $insert_data['cellphone'] = $phone;
                 $insert_data['pwd']       = 123456;
                 $insert_data['role']      = 'model';
                 $insert_data['nickname']  = "手机用户".substr($phone,-4);
                 $user_obj->create_account($insert_data);
                 $user_id = $user_obj->get_user_id_by_phone($phone);
                 $user_obj->update_model_db_pwd($user_id);
                 $insert_model ["user_id"] = $user_id;
                 $model_card_obj->add_model_card ( $insert_model );
            }
            $uid = $model_add_obj->get_model_id_by_phone($phone);
            if (!empty($uid)) 
            {
              $fail ++;
              $error_str .= "第{$row}行该用户已经存在,手机号码为:[{$phone}]\n";
              continue;
            }
            //基本信息
            $data_info['name']         = $strs[0];
            $data_info['nick_name']    = $strs[1];
            $data_info['weixin_name']  = $strs[2];
            $data_info['discuz_name']  = $strs[3];
            $data_info['poco_name']    = $strs[4];
            $data_info['app_name']     = $strs[5];
            $data_info['phone']        = $phone;
            $data_info['weixin_id']    = $strs[7];
            $data_info['qq']           = $strs[8];
            $data_info['email']        = $strs[9];
            $data_info['poco_id']      = (int)$strs[10];
            $data_info['inputer_name'] = $strs[16];
            $data_info['inputer_time'] = $strs[17];
            //获取城市
            $ret = POCO::execute('common.get_location_2_location_id', $strs[15]);
            if (!empty($ret) && is_array($ret)) 
            {
               $data_info['location_id'] = $ret['location_id'];
            }
            //城市处理
            $model_add_obj->insert_model_info(true ,$user_id,$data_info);
            //职业信息
            //$data_prof['p_state']      = $strs[12];
            switch ($strs[11]) 
            {
                case '兼职':
                   $data_prof['p_state'] = 1;
                 break;
                case '全职':
                   $data_prof['p_state'] = 2;
                break;
                case '学生':
                    $data_prof['p_state'] = 3;
                break;              
             }
             //风格先不处理
            if (!empty($strs[12])) 
            {
              $style_data = array();
              $style_data = explode('、', $strs[12]);
              foreach ($style_data as $style_key => $vo) 
              {
                 switch ($vo) 
                 {
                   case '欧美':
                     $data_style['style'] = 0;
                     break;
                   case '情绪':
                     $data_style['style'] = 1;
                     break;
                   case '清新':
                     $data_style['style'] = 2;
                     break;
                  case '复古':
                     $data_style['style'] = 3;
                     break;
                  case '韩系':
                     $data_style['style'] = 4;
                     break;
                  case '日系':
                     $data_style['style'] = 5;
                     break;
                  case '性感':
                     $data_style['style'] = 6;
                     break;
                  case '街拍':
                     $data_style['style'] = 7;
                     break;
                  case '胶片':
                     $data_style['style'] = 8;
                     break;
                  case '商业':
                     $data_style['style'] = 9;
                     break;
                  default:
                     $data_style['style'] = 0;
                  break;
                 }
                 $model_add_obj->add_style($user_id,$data_style);
              }
            }
            $data_prof['p_school']     = $strs[13];
            $model_add_obj->insert_model_profession($user_id, $data_prof);
            //其他信息
            $data_other['information_sources'] = $strs[14];
            $data_other['alipay_info']  = $strs[18];
            $model_add_obj->insert_model_other($user_id, $data_other);
            //身材信息
            $sex          = (int)$strs[19];
            if($sex == '女') 
            {
              $data_stature['sex'] = 0;
            }
            else
            {
               $data_stature['sex'] = 1;
            }
            $data_stature['age']          = $strs[20];
            $data_stature['height']       = $strs[21];
            $data_stature['weight']       = $strs[22];
            if (!empty($strs[23])) 
            {
              $cup_arr = explode('/', $strs[23]);
              $cup_id = 0;
              $cup_a  = 0;
              switch ($cup_arr[0]) 
              {
                case 28:
                  $cup_id = 1;
                  break;
                case 30:
                  $cup_id = 2;
                  break;
                case 32:
                  $cup_id = 3;
                  break;
                case 34:
                  $cup_id = 4;
                  break;
                case 36:
                  $cup_id = 5;
                  break;
                case 38:
                  $cup_id = 6;
                  break;
              }
              switch ($cup_arr[1]) 
              {
                case 'A':
                  $cup_a = 1;
                  break;
                case 'B':
                  $cup_a = 2;
                  break;
                case 'C':
                  $cup_a = 3;
                  break;
                case 'D':
                  $cup_a = 4;
                  break;
                case 'E':
                  $cup_a = 5;
                  break;
                case 'F':
                  $cup_a = 6;
                  break;
              }
              $data_stature['cup_id']  = $cup_id;
              $data_stature['cup_a']   = $cup_a;
            }
            if (!empty($strs[24])) 
            {
              $bwh_arr = explode('/', $strs[24]);
              $data_stature['chest']        = (int)$bwh_arr[0];
              $data_stature['waist']        = (int)$bwh_arr[1];
              $data_stature['chest_inch']   = (int)$bwh_arr[2];
            }
            $data_stature['shoe_size']    = $strs[25];
            $model_add_obj->insert_model_stature($user_id, $data_stature);
            $successs ++;
        }
        if ($successs == 0) 
        {
          $msg = '导入失败';
        }
        else
        {
          $msg = '导入成功';
        }
        /*echo "<script type='text/javascript' src='resources/js/jquery.min.js'></script>";
        echo "<script type='text/javascript' src='js/layer/layer.min.js'></script>";
        echo "<script type='text/javascript'>layer.msg({$msg});</script>";*/
        //var_dump($error_str);exit;
        $filePath = 'error_log.txt';
        $highestRow = $highestRow - 1;
        $error_log = "总共有{$highestRow}条数数据,成功条数为{$successs};失败条数为{$fail};\n{$error_str}";
        file_put_contents($filePath, $error_log);
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header("Content-Length: ". filesize($filePath));
        readfile($filePath);
        /*if ($successs == 0) 
        {
          $msg = 'noo';
        }
        else
        {
          $msg = true;
        }*/
    
}
?>
