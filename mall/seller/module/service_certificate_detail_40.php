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
$certificate_cameror_work_type = pai_mall_load_config('certificate_cameror_work_type');//��Ʒ����
$certificate_common_years = pai_mall_load_config('certificate_common_years');//ʱ������
$certificate_cameror_order_income = pai_mall_load_config('certificate_cameror_order_income');//��Ӱʦ�¾�����
$certificate_cameror_team = pai_mall_load_config('certificate_cameror_team');//�Ŷӹ���
//���������������ݽṹ
$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_cameror_work_type_new = array_to_square_array($certificate_cameror_work_type);
$certificate_cameror_order_income_new = array_to_square_array($certificate_cameror_order_income);
$certificate_cameror_team_new = array_to_square_array($certificate_cameror_team);

$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_cameror_work_type",$certificate_cameror_work_type_new);
$tpl->assign("certificate_cameror_order_income",$certificate_cameror_order_income_new);
$tpl->assign("certificate_cameror_team",$certificate_cameror_team_new);



?>