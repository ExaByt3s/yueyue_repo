<?php
/**
 * @desc:   �����޸ĺ����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/10
 * @Time:   12:16
 * version: 2.0
 */
include("common.inc.php");
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once ('/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php');
$organization_obj = POCO::singleton('pai_organization_class');//������
$user_obj         = POCO::singleton('pai_user_class');
$tpl = new SmartTemplate("org_edit.tpl.htm");


$act     = trim($_INPUT['act']);
$user_id = intval($_INPUT['user_id']);

if ($user_id <1) js_pop_msg_v2('�Ƿ�����',true);
//��������
if ($act == 'update')
{
    $nick_name   = trim($_INPUT['nick_name']);
    $pwd_hash    = trim($_INPUT['pwd_hash']);
    $link_man    = trim($_INPUT['link_man']);
    $address     = trim($_INPUT['address']);
    $org_desc    = trim($_INPUT['org_desc']);
    $province    = intval($_INPUT['province']);
    $location_id = intval($_INPUT['location_id']);
    if (strlen($nick_name)<1) js_pop_msg_v2('����������Ϊ��',true);
    if ($pwd_hash)
    {
        if (strlen($pwd_hash) < 6)
        {
            js_pop_msg_v2('���볤�Ȳ���С��6λ',true);
        }
        else
        {
            $info = $user_obj->update_pwd_by_user_id($user_id, $pwd_hash);
        }
    }
    if($location_id <1) js_pop_msg_v2('��������Ϊ��',true);
    $user_info_arr['location_id'] = $location_id;
    $info_id = $user_obj->update_user($user_info_arr, $user_id);

    $data['nick_name'] = $nick_name;
    $data['link_man']  = $link_man;
    $data['address']   = $address;
    $data['org_desc']  = $org_desc;
    $ret = $organization_obj->update_org($data, $user_id);
    if ($ret || $info || $info_id) js_pop_msg_v2('���»����ɹ�',false,'org_list_v2.php');
    js_pop_msg_v2('��������ʧ��',true);
}
elseif ($act == 'delete')
{
    $ret = $organization_obj->del_org($user_id);
    if ($ret) js_pop_msg_v2('ɾ�������ɹ�',false,'org_list_v2.php');
    js_pop_msg_v2('ɾ������ʧ��',true);
}

$ret = $organization_obj->get_org_info_by_user_id($user_id);
$user_info = $user_obj->get_user_info($user_id);
$user_info['province'] = substr($user_info['location_id'],0,6);
//ʡ�ͽӿ�
$location_id_info = get_poco_location_name_by_location_id ($user_info['location_id'], true, true);
//ʡ

function js_pop_msg_v2($msg,$b_reload = false,$url=NULL)
{
    echo "<script language='javascript'>alert('{$msg}');";
    if($url) echo "location.href = '{$url}';";
    if($b_reload) echo "history.back();";
    echo "</script>";
    exit;
}

$tpl->assign($user_info);
$tpl->assign($ret);
$tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();