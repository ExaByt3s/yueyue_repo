<?php

//****************** wap�� ͷ��ͨ�� start  ******************

$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'category/index.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

// ��ʱ���ϵķ�������
// hudw 2015.10.1

$share_txt_arr = array
(
	31 => 'Լģ�� | 100000+ģ������Լ',// Լģ��
	5  => 'Լ��ѵ | ��ʦ������ѧϰ����', // Լ��ѵ
	3  => 'Լ��ױ | ����ױ��Ϊ��������', // Լ��ױ
	12 => '��ҵ���� | һվʽ���������������', // ��ҵ����
	99 => 'Լ� | ��𱬵ľ��ʻ��������', // Լ�
	40 => 'Լ��Ӱ | ����ʡ�������ײ����ľ���',// Լ��Ӱ
	41 => 'Լ��ʳ | ��ʳ���˴��㿪�����ص�ζ��֮��', // Լ��ʳ
	43 => 'Լ��Ȥ | ȫ����TOP�ĳ�����˴����' // Լ��Ȥ 
);

$type_id = intval($_INPUT['type_id']);
$share_txt_str = $share_txt_arr[$type_id];


$tpl->assign('share_txt_str', $share_txt_str);
$tpl->assign('ret', $ret['data']);



?>