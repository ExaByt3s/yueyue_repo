<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//�������а�
$ranking_array = array('25'=>array('ÿ����ģ', '25', 'Сʱ', ''),
					   //'36'=>array('����ֵ���а�', '36', '����', ''),
					   '37'=>array('�Ը�Ů�� ˽��ר��','37', '����', ''),
					   '38'=>array('ԼԼ�Ƽ� ��Ƭ��֤','38', 'Сʱ', ''),
					   '39'=>array('����ģ��', '39', '����', '')); 
                       


return $ranking_array;
?>