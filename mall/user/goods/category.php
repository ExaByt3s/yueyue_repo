<?php
include_once 'config.php';


// ========================= ��ʼ���ӿ� start =======================
$type_id = $_INPUT['type_id'];
$query = $_INPUT['query'];


$ret = get_api_result('customer/classify_list.php',array(
    'user_id' => $yue_login_id,
    'type_id' => $type_id,
    'query' => $query
    ));
// ========================= ��ʼ���ӿ� end =======================


// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

    //****************** pc�� ******************
    include_once './category-pc.php';
}
else
{
    //****************** wap�� ******************
    include_once './category-wap.php';
}



// ========================= ����ģ�����  =======================
$tpl->output();
?>