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





//��Ӱ��ѵ

//�����ض�����
$certificate_common_years = pai_mall_load_config('certificate_common_years');//ʱ������
$certificate_teacher_class_way = pai_mall_load_config('certificate_teacher_class_way');//�Ͽη�ʽ
$certificate_common_work_type = pai_mall_load_config('certificate_common_work_type');//��Ʒ����
$certificate_teacher_type = pai_mall_load_config('certificate_teacher_type');//��Ʒ����

//���������������ݽṹ

$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_teacher_class_way_new = array_to_square_array($certificate_teacher_class_way);
$certificate_common_work_type_new = array_to_square_array($certificate_common_work_type);
$certificate_teacher_type_new = array_to_square_array($certificate_teacher_type);



$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_teacher_class_way",$certificate_teacher_class_way_new);
$tpl->assign("certificate_common_work_type",$certificate_common_work_type_new);
$tpl->assign("certificate_teacher_type",$certificate_teacher_type_new);



?>