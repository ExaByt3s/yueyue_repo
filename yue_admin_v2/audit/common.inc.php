<?php
/**
 * @desc:   �°��������Ȩ�޿�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/8
 * @Time:   12:28
 * version: 2.0
 */
define('AUDIT_TEMPLATES_ROOT',"templates/");//ģ��λ��
include_once('../common.inc.php');
include_once('pai_pic_examine_v2_class.inc.php');//picͼƬ��
include_once('pai_text_examine_v2_class.inc.php');//text ������
if (empty($yue_login_id) || !isset($yue_login_id))
{
    echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Faudit%2Findex.php';</script>";
    exit;
}
$admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
$op_p = $admin_op_obj->check_op($yue_login_id,'audit');
if(!is_array($op_p) || empty($op_p))
{
    echo "<script type='text/javascript'>window.alert('��û��Ȩ��,����ϵ����Ա��ȡȨ��!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Faudit%2Findex.php';</script>";
    exit;
}

/**
 * �Ѻ���ʾ��Ϣ
 * @param $msg ��Ϣ
 * @param bool $b_reload
 * @param null $url
 */
function js_pop_msg_v2($msg,$b_reload = false,$url=NULL)
{
    echo "<script language='javascript'>alert('{$msg}');";
    if($url && $b_reload) echo "parent.location.href = '{$url}';";
    elseif($url)  echo "location.href = '{$url}';";
    if($b_reload) echo "history.back();";
    echo "</script>";
    exit;
}

$type_img_arr = array('works','merchandise','head'); //ͼƬ����

