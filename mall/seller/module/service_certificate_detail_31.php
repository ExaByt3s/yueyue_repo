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

//ģ�ط���

//�����ض�����
$certificate_common_sex = pai_mall_load_config('certificate_common_sex');//ģ���Ա�
//���������������ݽṹ
$certificate_common_sex_new = array_to_square_array($certificate_common_sex);


$tpl->assign("certificate_common_sex",$certificate_common_sex_new);
//����ģ��CUP����
$cup_num_array = array(
    array("cup"=>"30(65)"),
    array("cup"=>"32(70)"),
    array("cup"=>"34(75)"),
    array("cup"=>"36(80)"),
    array("cup"=>"38(85)")
);//cup��������ʽ����
$cup_english_array = array(
    array("cup"=>"A"),
    array("cup"=>"B"),
    array("cup"=>"C"),
    array("cup"=>"D"),
    array("cup"=>"E+")
);//cup��Ӣ����ʽ����
$tpl->assign("cup_num_array",$cup_num_array);
$tpl->assign("cup_english_array",$cup_english_array);


?>