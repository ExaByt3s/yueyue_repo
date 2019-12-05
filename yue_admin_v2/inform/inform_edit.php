<?php
/**
 * @desc:   �ٱ�����������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   17:19
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'inform_edit');//Ȩ�޿���
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //�ٱ���������
$user_obj = POCO::singleton('pai_user_class'); //�û���
$user_level_obj = POCO::singleton('pai_user_level_class');//��Ӱʦ�ȼ���
$score_rank_obj = POCO::singleton('pai_score_rank_class'); //ģ�صȼ���

$tpl = new SmartTemplate( TEMPLATES_ROOT.'inform_edit.tpl.htm' );

//���ܲ���
$act = trim($_INPUT['act']);
$id = (int)$_INPUT['id'];
if($id <1) js_pop_msg_v2("�Ƿ�����",true);

if ($act == 'shield')//�Ѿٱ��߼��������
{
    $reason = trim($_INPUT['reason']);
    $ret = $pai_log_inform_v2_obj->update_inform_by_id($id,$reason);
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2("�����ɹ�",false,"inform_list.php");
    js_pop_msg_v2("����ʧ��",false,"inform_list.php");
    exit;
}

//�ٱ���������
$ret = $pai_log_inform_v2_obj->get_inform_info($id);
if (is_array($ret))
{

    //���ٱ��߻�����Ϣ
    $ret['status'] = $pai_log_inform_v2_obj->get_info_by_to_informer_id($ret['to_informer']);//���ٱ���״̬
    $to_informer_data['to_informer_name'] = get_user_nickname_by_user_id($ret['to_informer']);
    $by_informer_data['to_cellphone'] = $user_obj->get_phone_by_user_id($ret['to_informer']);
    $to_informer_data['to_be_count_v2'] = $pai_log_inform_v2_obj->get_inform_list(true,0,$ret['to_informer']); //���ٱ�����
    $to_informer_data['by_to_count_v2'] = $pai_log_inform_v2_obj->get_inform_list(true,$ret['to_informer']); //�ٱ����˴���


    //�ٱ��˻�����Ϣ
    $by_informer_data['by_informer_name'] = get_user_nickname_by_user_id($ret['by_informer']); //�ٱ�����Ϣ���
    $by_informer_data['by_cellphone'] = $user_obj->get_phone_by_user_id($ret['by_informer']);
    $by_informer_data['to_be_count'] = $pai_log_inform_v2_obj->get_inform_list(true,0,$ret['by_informer']); //���ٱ�����
    $by_informer_data['by_to_count'] = $pai_log_inform_v2_obj->get_inform_list(true,$ret['by_informer']); //�ٱ����˴���
}
$tpl->assign($ret);
$tpl->assign($by_informer_data);
$tpl->assign($to_informer_data);
$tpl->output ();