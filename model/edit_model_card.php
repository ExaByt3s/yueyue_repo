<?php
/** 
 * 
 * 编辑模特卡
 * 
 * author nolest
 * 
 * 
 * 2015-1-20
 * 
 * 
 */
require_once('../poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/config/model_card_config.php');
$referer_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$referer_url = urlencode($referer_url);

// 判断角色，如果是摄影师就跳转
$pai_user_obj = POCO::singleton('pai_user_class');

$role = $pai_user_obj->check_role($yue_login_id);


// 用于处理机构问题
$model_id = intval($_INPUT['model_id'])?intval($_INPUT['model_id']) : $yue_login_id;

$pai_model_obj = POCO::singleton('pai_model_relate_org_class');

$pai_obj = POCO::singleton('pai_model_card_class');

$ret = $pai_obj->get_model_card_by_user_id($model_id,15);

if($role == 'cameraman')
{
	echo "<script>alert('请用模特账号登录');top.location.href='http://www.yueus.com/'</script>";

	die();
}

// 没有登录要跳转到登录页
if(empty($yue_login_id))
{	
	echo "<script>top.location.href='http://www.yueus.com/model/login.php?referer_url=".$referer_url."'</script>";

	die();
}

// 判断机构是否拥有编辑模特权限
$can_edit = $pai_model_obj-> get_org_model_audit_by_user_id($model_id,$yue_login_id);

if(!$can_edit)
{
	
	echo "<script>top.location.href='http://www.yueus.com/model/login.php?referer_url=".$referer_url."'</script>";

	die();
}

//来自yueyue_m目录 location_data_v2.php 
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');
$arr = array('province'=>$area_config['province'],'city'=>$area_config['city']);



//$output_arr['two_lv_data'] = $arr;
$model_style_arr = array();
$data_arr = array(''=>
    array());





$sex_obj = POCO::singleton('pai_user_class');
$ret['sex'] = $sex_obj->get_user_sex($yue_login_id);
if(empty($ret['sex']))
{	
	$ret['sex'] = '女';
}
if(empty($ret['limit_num']))
{
    $ret['limit_num'] = '1';
}


foreach($ret['model_style_combo']['other'] as $key => $value)
{
    $ret['model_style_combo']['other'][$key]['idx'] = $key+1;
}

$tpl = $my_app_pai->getView('edit_model_card.htm');
if($yue_login_id == '100029')
{
    //$tpl = $my_app_pai->getView('management_v2.htm');
}
else
{

}
//$redirect_url='event_list.php';
//读取模板






$header_html = $my_app_pai->webControl('pc_model_card_header', array(), true);
$tpl->assign('header_html', $header_html);

// 公共样式和js引入
$pc_global_top = $my_app_pai->webControl('pc_global_top', array(), true);
$tpl->assign('pc_global_top', $pc_global_top);

foreach ($model_style as $key => $val) {
    $tmp_arr = array(
        'text' => (string)$val,
        'id' => rand(0,10000)
    );
    $model_style_arr[]=$tmp_arr;
}

$icon_hash = md5($yue_login_id.'YUE_PAI_POCO!@#456');

$tpl ->assign("test_demo","demo");
$tpl ->assign("rand",$random_num = date("YmdHis"));
$tpl ->assign("data",$ret);
$tpl ->assign("user_icon",get_user_icon($model_id,165));
$tpl ->assign("user_id",$model_id);
$tpl ->assign("model_style_data",json_encode(poco_iconv_arr($model_style_arr, 'GBK','UTF-8')));
$tpl ->assign("province",$arr['province']);
$tpl ->assign("city",$arr['city']);
$tpl ->assign("icon_hash",$icon_hash);
$tpl ->assign("model_pic",json_encode(poco_iconv_arr($ret['model_pic'], 'GBK','UTF-8')));
//dump($model_style_arr);

//dump($ret);


//二维码生成
$erweima = 'http://www.yueus.com/share_card/' . $yue_login_id;
$tpl ->assign("erweima",$erweima);  


$tpl->output();
if($yue_login_id == '100029')
{
    dump($ret);
}
?>