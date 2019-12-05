<?php
/**
 * @desc:   �̼ҹ������������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/19
 * @Time:   15:28
 * version: 1.0
 */
include_once 'common.inc.php';

$organization_obj  = POCO::singleton('pai_organization_class');//������
$seller_relate_obj  = POCO::singleton('pai_model_relate_org_class'); //֮ǰ��ֻ����ģ�صĻ��������࣬�������̼ҵ���
$org_log_obj  = POCO::singleton('pai_org_log_class'); //ģ�ػ���log

$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."seller_relate_org.tpl.htm" );

$act = trim($_INPUT['act']);
$user_id = intval($user_id);

if($yue_login_id <1) js_pop_msg('�Ƿ�����');
if($user_id <1)  js_pop_msg('�Ƿ�����');

$where_str = '';
$setParam = array();

$selected_seller_ret = $seller_relate_obj->get_org_list_by_user_id(false, $user_id,$where_str,'0,99999999');//��ȡ��ѡ�е��̼�
if(!is_array($selected_seller_ret)) $selected_seller_ret = array();

if($act == 'insert')//����
{
    //����log����
    $data_log = array();
    $data_log['model_user_id'] = $user_id;
    $data_log['audit_user_id'] = $yue_login_id;
    $data_log['add_time'] = time();

    foreach ($selected_seller_ret as $val)//����log����
    {
        if($val['priority'] == 1) $data_log['before_priority_org_id'] = $val['org_id'];
        if($key != 0) $data_log['before_org_id'] .= ',';
        $data_log['before_org_id'] .= $val['org_id'];
    }

    $org_id= $_INPUT['org_id'];
    $priority= intval($_INPUT['priority']);

    $seller_relate_obj->delete_org_by_user_id($user_id);//ɾ��֮ǰ����
    if(!is_array($org_id)) $org_id = array();

    if (!empty($org_id) && is_array($org_id)) { //��ʼ���������
        $count = count($org_id);
        if ($count == 1) {//����Ϊ1
            $data['priority'] = 1;
            $data['org_id']   = $org_id[0];
            $data['user_id']  = $user_id;
            $seller_relate_obj->add_model_org($data);
        }else {//����������Ϊ1
            foreach ($org_id as $key => $vo)
            {
                $org_user_id = intval($vo);
                if ($priority == 0) {
                    if ($key == 0) {
                        $data['priority'] = 1;
                    }
                    else {
                        $data['priority'] = 0;
                    }
                }
                elseif($priority) {
                    if ($vo == $priority) {
                        $data['priority'] = 1;
                    }
                    else {
                        $data['priority'] = 0;
                    }
                }
                $data['org_id']   = $org_user_id;
                $data['user_id']  = $user_id;
                $seller_relate_obj->add_model_org($data);
            }
        }
    }
    //�޸ĺ�ģ�ػ���
    $after_org_ret = $seller_relate_obj->get_org_list_by_user_id(false, $user_id,$where_str,'0,99999999');
    if(!is_array($after_org_ret)) $after_org_ret = array();
    foreach ($after_org_ret as $key=>$val) {
        if($val['priority'] == 1) $data_log['after_priority_org_id'] = $val['org_id'];
        if($key != 0) $data_log['after_org_id'] .= ',';
        $data_log['after_org_id'] .= $val['org_id'];
    }
    $org_log_obj->add_info($data_log);//��ӻ���log

    $ret = $seller_relate_obj->get_org_info_by_user_id($user_id);//��ҳ����ʾ������
    $org_sign_id = intval($ret['org_id']);
    if($org_sign_id >1)
    {
        $org_name = $organization_obj->get_org_name_by_user_id($org_sign_id);
        $ret['org_name'] = iconv( "GB2312", "UTF-8", $org_name);
    }
    $arr  = array( 'msg' => 'success' , 'ret' => $ret);
    echo json_encode($arr);
    exit;
}

foreach ($selected_seller_ret as &$val) {
    $val['org_name'] = $organization_obj->get_org_name_by_user_id($val['org_id']);//������
}
$list = $organization_obj->get_org_list(false,'status=1','id DESC','0,99999999','user_id,nick_name');
$tpl->assign('user_id', $user_id);
$tpl->assign('list', $list);
$tpl->assign('selected_org', $selected_seller_ret);
$tpl->output();



