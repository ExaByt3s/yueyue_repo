<?php
/**
 * @desc:   �������б�
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/29
 * @Time:   11:42
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'shield_list');//Ȩ�޿���
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //�ٱ���������

$page_obj = new show_page ();
$show_count = 20;

$tpl = new SmartTemplate( TEMPLATES_ROOT."shield_list.tpl.htm" );

$act = trim($_INPUT['act']);
$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);
$user_id = (int)$_INPUT['user_id']; //������ID
$audit_id = (int)$_INPUT['audit_id'];//�����ID

//�������Ƴ�����
if($act == 'remove')
{
    $id = (int)$id;
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    $ret = $pai_log_inform_v2_obj->mouve_out_blacklist_by_id($id);
    if(!is_array($ret)) $ret = array();
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2('�Ƴ������������ɹ�',false,'shield_list.php');
    js_pop_msg_v2('�Ƴ�����������ʧ��',false,'shield_list.php');
    exit;
}


//�б�չʾ����
$where_str = '';
$setParam = array();

//��������Ͳ�ѯ����ƴ��
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}
if($user_id >0) $setParam['user_id'] = $user_id;
if($audit_id >0) $setParam['audit_id'] = $audit_id;

$page_obj->setvar($setParam);
$total_count = $pai_log_inform_v2_obj->get_sheld_list(true,$user_id,$audit_id,$where_str);
$page_obj->set($show_count,$total_count);
$list = $pai_log_inform_v2_obj->get_sheld_list(false,$user_id,$audit_id,$where_str,"add_time DESC,id DESC",$page_obj->limit());
if(!is_array($list)) $list = array();

foreach($list as &$val)
{
    $val['nickname'] = get_user_nickname_by_user_id($val['user_id']);
    $val['auditname'] = get_user_nickname_by_user_id($val['audit_id']);
}

$tpl->assign('total_count',$total_count);
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();

