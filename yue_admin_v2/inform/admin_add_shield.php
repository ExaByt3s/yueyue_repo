<?php
/**
 * @desc:   ����Ա�ֶ������û�
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/29
 * @Time:   14:23
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'inform_list');//Ȩ�޿���
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //�ٱ���������

$tpl = new SmartTemplate( TEMPLATES_ROOT."admin_add_shield.tpl.htm" );

$act = trim($_INPUT['act']);
$user_id = (int)$_INPUT['user_id'];
$data_str = trim($_INPUT['data_str']);

//�����û�
if($act == 'shield')
{
    if($user_id < 1) js_pop_msg_v2('�û�ID����Ϊ��');
    if($user_id < 100000) js_pop_msg_v2('���޷�����ϵͳ�û�');
    if(strlen($data_str)<1) js_pop_msg_v2('����д����ԭ��');
    $ret = $pai_log_inform_v2_obj->admin_add_user_id_into_blacklist($user_id,$data_str);
    print_r($ret);
    if(!is_array($ret)) $ret = array();
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2('�ֶ����ڳɹ�',false,"?act=007",true);
    js_pop_msg_v2('�ֶ�����ʧ��,���ܸ��û��Ѿ������ڹ���');
    exit;
}



$tpl->output();