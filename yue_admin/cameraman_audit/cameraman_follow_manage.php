<?php 

/**
 * 摄影师跟进信息管理器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月29日
 * @version 2
 */
    
    include_once 'common.inc.php';
    include_once ("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
    
    $cameraman_add_follow_obj = POCO::singleton('pai_cameraman_add_follow_class');
    
    $tpl = new SmartTemplate("cameraman_follow_manage.tpl.htm");
    
    
    $act = trim($_INPUT['act']);
    
    //插入
    if($act == 'insert')
    {
    	$data['user_id']          = intval($_INPUT['user_id']);
    	$data['result']           = intval($_INPUT['result']);
    	$data['follow_time']      = trim($_INPUT['follow_time']);
    	$data['follow_name']      = utf8_to_gbk(trim($_INPUT['follow_name']));
    	$data['problem_type']     = utf8_to_gbk(trim($_INPUT['problem_type']));
    	$data['problem_content']  = utf8_to_gbk(trim($_INPUT['problem_content']));
    	$data['add_time'] = date('Y-m-d H:i:s',time());
    	$info = $cameraman_add_follow_obj->add_info($data);
    	if($info)
    	{
    		/* $ret = array();
    		if ($data['result'] == 0)
    		{
    			$ret['result'] = gbk_to_utf8('已解决');
    		}
    		elseif ($data['result'] == 1)
    		{
    			$ret['result'] = gbk_to_utf8('未解决');
    		}
    		elseif ($data['result'] == 2)
    		{
    			$ret['result'] = gbk_to_utf8('跟进中');
    		}
    		$ret['follow_time']     = gbk_to_utf8($data['follow_time']);
    		$ret['follow_name']     = gbk_to_utf8($data['follow_name']);
    		$ret['problem_type']    = gbk_to_utf8($data['problem_type']);
    		$ret['problem_content'] = gbk_to_utf8($data['problem_content']); */
    		//$ret['follow_name']     = gbk_utf8_to($data['follow_name']);
    		$arr  = array('msg' => 'success');
    		echo json_encode($arr);
    		exit;
    	}
    	$arr  = array('msg' => 'error');
        echo json_encode($arr);
    	exit;
    }
    //删除
    elseif($act == 'del')
    {
    	//判断是否可以删除
    	if(!in_array($yue_login_id, $user_arr))
    	{
    		js_pop_msg('你没有删除跟进的权限',true);
    		exit;
    	}
    	
    	$id = intval($_INPUT['id']);
    	if($id <1)
    	{
    		js_pop_msg('非法操作',true);
    		exit;
    	}
    	$info = $cameraman_add_follow_obj->del_info($id);
    	if($info)
    	{
    		js_pop_msg('删除成功',true);
    		exit;
    	}
    	js_pop_msg('删除失败',true);
    	exit;
    }
    //更多
    elseif($act == 'more')
    {
    	$user_id = intval($_INPUT['user_id']);
    	$list = $cameraman_add_follow_obj->get_list(false,$user_id,'','follow_time DESC,id DESC','0,99999999');
    	foreach ($list as $key=>$val)
    	{
    		$list[$key] = gbk_to_utf8($val);
    		if($val['result'] == 0) $list[$key]['result'] = gbk_to_utf8('已解决');
    		if($val['result'] == 1) $list[$key]['result'] = gbk_to_utf8('未解决');
    		if($val['result'] == 2) $list[$key]['result'] = gbk_to_utf8('跟进中');
    	}
    	$arr  = array
        (
	    	'msg'  => 'success',
	    	'list' =>  $list
        );
        echo json_encode($arr); 
    	exit;
    }
    //缩小
    elseif($act == 'less')
    {
    	$user_id = intval($_INPUT['user_id']);
    	$list = $cameraman_add_follow_obj->get_list(false,$user_id,'','follow_time DESC,id DESC','0,3');
    	foreach ($list as $key=>$val)
    	{
    		$list[$key] = gbk_to_utf8($val);
    		if($val['result'] == 0) $list[$key]['result'] = gbk_to_utf8('已解决');
    		if($val['result'] == 1) $list[$key]['result'] = gbk_to_utf8('未解决');
    		if($val['result'] == 2) $list[$key]['result'] = gbk_to_utf8('跟进中');
    	}
    	$arr  = array
    	(
    			'msg'  => 'success',
    			'list' =>  $list
    	);
    	echo json_encode($arr);
    	exit;
    }
    
    
    $user_id = intval($_INPUT['user_id']);
    $tpl->assign('user_id', $user_id);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 	
    
    
 ?>