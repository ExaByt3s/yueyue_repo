<?php
/** 
 * 
 * 摄影峰会专题页
 * 
 * 2015-4-1
 * 
 * author 星星
 * 
 */

//include_once("/disk/data/htdocs232/photo/photo_common.inc.php");
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('./config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱


/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

//跟距设备使用不同模板跟显示
if($__is_weixin || $__is_android || $__is_iphone)//if($_INPUT['v']==1)
{
    $tpl = $my_app_pai->getView('photo_meeting_wap.tpl.htm');
    if($__is_weixin)
    {
        $device = "weixin";
        //配置分享的相关内容
        //获取微信JSSDK签名数据
        $app_id = 'wx25fbf6e62a52d11e';	//约约正式号
        $weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
        
        $location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $location_url);
        $share_txt = '互联网+摄影年度盛事——想与大师和投资人面对面交流吗？把握机遇，快来参加！';
        $share_img = 'http://www.yueus.com/topic/meeting/images/share_img.jpg';
        $share_des = '5月17日摄影大师、行业精英、投资人齐聚“互联网+摄影”年度盛事，快来参加！';
        
        

        $tpl->assign('wx_sign_package',json_encode($wx_sign_package));
        $tpl->assign('share_txt',$share_txt);
        $tpl->assign('share_img',$share_img);
        $tpl->assign('share_des',$share_des);
    }
    else
    {
        $device = "wap";
    }
}
else
{
    $device = "pc";
    $tpl = $my_app_pai->getView('photo_meeting_pc.tpl.htm');
}


//构造场次数组


foreach($meeting_array as $key => $value)
{
    $meeting_name_price_array[] = array("name"=>$meeting_name_array[$value],"price"=>$meeting_price_array[$value],"meeting_id"=>$value);
}



//var_dump($meeting_name_price_array);

//$header_html = $my_app_pai->webControl('PartyHeader', array(), true);




$meeting_price_1 = $meeting_price_array["1"];
$meeting_price_2 = $meeting_price_array["2"];
$meeting_price_3 = $meeting_price_array["3"];


/* var_dump($meeting_price_1);
var_dump($meeting_price_2);
var_dump($meeting_price_3);
exit; */

$tpl->assign("is_weixin",$__is_weixin);
$tpl->assign("meeting_price_1",$meeting_price_1);
$tpl->assign("meeting_price_2",$meeting_price_2);
$tpl->assign("meeting_price_3",$meeting_price_3);


$tpl->assign("meeting_name_price_array",$meeting_name_price_array);
$tpl->assign("rand",time());
$tpl->assign("device",$device);

//$tpl->assign('header_html', $header_html);

$tpl->output();
?>