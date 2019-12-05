<?php
include_once 'config.php';

die('页面不存在');
// ========================= 初始化接口 start =======================
$type_id = trim($_INPUT['type_id']);
$query = trim($_INPUT['query']);
$keywords = trim($_INPUT['keywords']);
$search_type = trim($_INPUT['search_type']);
$page = intval($_INPUT['p']);
$a_key = trim($_INPUT['a_key']);
$b_key = trim($_INPUT['b_key']);
$orderby = intval($_INPUT['orderby']);
if (empty($main_title )) 
{
    $main_title = '搜索';
}
/*
$ret = get_api_result('customer/classify_list.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));
	*/
// ========================= 初始化接口 end =======================


 
// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{

	
	include_once './search-pc.php';

    //****************** pc版 ******************
    


}
else
{
    //****************** wap版 ******************
    include_once './search-wap.php';
}


//****************** pc版头部通用 start ******************



// ========================= 最终模板输出  =======================
$tpl->output();
?>
