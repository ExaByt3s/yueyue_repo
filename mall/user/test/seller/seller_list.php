<?php
include_once 'config.php';

// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{
    //****************** pc�� ******************
    include_once './seller_list-pc.php';
}
else
{
	// ========================= ��ʼ���ӿ� start =======================



	// ========================= ��ʼ���ӿ� end =======================
    //****************** wap�� ******************
    include_once './seller_list-wap.php';
}
// ========================= ����pc��wapģ�������ݸ�ʽ���� end  =======================


// ========================= ����ģ�����  =======================
$tpl->output();
?>