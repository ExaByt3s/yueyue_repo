<?php

include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================
$type_id = (int)$_INPUT['type_id'];
$return_query = trim($_INPUT['return_query']);
$main_title = mb_convert_encoding($_INPUT['title'], 'gbk', 'utf8');

if (empty($main_title )) 
{
    $main_title = '�����б�';
}

// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

    //****************** pc�� ******************
    include_once './service_list-pc.php';


}
else
{
    //****************** wap�� ******************
    include_once './service_list-wap.php';
}




// ========================= ����ģ�����  =======================
$tpl->output();
?>