<?php
/**
 * @desc:   店铺查看控制器 审核控制器
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/7
 * @Time:   14:42
 * version: 2.0
 */
include('common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");
$user_obj  = POCO::singleton('pai_user_class'); //用户表
$model_org_obj = POCO::singleton('pai_model_org_class'); //机构表
$user_icon_obj = POCO::singleton('pai_user_icon_class'); //用户图片
$model_add_obj = POCO::singleton('pai_model_add_class'); //模特表,有可能改为商品表
$model_relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' ); //机构关联表
$mall_obj = POCO::singleton('pai_mall_seller_class'); //店铺表
$tpl = new SmartTemplate("model_org_list_v2.tpl.htm");

$status        = intval($_INPUT['status']);
$act           = trim($_INPUT['act']);
$ids           = $_INPUT['ids'] ? $_INPUT['ids'] : array();
$yue_login_id  = intval($yue_login_id);

//处理上下架
if($act =='up')//上架
{
    if (empty($ids) || !is_array($ids)) js_pop_msg2('传过来数据不能为空!','model_org_list_v2.php?status=2');
    foreach($ids as $key => $vo)
    {
        $mall_obj->institutions_open_store_by_user_id($vo);
    }
    js_pop_msg2('上架成功!','model_org_list_v2.php?status=2');
}
elseif($act == 'down')//下架
{
    if (empty($ids) || !is_array($ids))  js_pop_msg2('传过来数据不能为空!','model_org_list_v2.php?status=1');
    $reason = '机构审核不通过';
    foreach ($ids as $key => $vo)
    {
        $mall_obj->institutions_close_store_by_user_id($vo);
    }
    js_pop_msg2('下架成功!','model_org_list_v2.php?status=1');
}

function js_pop_msg2($msg,$url=NULL)
{
    echo "<script language='javascript'>alert('{$msg}');";
    if($url) echo "location.href = '{$url}';";
    echo "</script>";
    exit;
}


$setParam = array();
$where_str = '';
$user_arr  = array();
$total_count = 0;
if($status >0)
{
    if(strlen($where_str) >0) $where_str .= ' AND ';
    $where_str .= "status={$status}";
    $setParam['status'] = $status;
}
//查询机构下的店铺
$where_org_str   = "org_id={$yue_login_id}";
$list        = $model_relate_org_obj->get_model_org_list_by_org_id(false,$where_org_str, '0,99999999', 'id DESC',$fields = 'user_id,priority');

if(!is_array($list)) $list = array();
$user_tmp_str = '';
foreach($list as $key=>$val)
{
    if($key !=0) $user_tmp_str .= ',';
    $user_tmp_str .= $val['user_id'];
}
//查询店铺数据
if(strlen($user_tmp_str) >0)
{
    if(strlen($where_str) >0) $where_str .=' AND ';
    $where_str .= "user_id IN ({$user_tmp_str})";
    $total_count = $mall_obj->get_seller_store_list(true, $where_str);
    $user_arr = $mall_obj->get_seller_store_list(false, $where_str,'store_id DESC','99999999','DISTINCT(user_id),status');
    $user_arr = combine_arr($user_arr,$list,'user_id');
}

foreach ($user_arr as $key => $vo)
{
    $user_arr[$key]['nickname']  = get_user_nickname_by_user_id($vo['user_id']);
    $user_arr[$key]['cellphone'] = $user_obj->get_phone_by_user_id($vo['user_id']);
    $user_arr[$key]['icon']      = $user_icon_obj->get_user_icon($vo['user_id'], 32);
    $user_arr[$key]['thumb']     = $user_icon_obj->get_user_icon($vo['user_id'], 100);
    $user_arr[$key]['name']      = $model_add_obj->get_user_name_by_user_id($vo['user_id']);
}

$tpl->assign($setParam);
$tpl->assign("list", $user_arr);
$tpl->assign("total_count", $total_count);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();
