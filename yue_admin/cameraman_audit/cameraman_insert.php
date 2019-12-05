<?php

/**
 * 新增摄影师版本2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月28日
 * @version 2
 */
 include_once 'common.inc.php';
 
 $user_obj = POCO::singleton('pai_user_class');
 
 $cameraman_add_v2_obj = POCO::singleton('pai_cameraman_add_v2_class');
 
 $tpl = new SmartTemplate("cameraman_insert.tpl.htm");
 
 $act        = trim($_INPUT['act']);
 $cellphone  = trim($_INPUT['cellphone']);
 
 //提交过来的手机号码
 if($act == 'sign')
 {
 	if(!preg_match('/^1\d{10}$/isU',$cellphone))
 	{
 		echo "<script type='text/javascript'>window.alert('输入的手机号码格式有误!');location.href='cameraman_insert.php';</script>";
 		exit;	
 	}
 	$sql_str = "SELECT user_id,role FROM pai_db.pai_user_tbl WHERE cellphone='{$cellphone}'";
 	$ret = db_simple_getdata($sql_str,true,101);
 	if(is_array($ret) && !empty($ret))
 	{
 		if($ret['role'] == 'model')
 		{
 			echo "<script type='text/javascript'>window.alert('该用户的存在,并且角色不为摄影师');location.href='cameraman_insert.php';</script>";
 			exit;
 		}
 		$data['user_id']    = $ret['user_id'];
 		$data['inputer_id'] = $yue_login_id;
 		$data['add_time']   = date('Y-m-d H:i:s', time());
 		$data['source']     = 'ht';
 		$cameraman_add_v2_obj->add_info($data);
 		header("location:cameraman_detail_v2.php?user_id={$ret['user_id']}");
 		exit;
 	}
 	else 
 	{
 		//echo "no";
 		$insert_data['cellphone'] = $phone;
        $insert_data['pwd']       = 123456;
        $insert_data['role']      = 'cameraman';
        $insert_data['reg_from']  = 'ht';
        $insert_data['nickname']  = "手机用户".substr($phone,-4);
        $user_obj->create_account($insert_data);
        $user_id = $user_obj->get_user_id_by_phone($phone);
        $user_obj->update_model_db_pwd($user_id);
        
        //添加到摄影师库中
        $data['user_id']    = $user_id;
        $data['inputer_id'] = $yue_login_id;
        $data['add_time']   = date('Y-m-d H:i:s', time());
        $cameraman_add_v2_obj->add_info($data);
        header("location:cameraman_detail_v2.php?user_id={$user_id}&inputer_id={$yue_login_id}");
        exit;
 	}
 	
 	
 }
 

	
 
 
 
 
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 
 ?>
  