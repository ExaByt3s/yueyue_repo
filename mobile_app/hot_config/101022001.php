<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//�ɶ����а�
$ranking_array = array('49'=>array('ÿ����ģ', '49', 'Сʱ', ''),
					   //'50'=>array('����ֵ���а�', '50', '����', ''),
					   '51'=>array('�Ը�Ů�� ˽��ר��','51', '����', ''),
					   '52'=>array('ԼԼ�Ƽ� ��Ƭ��֤','52', 'Сʱ', ''),
					   '53'=>array('����ģ��', '53', '����', '')); 
                       


return $ranking_array;
?>