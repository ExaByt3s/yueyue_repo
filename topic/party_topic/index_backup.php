<?php
include_once('/disk/data/htdocs232/poco/pai/topic/party/party_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//var_dump($yue_login_id);

//echo $_REQUEST['share_phone'];

$share_txt = '广东史上最大型的外拍盛事，摄影人必须分享';
$share_img = 'http://www.yueus.com/topic/party_topic/images/share_img.jpg';
$share_des = '500位美女让你1元随心拍，成功约拍，还有百万礼包等你来拿，绝对任性！';
$page_title = '约约-千人大外拍挑战吉尼斯 百万优惠送送送';
$pai_user_obj = POCO::singleton('pai_user_class');


/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  


$__is_mobile = ($__is_android || $__is_iphone) ? true : false;

if($__is_weixin && !$yue_login_id && !$_COOKIE['weixin_jump'])
{
    $jump_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $url = $weixin_pub_obj->auth_get_authorize_url(array('url' => $jump_url), 'snsapi_base');
    setcookie("weixin_jump",1);
    header("Location: {$url}");
    exit;
}

$cms_obj = new cms_system_class();
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');

//1元随心拍
$one_pai = $cms_obj->get_record_list_by_issue_id(false, 279, "0,4","place_number ASC", null, "");


//大外拍（室内创造）
$waipai1 = $cms_obj->get_record_list_by_issue_id(false, 280, "0,50","place_number ASC", null, "");


//大外拍（小清新）
$waipai2 = $cms_obj->get_record_list_by_issue_id(false, 281, "0,10","place_number ASC", null, "");

foreach($one_pai as $k=>$val)
{
    $content_arr = explode("|",$val['content']);
    $remark_arr = explode("|",$val['remark']);
    
    $event_id = $content_arr[0];
    
    $one_pai[$k]['event_id'] = $event_id;
    $one_pai[$k]['old_price'] = $content_arr[1];
    
    $one_pai[$k]['time'] = $remark_arr[0];
    $one_pai[$k]['address'] = $remark_arr[1];
    $one_pai[$k]['org'] = $remark_arr[2];
    $one_pai[$k]['num'] = $remark_arr[3];
    
    //处理时间


    $distance_timestamp =strtotime($one_pai[$k]['time'])-time();
    $distance = ($distance_timestamp<=0) ? 0 : date('z',$distance_timestamp); 

    $one_pai[$k]['time_distance'] = $distance."天";
    
    
    
    if($__is_weixin)
    {
        $url = $weixin_pub_obj->auth_get_authorize_url(array('mode' => 'wx','route' => 'act/detail/'.$event_id), 'snsapi_base');
        $one_pai[$k]['wap_url'] = $url;
    }
    else
    {
        $one_pai[$k]['wap_url'] = "http://app.yueus.com";
    }
}

//print_r($one_pai);

foreach($waipai1 as $k=>$val)
{
    $content_arr = explode("|",$val['content']);
    $remark_arr = explode("|",$val['remark']);
    
    $event_id = $content_arr[0];
    
    $waipai1[$k]['event_id'] = $event_id;
    $waipai1[$k]['old_price'] = $content_arr[1];
    $waipai1[$k]['new_price'] = $content_arr[2];
    
    $waipai1[$k]['time'] = $remark_arr[0];
    $waipai1[$k]['address'] = $remark_arr[1];
    $waipai1[$k]['intro'] = $remark_arr[2];
    
    $distance_timestamp =strtotime($waipai1[$k]['time'])-time();
    $distance = ($distance_timestamp<=0) ? 0 : date('z',$distance_timestamp); 

    
    
    $waipai1[$k]['time_distance'] = $distance."天";
    
    
    if($__is_weixin)
    {
        $url = $weixin_pub_obj->auth_get_authorize_url(array('mode' => 'wx','route' => 'act/detail/'.$event_id), 'snsapi_base');
        $waipai1[$k]['wap_url'] = $url;
    }
    else
    {
        $waipai1[$k]['wap_url'] = "http://app.yueus.com";
    }
    
}

foreach($waipai2 as $k=>$val)
{
    $content_arr = explode("|",$val['content']);
    $remark_arr = explode("|",$val['remark']);
    
    $event_id = $content_arr[0];
    
    $waipai2[$k]['event_id'] = $event_id;
    $waipai2[$k]['old_price'] = $content_arr[1];
    $waipai2[$k]['new_price'] = $content_arr[2];
    
    $waipai2[$k]['time'] = $remark_arr[0];
    $waipai2[$k]['address'] = $remark_arr[1];
    $waipai2[$k]['intro'] = $remark_arr[2];
    
    //处理时间
    $distance_timestamp =strtotime($waipai2[$k]['time'])-time();
    $distance = ($distance_timestamp<=0) ? 0 : date('z',$distance_timestamp); 
    $waipai2[$k]['time_distance'] = $distance."天";
    
    
    
    if($__is_weixin)
    {
        $url = $weixin_pub_obj->auth_get_authorize_url(array('mode' => 'wx','route' => 'act/detail/'.$event_id), 'snsapi_base');
        $waipai2[$k]['wap_url'] = $url;
    }
    else
    {
        $waipai2[$k]['wap_url'] = "http://app.yueus.com";
    }
}



// pc 访问
if($_INPUT['v2']==1)
{
    $tpl = $my_app_pai->getView('wap_v2.tpl.htm');

    if($__is_weixin)
    {
        //获取微信JSSDK签名数据
        $app_id = 'wx25fbf6e62a52d11e';	//约约正式号
        $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
        
        $location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $location_url);
        

        $tpl->assign('wx_sign_package',json_encode($wx_sign_package));
        $tpl->assign('share_txt',$share_txt);
        $tpl->assign('share_img',$share_img);
        $tpl->assign('share_des',$share_des);
    }	
}
elseif(!$__is_mobile)
{
    $tpl = $my_app_pai->getView('index.tpl.htm');
}
else
{

    $tpl = $my_app_pai->getView('wap.tpl.htm');
    
    if($__is_weixin)
    {
        //获取微信JSSDK签名数据
        $app_id = 'wx25fbf6e62a52d11e';	//约约正式号
        $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
        
        $location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $location_url);
        

        $tpl->assign('wx_sign_package',json_encode($wx_sign_package));
        $tpl->assign('share_txt',$share_txt);
        $tpl->assign('share_img',$share_img);
        $tpl->assign('share_des',$share_des);
    }	

    
}



//获取相关信息
$enroll_user_phone = $pai_user_obj->get_phone_by_user_id($yue_login_id);

$wap_user_phone = $enroll_user_phone;
if(!$wap_user_phone)
{
    $wap_user_phone = 0;
}
else
{
    $wap_user_phone = base_convert($wap_user_phone,10,8);
}

$tpl->assign("onepai",$one_pai);
$tpl->assign("waipai1",$waipai1);
$tpl->assign("waipai2",$waipai2);
$tpl->assign("is_weixin",$__is_weixin);
$tpl->assign("yue_login_id",$yue_login_id);
$tpl->assign("enroll_user_phone",$enroll_user_phone);
$tpl->assign("wap_user_phone",$wap_user_phone);
$tpl->assign("footer_iphone_link",G_IPHONE_DOWN_LINK);
$tpl->assign("footer_android_link",G_ANDROID_DOWN_LINK);
$tpl->assign("page_title",$page_title);

$tpl->assign("t",201503091415);

$tpl->output();
?>