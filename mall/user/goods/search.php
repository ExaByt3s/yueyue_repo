<?php
include_once 'config.php';

die('ҳ�治����');
// ========================= ��ʼ���ӿ� start =======================
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
    $main_title = '����';
}
/*
$ret = get_api_result('customer/classify_list.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));
	*/
// ========================= ��ʼ���ӿ� end =======================


 
// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

	
	include_once './search-pc.php';

    //****************** pc�� ******************
    


}
else
{
    //****************** wap�� ******************
    include_once './search-wap.php';
}


//****************** pc��ͷ��ͨ�� start ******************



// ========================= ����ģ�����  =======================
$tpl->output();
?>
