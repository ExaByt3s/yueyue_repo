<?php
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$str = '【约约红包疯狂送】1月15日—31日 | 约约特为你设置大奖①勤奋女神奖30名，奖金 500元/人。奖励条件：a. 指定时间内最快完成5单自然交易的前30名模特（活动产生的交易不参与计算）；b.每次约拍单价不低于100元/2小时；c.5单交易中需与3个或以上不同摄影师约拍；②人气女神奖10名，奖品：55度神奇温度速调杯。奖励条件：【约约】微信约拍页面粉丝人数最多的前10名。以上奖项均需真实，工作人员会检测真实性。【温馨提示】在约女神微信（yueusmt）中分享自己的模特卡到朋友圈，增加约拍成功率，更易获奖。';

$sql = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role='model' AND pwd_hash != 'poco_model_db'";
$result = db_simple_getdata($sql, FALSE, 101);

foreach($result AS $key=>$val)
{
    echo $val['user_id'] . "<BR>";
    send_message_for_10002($val['user_id'],$str);

}

//send_message_for_10002($user_id,$str);



?>