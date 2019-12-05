<?php

/**
 * ��Ӱʦ����汾2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��24��
 * @version 2
 */
 
 include_once ("common.inc.php");
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/locate_file.php");
 include_once ("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
 
 $user_obj  = POCO::singleton('pai_user_class');
 $cameraman_obj_log  = POCO::singleton('pai_cameraman_comment_log_class');
 
 $cameraman_add_v2_obj = POCO::singleton('pai_cameraman_add_v2_class');
 
 //��Ӱʦͷ��
 $user_icon_obj  = POCO::singleton("pai_user_icon_class");
 
 //�˻���
 $payment_obj = POCO::singleton ( 'pai_payment_class' );
 
 //��Ӱʦ�ȼ�
 $user_level_obj = POCO::singleton('pai_user_level_class');
 
 //ģ�ض���Ӱʦ��������
 $cameraman_comment_log_obj = POCO::singleton('pai_cameraman_comment_log_class');
 
 //������
 $cameraman_add_follow_obj = POCO::singleton('pai_cameraman_add_follow_class');
 
 //��Ӱʦ���ǩ��
 $cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
 
 //��ǩ�б���
 $cameraman_add_label = POCO::singleton('pai_cameraman_add_label_class');
 
 $tpl = new SmartTemplate("cameraman_detail_v2.tpl.htm");
 
 //ֵ
 $user_id = intval($_INPUT['user_id']);
 
 if($user_id <1) die('�Ƿ�����');
 $role = $user_obj->check_role($user_id);
 if($role != 'cameraman') {die('���û�������Ӱʦ');}
 
 
 $setParam = array();
 $setParam['super'] = 0;
 //�ж��û��Ƿ���Բ�������label
 if(in_array($yue_login_id, $user_arr))
 {
 	$setParam['super'] = true;
 }
 
 
 //��ȡuser����
 $user_info = $user_obj->get_user_info($user_id);
 //print_r($user_info);
 if(is_array($user_info))
 {
 	$setParam['nickname']      = $user_info['nickname'];
 	$setParam['location_name'] =  get_poco_location_name_by_location_id($user_info['location_id']);
 	$setParam['cellphone']     = $user_info['cellphone'];
 }
 
 //��Ӱʦͷ��
 $setParam['icon_user'] = $user_icon_obj->get_user_icon($user_id,165);

 $ret = $cameraman_add_v2_obj->get_info($user_id); 
 if(is_array($ret))
 {
 	//¼����
 	$setParam['inputer_name']  = get_user_nickname_by_user_id($ret['inputer_id']);
 	//$setParam['location_name'] = get_poco_location_name_by_location_id($ret['location_id']);
 	$ret['goods_style']  = str_replace('|', ',', $ret['goods_style']);
 	
 	$ret['join_time']  = date('Y-m',strtotime($ret['join_time'])) == '-0001-11' ? '':date('Y-m',strtotime($ret['join_time']));
 	//print_r($ret);
 	if($ret['login_sum']==0) $ret['login_name'] = '��Ĭ';
 	if($ret['login_sum']>0 && $ret['login_sum'] <= 5) $ret['login_name'] = '��Ծ';
 	if($ret['login_sum']>5) $ret['login_name'] = '����';
 	//����ˮƽ
 	if($ret['consumption_level']>=0 && $ret['consumption_level']<=100 )   $ret['consumption_name'] = '�ͼ�';
 	if($ret['consumption_level']>=101 && $ret['consumption_level']<=200 ) $ret['consumption_name'] = '�ϵ�';
 	if($ret['consumption_level']>=201 && $ret['consumption_level']<=400 ) $ret['consumption_name'] = '����';
 	if($ret['consumption_level']>=401 && $ret['consumption_level']<=600 ) $ret['consumption_name'] = '�ϸ�';
 	if($ret['consumption_level']>600) $ret['consumption_name'] = '�߼�';
 }
 
 
 //��ȡ�û���ǩ ���10��ǩ
 $label_ret = $cameraman_add_user_label->get_list(false,$user_id,'','id ASC','0,10');
 if(!is_array($label_ret)) $label_ret = array();
 
 $label_tmp_str = '';
 foreach ($label_ret as $key=>$label_val)
 {
 	if($key !=0) $label_tmp_str .= ',';
 	$label_tmp_str .= $label_val['label_id'];
 }
 
 if(strlen($label_tmp_str)>0)
 {
 	$sql_label_str = "id IN ({$label_tmp_str})";
 	$label_list_ret = $cameraman_add_label->get_list(false,'', $sql_label_str,'id ASC','0,10','id as label_id,label');
 	if(is_array($label_list_ret)) $label_ret = combine_arr($label_ret, $label_list_ret, 'label_id');
 }
 
 foreach ($label_ret as $key=>$label_vo)
 {
 	if($key != 0)
 	{
 		$setParam['label_name'] .= ',';
 		$setParam['label_id']   .= ',';
 	}
 	$setParam['label_name'] .= $label_vo['label'];
 	$setParam['label_id'] .= $label_vo['label_id'];
 }
 
 
 //��ǩ����
 
 
 //��ȡ������Ϣ
 $follow_ret = $cameraman_add_follow_obj->get_list(false,$user_id,'','follow_time DESC,id DESC','0,3');
 foreach ($follow_ret as $key=>$follow_val)
 {
 	$follow_ret[$key]['super'] = $setParam['super'];
 }
 
 /* print_r($follow_ret);
 exit; */
 
 //�˻���Ϣ
 $account_info = $payment_obj->get_user_account_info ( $user_id );
 
 //��Ӱʦ�ȼ�
 $level = $user_level_obj->get_user_level($user_id);
 
 
 //Լ����Ϣ
/* $sql_str = "SELECT dat.date_time,dat.date_id,dat.date_style,dat.to_date_id,dat.date_style,det.budget FROM event_db.event_date_tbl dat,event_db.event_details_tbl det WHERE dat.from_date_id= {$user_id} AND dat.event_id = det.event_id AND dat.date_status='confirm' AND det.event_status='2' ORDER BY date_time DESC,to_date_id DESC LIMIT 0,3 ";
 $date_ret = db_simple_getdata($sql_str,false);*/
 if(!is_array($date_ret)) $date_ret = array();
 //��ȡ����
 $date_tmp_str = '';
 $nickname_tmp_str = '';
 foreach ($date_ret as $key=>$date_val)
 {
 	if($key !=0)
 	{
 		$date_tmp_str .= ',';
 		$nickname_tmp_str .= ',';
 	}
 	$date_ret[$key]['date_time'] = date('Y-m-d H:i:s',$date_val['date_time']);
 	$date_tmp_str .= $date_val['date_id'];
 	$nickname_tmp_str .= $date_val['to_date_id'];
 }
 
 //��ȡ����
 if(strlen($date_tmp_str) >0)
 {
 	$date_sql_str = "date_id IN ({$date_tmp_str})";
 	$comment_ret = $cameraman_comment_log_obj->get_comment_list(false,$date_sql_str,'id DESC','0,10','date_id,overall_score');
 	//print_r($comment_ret);
 	if(is_array($comment_ret)) $date_ret = combine_arr2($date_ret, $comment_ret, 'date_id');
 }
 
 //��ȡ�ǳ�
 if(strlen($nickname_tmp_str) >0)
 {
 	$nickname_sql_str = "user_id IN ({$nickname_tmp_str})";
 	$nickname_ret = $user_obj->get_user_list(false, $nickname_sql_str,'user_id DESC','0,10','user_id AS to_date_id,nickname');
 	if(is_array($nickname_ret)) $date_ret = combine_arr2($date_ret, $nickname_ret, 'to_date_id');
 }
 
 
 //Լ�ĳɹ���
/* $success_sql_str = "SELECT count(det.event_id) as success_count FROM event_db.event_date_tbl dat,event_db.event_details_tbl det WHERE dat.from_date_id= {$user_id} AND dat.event_id = det.event_id AND dat.date_status='confirm' AND det.event_status='2'";
 $detail_success_ret = db_simple_getdata($success_sql_str,true);*/
 if(is_array($detail_success_ret))
 {
 	$setParam['detail_success_count'] = $detail_success_ret['success_count'];
 }
 
 //Լ������
/* $all_sql_str = "SELECT count(date_id) as all_count FROM event_db.event_date_tbl WHERE from_date_id= {$user_id}";
 $detail_all_ret = db_simple_getdata($all_sql_str,true);*/
 /* print_r($detail_all_ret);
 exit; */
 if(is_array($detail_all_ret))
 {
 	$setParam['all_count'] = $detail_all_ret['all_count'];
 	//����ɹ���
 	if(is_array($detail_success_ret))
 	{
 		$setParam['success_pr'] = sprintf('%.4f',($detail_success_ret['success_count']/$detail_all_ret['all_count']))*100;
 		$setParam['detail_fail_count'] = intval($detail_all_ret['all_count']-$detail_success_ret['success_count']);
 	}
 }
 
 
 $tpl->assign($ret); 
 //exit;
 $tpl->assign($account_info);
 $tpl->assign('level',$level);
 //$tpl->assign('nickname',$nickname);
 $tpl->assign($setParam);
 $tpl->assign('date_ret',$date_ret);
 $tpl->assign('user_id', $user_id);
 $tpl->assign('follow_ret',$follow_ret);
 $tpl->output();
?>
 