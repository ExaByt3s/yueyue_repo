<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/authority.php');
$authority_obj  = POCO::singleton('pai_authority_class');
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fmanage%2Findex.php'</script>";
    exit;
}
$is_root = $authority_obj->user_id_is_root();
if (is_array($is_root))
{
    $aut_list = $authority_obj->get_authority_list_user(false,"action!='text_examine'", 'id ASC','0,1', $fields = 'DISTINCT(action)');

}
else
{
    $aut_list = $authority_obj->get_authority_list_user(false,"user_id={$yue_login_id} AND action!='text_examine'", 'id ASC','0,1', $fields = 'DISTINCT(action)');
    if(!is_array($aut_list) || empty($aut_list))
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $aut_list = $admin_op_obj->get_op_full_list(false,$yue_login_id,'','',2);
    }
}
if(!is_array($aut_list) || empty($aut_list))
{
    echo "<script type='text/javascript'>window.alert('ÄúÔÝÎÞÈ¨ÏÞ');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin%2Fmanage%2Findex.php'</script>";
    exit;
}





?>