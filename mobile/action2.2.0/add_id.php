<?php
ignore_user_abort ( true );
/**
 * ������֤ͼƬ
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * ҳ����ղ���
 */

if (! $yue_login_id)
{
	die ( 'no login' );
}

$img = $_INPUT ['img'];
$id_code = $_INPUT ['id_code'];
$name = $_INPUT ['name'];

if (! $img)
{
	$output_arr ['code'] = 0;
	$output_arr ['msg'] = '���ϴ�ͼƬ';
	
	mobile_output ( $output_arr, false );
	exit ();
}

$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );



$insert_data ['user_id'] = $yue_login_id;
$insert_data ['img'] = $img;
$insert_data ['id_code'] = $id_code;
$insert_data ['name'] = $name;

$ret = $id_audit_obj->add_audit ( $insert_data );

$output_arr ['code'] = $ret > 0 ? 1 : 0;
$output_arr ['msg'] = $ret ? '�ύ�ɹ�' : '�ύʧ��';

mobile_output ( $output_arr, false );

?>