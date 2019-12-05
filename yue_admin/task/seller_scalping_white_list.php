<?php
/**
* @file seller_scalping_white_list.php
* @synopsis 刷单白名单管理
* @author wuhy@yueus.com
* @version null
* @date 2015-11-18
 */

include_once ('/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php');

define('G_SIMPLE_INPUT_CLEAN_VALUE', 1);
include_once 'common.inc.php';
$pai_risk_obj = POCO::singleton('pai_risk_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$mall_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');

$action = trim($_INPUT['action']);

if ($action=='white_list_info') {
    $tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_white_list_info.tpl.htm");
    $global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
    $tpl->output();
    die();
}
if ($action=='add_white_list') {
    $user_id = intval($_INPUT['user_id']);
    $remark = trim($remark);
    if ($user_id < 1 || strlen($remark) < 1 ) {
        echo "<script>alert('参数错误');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
        die();
    }

	$add_rst =  $pai_risk_obj->add_scalping_white_list_seller($user_id, array('remark' => $remark));
    if ($add_rst<1) { 
        echo "<script>alert('添加失败');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
        die();
    }
    echo "<script>alert('添加成功');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
    die();
}
if ($action=='remove_white_list') {
    $user_id = intval($_INPUT['user_id']);
    if ($user_id < 1) {
        echo "<script>alert('参数错误');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
        die();
    }
	$remove_rst =  $pai_risk_obj->remove_scalping_white_list_seller($user_id);
    if ($remove_rst<1) { 
        echo "<script>alert('删除失败');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
        die();
    }
    echo "<script>alert('删除成功');window.parent.location.href='{$_SERVER['SCRIPT_NAME']}'</script>";
    die();
}
$user_id = intval($_INPUT['user_id']);
$search_var = array();
$where_str = '1 ';
if ($user_id>0) {
    $search_var['user_id'] = $user_id;
    $where_str .= " AND user_id={$user_id}";
}

$page_obj = new show_page();
$page_obj->setvar($search_var);
$count_seller_list = $pai_risk_obj->get_scalping_white_list(true);
$page_obj->set(20, $count_seller_list);		

$seller_list = $pai_risk_obj->get_scalping_white_list(false, $where_str, '', $page_obj->limit());

foreach ($seller_list as $key => &$seller) {
    $seller_info = $mall_seller_obj->get_seller_info($seller['user_id'], 2);

    //商家认证服务列表
    $seller_type_arr = explode(',', $seller_info['seller_data']['profile'][0]['type_id']);
    $type_list = array();
    foreach ($seller_type_arr as $type_id_temp) {
        $type_info = $mall_goods_type_obj->get_type_info($type_id_temp);
        $type_list[] = array(
            'id' => $type_id,
            'name' => $type_info['name'],
        );
    }
    unset($type_id_temp);

    $seller['seller_name'] = $seller_info['seller_data']['name'];
    $seller['type_list'] = $type_list;//商家开通的服务列表
    $seller['add_date'] = date('Y-m-d H:i:s', $seller['add_time']);
}
unset($seller);

$tpl = new SmartTemplate(TASK_TEMPLATES_ROOT."seller_scalping_white_list.tpl.htm");
$global_header_html = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign('search', $search_var);
$tpl->assign('seller_list', $seller_list);
$tpl->assign('page', $page_obj->output(true));
$tpl->output();
?>
