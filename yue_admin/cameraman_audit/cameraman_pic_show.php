<?php 

/**
 * 摄影师库图片展示2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月29日
 * @version 2
 */

	include_once 'common.inc.php';
	
	$cameraman_add_v2_obj  = POCO::singleton('pai_cameraman_add_v2_class');
	
	$pic_obj = POCO::singleton('pai_pic_class');
	
 	$tpl = new SmartTemplate("cameraman_pic_show.tpl.htm");
 	 	
 	$user_id = intval($_INPUT['user_id']);
 	
 	if($user_id < 1)
 	{
 		js_pop_msg('非法操作');
 		exit;
 	}
 	
 	$act   = trim($_INPUT['act']);
 	//删除图片
 	if($act == 'del')
 	{
 		$ids = $_INPUT['ids'];
 		if(!is_array($ids))
 		{
 			js_pop_msg('请选择图片',false,'cameraman_pic_show.php?user_id='.$user_id);
 			exit;
 		}
 		foreach ($ids as $key=>$id)
 		{
 			$pic_obj->del_pic_by_id($id);
 			//$cameraman_add_pic_obj->del_info($id);
 		}
 		js_pop_msg('删除成功',false,"cameraman_pic_show.php?user_id=".$user_id);
 		exit;
 	}
 		
 	
 	$where_str = '';
 	$setParam  = array();
 	
 	if($user_id >0)
 	{
 		$setParam['user_id'] = $user_id;
 	}
 	
 	$list = $pic_obj->get_user_pic($user_id);
 	if(!is_array($list)) $list = array();
 	
 	foreach ($list as $key=>$val)
 	{
 		$list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
 		$list[$key]['img_url']      = str_replace('_260.', '.', $val['img']);
 	}
 	
 	
    $tpl->assign('list', $list);
    $tpl->assign($setParam);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();
 ?>