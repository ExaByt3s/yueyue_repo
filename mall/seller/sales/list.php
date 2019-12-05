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


// ========================= 初始化接口 end =======================



// ========================= 区分pc，wap模板与数据格式整理 start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    include_once './list-pc.php';
   
}
else
{
    
    //****************** wap版 ******************
    include_once './list-wap.php';
    
} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================




// ========================= 最终模板输出  =======================
$tpl->output();


?>