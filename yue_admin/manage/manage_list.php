<?php

/**
 * 管理员管理
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月30日
 * @version 1
 */

include_once 'common.inc.php';
$admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
$authority_obj  = POCO::singleton('pai_authority_class');
$is_root = $authority_obj->user_id_is_root();
$tpl = new SmartTemplate("manage_list.tpl.htm");

if (is_array($is_root))
{
    $data = $authority_obj->get_authority_list_user(false,"action!='text_examine'", 'id ASC','0,99999999', $fields = 'DISTINCT(action)');

}
else
{
    $data = $authority_obj->get_authority_list_user(false,"user_id={$yue_login_id} AND action!='text_examine'", 'id ASC','0,99999999', $fields = 'DISTINCT(action)');
}
$list_v2 = $admin_op_obj->get_op_full_list(false,$yue_login_id,'','',2);

$list = array();
$i = 0;
foreach ($data as $key => $val)
{
    /*if ($val['action'] == 'cameraman_audit')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/cameraman_audit';
        $list[$i]['name'] = '摄影师库';
    }*/
    if ($val['action'] == 'task')
    {
        $list[$i]['op_url']  = 'http://yp.yueus.com/yue_admin/task';
        $list[$i]['op_name'] = '认证/服务管理';
    }
    /*if ($val['action'] == 'org_add')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/org_add';
        $list[$i]['name'] = '机构管理';
    }*/
    if ($val['action'] == 'coupon')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/coupon';
        $list[$i]['op_name'] = '优惠券管理';
    }
    /*if ($val['action'] == 'model_audit')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/model_audit';
        $list[$i]['name'] = '模特库';
    }*/
   /* if ($val['action'] == 'report')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/report';
        $list[$i]['name'] = '数据报表';
    }*/
    /*if ($val['action'] == 'inform')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/inform';
        $list[$i]['name'] = '举报管理';
    }*/
    if ($val['action'] == 'topic')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/topic';
        $list[$i]['op_name'] = '专题管理';
    }
    //旧的
    /*if ($val['action'] == 'cameraman')
    {
        //unset($val);
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/audit/index.php';
        $list[$i]['name'] = '旧的摄影师后台';
    }*/
    /*if ($val['action'] == 'text_examine')
    {
        $list[$key]['url'] = 'http://yp.yueus.com/yue_admin/audit/index.php';
        $list[$key]['name'] = '文字审核后台';
    }*/
    /*if($val['action'] == 'pic_examine')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/audit/index.php';
        $list[$i]['op_name'] = '内容审核管理';
    }*/
    /*if ($val['action'] == 'model')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/audit/index.php';
        $list[$i]['name'] = '旧的模特后台';
    }*/
    //旧的
    if ($val['action'] == 'operate')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/operate';
        $list[$i]['op_name'] = '身份证审核管理';
    }
    /*if ($val['action'] == 'app_template')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/app_template';
        $list[$i]['name'] = 'APP模板模块管理';
    }*/
    /*if ($val['action'] == 'rank')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/rank';
        $list[$i]['name'] = '榜单管理';
    }*/
    /*if ($val['action'] == 'message')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/message';
        $list[$i]['name'] = '信息推送管理';
    }*/
    /*if ($val['action'] == 'push')
    {
        $list[$i]['url'] = 'http://yp.yueus.com/yue_admin/push';
        $list[$i]['name'] = '运营推送模特后台';
    }*/
    if ($val['action'] == 'template')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/template';
        $list[$i]['op_name'] = '消息推送模板后台';
    }
    if ($val['action'] == 'authority')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/authority';
        $list[$i]['op_name'] = '权限管理后台(V1)';
    }
    if ($val['action'] == 'cms')
    {
        $list[$i]['op_url'] = 'http://yp.yueus.com/yue_admin/cms';
        $list[$i]['op_name'] = '排行榜';
    }
    $i++;
}

if(is_array($list_v2))
{
    $list = array_merge($list_v2,$list);
}


$tpl->assign('list',$list);
$tpl->output();

