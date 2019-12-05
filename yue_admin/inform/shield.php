<?php
/**
 * 屏蔽用户类
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 17:31:16
 * @version 1
 */
 include('common.inc.php');
 $tpl = new SmartTemplate("shield_list.tpl.htm");
 //举报类
 $log_inform_obj = POCO::singleton('pai_log_inform_class');
 //屏蔽类
 $inform_shield_obj = POCO::singleton('pai_log_inform_shield_class');
 $page_obj = new show_page ();
 $show_count = 20;
 $act        = $_INPUT['act'] ? $_INPUT['act'] : '';
 $start_time = $_INPUT['start_time'] ? $_INPUT['start_time'] : '';
 $end_time   = $_INPUT['end_time']   ? $_INPUT['end_time'] : '';
 $user_id    = $_INPUT['user_id'] ?  (int)$_INPUT['user_id'] : '';
 $audit_id    = $_INPUT['audit_id'] ? (int)$_INPUT['audit_id'] : '';

 //移除屏蔽
 if ($act == 'remove') 
 {
 	$id = $_INPUT['id'] ? (int)$_INPUT['id'] : 0;
 	if (!$id) 
 	{
 		echo "<script type='text/javascript'>window.alert('非法操作');location.href='shield.php';</script>";
 		exit;
 	}
 	$ret = $inform_shield_obj->get_info($id);
 	if (is_array($ret)) 
    {
 		$info  = $inform_shield_obj->shield_user($ret['user_id'], 'remove');
 		$info2 =$log_inform_obj->update_info(array('state'=> 0), $ret['inform_id']);
 	}
 	$delete_info = $inform_shield_obj->delete_info($id);
 	if ($delete_info && $info && $info2) 
 	{
 		echo "<script type='text/javascript'>window.alert('操作成功');location.href='shield.php';</script>";
 		exit;
 	}
 	echo "<script type='text/javascript'>window.alert('操作失败');location.href='shield.php';</script>";
 		exit;
 }
 //列表
 $where_str = "1";
 if ($start_time) 
 {
 	$tmp_start_time = strtotime($start_time);
 	$where_str .= " AND add_time >= {$tmp_start_time}";
 }
 if ($end_time) 
 {
 	$tmp_end_time = strtotime($end_time)+24*3600;
 	$where_str .= " AND add_time <= {$tmp_end_time}";
 }
 if ($user_id) 
 {
 	$where_str .= " AND user_id= {$user_id}";
 }
 if ($audit_id) 
 {
 	$where_str .= " AND audit_id = {$audit_id}";
 }
 $total_count = $inform_shield_obj->get_shield_list(true, $where_str);
 $page_obj->setvar (
        array
        (
          'start_time'  => $start_time,
          'end_time'    => $end_time,
          'user_id'     => $user_id,
          'audit_id'    => $audit_id
          )
      );
 $page_obj->set ( $show_count, $total_count );
 $list = $inform_shield_obj->get_shield_list(false,$where_str,"id DESC", $page_obj->limit());
 if (is_array($list)) 
 {
 	foreach ($list as $key => $vo) 
 	{
 		$list[$key]['add_time']  = date('Y-m-d H:i:s', $vo['add_time']);
 		$list[$key]['nickname']  = get_user_nickname_by_user_id($vo['user_id']);
 		$list[$key]['auditname'] = get_user_nickname_by_user_id($vo['audit_id']);
 	}

 }
 $tpl->assign('start_time', $start_time);
 $tpl->assign('end_time', $end_time);
 $tpl->assign('user_id', $user_id);
 $tpl->assign('audit_id', $audit_id);
 $tpl->assign('list', $list);
 $tpl->assign('total_count', $total_count);
 $tpl->assign ( "page", $page_obj->output ( 1 ) );
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();

 ?>