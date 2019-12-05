<?php
ignore_user_abort ( true );
/**
 * ������֤ͼƬ
 */

include_once('../common.inc.php');

/**
 * ҳ����ղ���
 */
$img = trim($_INPUT ['img']);
$id_code = trim($_INPUT ['id_code']);
$name = trim($_INPUT ['name']);

if (empty($yue_login_id))
{
	$output_arr ['code'] = 0;
	$output_arr ['msg'] = '��δ��¼';
	
	mobile_output ( $output_arr, false );
	exit ();
}

if (! $img)
{
	$output_arr ['code'] = 0;
	$output_arr ['msg'] = '���ϴ�ͼƬ';
	
	mobile_output ( $output_arr, false );
	exit ();
}

// �ύ���
$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );

$insert_data ['user_id'] = $yue_login_id;
$insert_data ['img'] = $img;
$insert_data ['id_code'] = $id_code;
$insert_data ['name'] = $name;

$ret = $id_audit_obj->add_audit ( $insert_data );

$output_arr ['code'] = $ret > 0 ? 1 : 0;
$output_arr ['msg'] = $ret ? '�ύ�ɹ�' : '�ύʧ��';
$output_arr ['data'] = array(
	"url" => './index.php?shz=v2'
);

mall_mobile_output ( $output_arr, false );

?>