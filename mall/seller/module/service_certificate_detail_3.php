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


//��ױ����

//�����ض�����
$certificate_common_years = pai_mall_load_config('certificate_common_years');//ʱ������
$certificate_dresser_has_place = pai_mall_load_config('certificate_dresser_has_place');//��ױ����
$certificate_dresser_order_way = pai_mall_load_config('certificate_dresser_order_way');//�ӵ���ʽ
$certificate_dresser_team_num = pai_mall_load_config('certificate_dresser_team_num');//�Ŷӹ�ģ
$certificate_dresser_do_well = pai_mall_load_config('certificate_dresser_do_well');//�ó���ױ��

//���������������ݽṹ

$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_dresser_has_place_new = array_to_square_array($certificate_dresser_has_place);
$certificate_dresser_order_way_new = array_to_square_array($certificate_dresser_order_way);
$certificate_dresser_team_num_new = array_to_square_array($certificate_dresser_team_num);
$certificate_dresser_do_well_new = array_to_square_array($certificate_dresser_do_well);



$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_dresser_has_place",$certificate_dresser_has_place_new);
$tpl->assign("certificate_dresser_order_way",$certificate_dresser_order_way_new);
$tpl->assign("certificate_dresser_team_num",$certificate_dresser_team_num_new);
$tpl->assign("certificate_dresser_do_well",$certificate_dresser_do_well_new);






?>