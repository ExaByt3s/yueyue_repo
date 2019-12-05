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
 
 
 
 include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 include_once('./config/phone_meeting_config.php');//配置峰会对应场次的ID跟价钱
 $__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
 if($__is_weixin)
 {
     header("location:http://yp.yueus.com/m/photo_meeting_wap_weixin_enroll.php");
 }
 
 
$tpl = $my_app_pai->getView('photo_meeting_wap_enroll.tpl.htm');
 
 
$meeting_price_1 = $meeting_price_array["1"];
$meeting_price_2 = $meeting_price_array["2"];
$meeting_price_3 = $meeting_price_array["3"];


$tpl->assign("is_weixin",$__is_weixin);
$tpl->assign("meeting_price_1",$meeting_price_1);
$tpl->assign("meeting_price_2",$meeting_price_2);
$tpl->assign("meeting_price_3",$meeting_price_3);
$tpl->assign("meeting_name_price_array",$meeting_name_price_array);
 
 $tpl->output();
 ?>