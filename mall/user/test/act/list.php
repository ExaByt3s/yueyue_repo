<?php

include_once 'config.php';

// ========================= ��ʼ���ӿ� start =======================
$return_query = trim($_INPUT['return_query']);
$main_title = mb_convert_encoding($_INPUT['title'], 'gbk', 'utf8');


// ========================= ��ʼ���ӿ� end =======================



// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

    //****************** pc�� ******************
    include_once './list-pc.php';


}
else
{
    //****************** wap�� ******************
    include_once './list-wap.php';
}




// ========================= ����ģ�����  =======================
$tpl->output();
?>