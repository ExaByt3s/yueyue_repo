<?php
/**
 * @desc:   新后台管理列表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/21
 * @Time:   18:04
 * version: 1.0
 */
include_once('common.inc.php');
$authority_obj  = POCO::singleton('pai_authority_class');
$is_root = $authority_obj->user_id_is_root();
$tpl = new SmartTemplate( 'manage_list_old.tpl.htm' );
if (is_array($is_root))
{
    $data = $authority_obj->get_authority_list_user(false,"action!='text_examine'", 'id ASC','0,99999999', $fields = 'DISTINCT(action)');

}
else
{
    $data = $authority_obj->get_authority_list_user(false,"user_id={$yue_login_id} AND action!='text_examine'", 'id ASC','0,99999999', $fields = 'DISTINCT(action)');
}

$list = array();
$i = 0;
$arr = array('cameraman_audit','org_add','model_audit','report','inform','cameraman','model','app_template','rank','message','push');
foreach ($data as $key => $val)
{
    if ($val['action'] == 'cameraman_audit')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/cameraman_audit';
        $list[$i]['op_name'] = '摄影师库';
    }
    if ($val['action'] == 'org_add')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/org_add';
        $list[$i]['op_name'] = '机构管理';
    }
    if ($val['action'] == 'model_audit')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/model_audit';
        $list[$i]['op_name'] = '模特库';
    }
    if ($val['action'] == 'report')
     {
         $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/report';
         $list[$i]['op_name'] = '数据报表';
     }
    if ($val['action'] == 'inform')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/inform';
        $list[$i]['op_name'] = '举报管理';
    }
    if ($val['action'] == 'app_template')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/app_template';
        $list[$i]['op_name'] = 'APP模板模块管理';
    }
    if ($val['action'] == 'rank')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/rank';
        $list[$i]['op_name'] = '榜单管理';
    }
    if ($val['action'] == 'message')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/message';
        $list[$i]['op_name'] = '信息推送管理';
    }
    if ($val['action'] == 'push')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/push';
        $list[$i]['op_name'] = '运营推送模特后台';
    }
    if(in_array($val['action'],$arr)) $i++;
}

$list = array_values($list);
if(!is_array($list) || empty($list))
{
    echo "<script type='text/javascript'>window.alert('您暂无权限');location.href='manage_list.php'</script>";
    exit;
}
$tpl->assign('list',$list) ;
$tpl->output();