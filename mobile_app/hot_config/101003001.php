<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//�Ϻ����а�
$ranking_array = array('26'=>array('ÿ����ģ', '26', 'Сʱ', ''),
				   //'40'=>array('����ֵ���а�', '40', '����', ''),
				   '41'=>array('�Ը�Ů�� ˽��ר��','41', '����', ''),
				   '42'=>array('ԼԼ�Ƽ� ��Ƭ��֤','42', 'Сʱ', ''),
				   '43'=>array('����ģ��', '43', '����', '')); 
                       


return $ranking_array;
?>