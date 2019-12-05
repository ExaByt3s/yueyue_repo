<?php 


	include_once 'common.inc.php';
    check_authority(array('cameraman'));
	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
    $act             = $_INPUT['act']     ? $_INPUT['act'] : 'add';
    $tpl = new SmartTemplate("cameraman_add_follow.tpl.htm");
 	switch ($act) {
 		//添加界面
 		case 'add':
 			  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
              $tpl->output();
        break;
        //数据插入
 		case 'insert':
 			  $data['uid']              = $_INPUT['uid'] ? intval($_INPUT['uid']) : 0;
 			  $data['follow_time']      = $_INPUT['follow_time'] ? $_INPUT['follow_time'] : '';
 			  $data['follow_name']      = $_INPUT['follow_name'] ? iconv("UTF-8", "GB2312" ,$_INPUT['follow_name']) : '';
 			  $data['problem_type']     = $_INPUT['problem_type'] ? iconv("UTF-8", "GB2312" ,$_INPUT['problem_type'] ): '';
 			  $data['result']           = $_INPUT['result'] ? intval($_INPUT['result']) : 0;
 			  $data['problem_content']  = $_INPUT['problem_content'] ? iconv("UTF-8", "GB2312" ,$_INPUT['problem_content']) : '';
 			  $ret = $cameraman_add_obj->insert_cameraman_follow($data);
 			  if (!empty($ret) && is_array($ret)) 
 			  {
                if ($ret['result'] == 0) 
                {
                   $ret['result'] = iconv('GB2312','UTF-8','已解决');
                }
                elseif ($ret['result'] == 1) 
                {
                    $ret['result'] = iconv('GB2312','UTF-8','未解决');
                }
                elseif ($ret['result'] == 2) 
                {
                    $ret['result'] = iconv('GB2312','UTF-8','跟进中');
                }
                $ret['problem_type']    = iconv('GB2312','UTF-8',$ret['problem_type']);
                $ret['problem_content'] = iconv('GB2312','UTF-8',$ret['problem_content']);
                $ret['follow_name']     = iconv('GB2312','UTF-8',$ret['follow_name']);
 			  }
              //print_r($ret);exit;
 			  $arr  = array
 			  (
 			  	'msg' => 'success' ,
 			  	'ret' => $ret
 			  );
 			  echo json_encode($arr);
 			break;
 	}
 	
    
    
    
 ?>