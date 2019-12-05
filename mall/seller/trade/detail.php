<?php

include_once 'config.php';

// ========================= 初始化接口 start =======================

// 权限检查
$check_arr = mall_check_user_permissions($yue_login_id);

// 账号切换时
if($check_arr['switch'] == 1)
{
$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
header("Location:{$url}");
die();
}


$topic_id =  intval($_INPUT['topic_id']);
$state = $_INPUT['state'];

if (empty($topic_id)) 
{
    header("location: ".'./list.php');
}


$trade_detail_obj = POCO::singleton ( 'pai_topic_class' );
$ret = $trade_detail_obj->get_task_detail($topic_id,$yue_login_id);






// ========================= 初始化接口 end =======================



// ========================= 区分pc，wap模板与数据格式整理 start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    include_once './detail-pc.php';
   
}
else
{
    
    //****************** wap版 ******************
    include_once './detail-wap.php';
    
} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================




// ========================= 最终模板输出  =======================
$tpl->output();

?>