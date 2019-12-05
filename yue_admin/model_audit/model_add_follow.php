<?php 


	include_once 'common.inc.php';
 	$model_add_obj  = POCO::singleton('pai_model_add_class');
    $tpl = new SmartTemplate("model_add_follow.tpl.htm");
    $uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
    $act = $_INPUT['act'] ? $_INPUT['act'] : '';
 	if ($act == 'insert') 
    {
         $data['follow_time'] = $_INPUT['follow_time'] ? iconv("UTF-8", "gbk" ,$_INPUT['follow_time']) : '';
         $data['follow_name'] = $_INPUT['follow_name'] ? iconv("UTF-8", "gbk" ,$_INPUT['follow_name']) : '';
         $data['problem_type'] = $_INPUT['problem_type'] ? iconv("UTF-8", "gbk" ,$_INPUT['problem_type']) : '';
         $data['result'] = $_INPUT['result'] ? iconv("UTF-8", "gbk" , $_INPUT['result']) : '';
         $data['problem_content'] = $_INPUT['problem_content'] ? iconv("UTF-8", "gbk" ,$_INPUT['problem_content']) : '';
          $data['uid']     = $uid;
          $ret = $model_add_obj->insert_model_follow($data);
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
        $arr  = array
        (
          'msg' => 'success' ,
          'ret' => $ret
        );
        echo json_encode($arr);
        exit;
    }
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->assign('uid', $uid);
    $tpl->output();
 	
 	
    
 ?>