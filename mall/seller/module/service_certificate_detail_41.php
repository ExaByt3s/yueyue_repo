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

//��ʳ����

//���ù̶�����
$certificate_diet_identification = pai_mall_load_config('certificate_diet_identification');//��֤���
$certificate_diet_job = pai_mall_load_config('certificate_diet_job');//ְҵ
$certificate_diet_max_forward = pai_mall_load_config('certificate_diet_max_forward');//΢��ת����
$certificate_diet_years = pai_mall_load_config('certificate_diet_years');//��Ʒ����

//���������������ݽṹ
$certificate_diet_identification_new = array_to_square_array($certificate_diet_identification);
$certificate_diet_job_new = array_to_square_array($certificate_diet_job);
$certificate_diet_max_forward_new = array_to_square_array($certificate_diet_max_forward);
$certificate_diet_years_new = array_to_square_array($certificate_diet_years);




$tpl->assign("certificate_diet_identification",$certificate_diet_identification_new);
$tpl->assign("certificate_diet_job",$certificate_diet_job_new);
$tpl->assign("certificate_diet_max_forward",$certificate_diet_max_forward_new);
$tpl->assign("certificate_diet_years",$certificate_diet_years_new);


?>