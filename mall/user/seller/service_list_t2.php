<?php

include_once 'config.php';

// ========================= 初始化接口 start =======================
$type_id = (int)$_INPUT['type_id'];
$main_title = mb_convert_encoding(urldecode($_INPUT['title']), 'gbk', 'utf8');
if (empty($main_title )) 
{
    $main_title = '服务列表';
}

// ========================= 初始化接口 end =======================

// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{

    //****************** pc版 ******************
    include_once './service_list_t2-pc.php';
}
else
{
    //****************** wap版 ******************
    include_once './service_list_t2-wap.php';
}



// ========================= 最终模板输出  =======================
$tpl->output();

?>