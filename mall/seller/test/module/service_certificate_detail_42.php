<?php
/*
 *
 * ������֤ģ��
 *
 *
 *
 *
 *
 */

//��Ӱ����

//�����ض�����
$certificate_activity_do_well = pai_mall_load_config('certificate_activity_do_well');//�ó��Ļ
//���������Ѿ��Ƕ�ά�������������࣬���������ά����
$certificate_activity_is_past_lead = pai_mall_load_config('certificate_activity_is_past_lead');//��ǰ��֯�Ļ
//���������Ѿ��Ƕ�ά�������������࣬���������ά����

$tpl->assign("certificate_activity_do_well",$certificate_activity_do_well);
$tpl->assign("certificate_activity_is_past_lead",$certificate_activity_is_past_lead);

//��ȡ���˽�������
//print_r($seller_info);

$introduce = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$seller_info["seller_data"]["profile"][0]["introduce"]);

$tpl->assign("introduce",$introduce);

?>