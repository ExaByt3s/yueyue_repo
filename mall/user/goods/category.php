<?php
include_once 'config.php';


// ========================= 初始化接口 start =======================
$type_id = $_INPUT['type_id'];
$query = $_INPUT['query'];


$ret = get_api_result('customer/classify_list.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));
// ========================= 初始化接口 end =======================


// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{

    //****************** pc版 ******************
    include_once './category-pc.php';
}
else
{
    //****************** wap版 ******************
    include_once './category-wap.php';
}



// ========================= 最终模板输出  =======================
$tpl->output();
?>
