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

//������֤

//���ù̶�����
$certificate_other_identifine_label = pai_mall_load_config('certificate_other_identifine_label');//��ݱ�ǩ

//���������������ݽṹ
$certificate_other_identifine_label_new = array_to_square_array($certificate_other_identifine_label);
$tpl->assign("certificate_other_identifine_label",$certificate_other_identifine_label_new);


?>