<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$pai_user_obj = POCO::singleton ( 'pai_user_class' );
//�人���а�
$ranking_array = array('24'=>array('ÿ����ģ', 'whb', 'Сʱ', ''),
					   //'32'=>array('����ֵ���а�', 'wh_mlbxb', '����', ''),
					   //'33'=>array('�Ը�Ů�� ˽��ר��','wh_xgnw', '����', ''),
					   '34'=>array('ԼԼ�Ƽ� ��Ƭ��֤','wh_yytj', 'Сʱ', ''),
					   //'35'=>array('����ģ��', 'wh_gdmt', '����', '')
					   );     
                       


return $ranking_array;
?>