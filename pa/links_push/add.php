<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   11:09
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');
include_once(YUE_PA_CLASS_ROOT.'pai_url_qrcode_class.inc.php');
$pai_url_qrcode_obj = new pai_url_qrcode_class();

$tpl = new SmartTemplate( TEMPLATES_ROOT.'add.tpl.htm' );

$act = trim($_INPUT['act']);
$url = trim($_INPUT['url']);
$setParam = array('button_name'=>'申请');

if($act == 'create')
{
    $qrcode_num = intval($_INPUT['qrcode_num']);
    if($qrcode_num <1) $qrcode_num = 1;//默认生成一个
    if(strlen($url) <1) js_pop_msg_v2('约约链接地址不能为空',true);
    if(!filter_var($url, FILTER_VALIDATE_URL)) js_pop_msg_v2('约约链接地址非法',true);
    $remark = trim($_INPUT['remark']);
    $pattern = '/yueus.com/is'; //防止被攻击
    preg_match($pattern,$url,$match);
    if(!$match) js_pop_msg_v2('链接不包含yueus.com非法地址',true);
    if($qrcode_num >100) js_pop_msg_v2('生成个数超过100个');
    /*$count = $pai_url_qrcode_obj->get_url_qrcode_list(true,$yue_login_id,$url);
    if($count >=1) js_pop_msg_v2('您已经生成过该二维码了，无需再次生成',true);*/
    $list = array();
    $setParam['url'] = $url;
    $setParam['remark'] = $remark;
    $setParam['qrcode_num'] = $qrcode_num;
    for($i=0;$i <$qrcode_num; $i++)
    {
        $ret = $pai_url_qrcode_obj->create_qrcode_img($url,$remark);
        $list[] = $ret;
        unset($ret);
    }

    //if(!$ret) js_pop_msg_v2('生成二维码失败');
    $setParam['button_name'] = '继续申请';
    $tpl->assign('list',$list);
}
$tpl->assign($setParam);
$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();