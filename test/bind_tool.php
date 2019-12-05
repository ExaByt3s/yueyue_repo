<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$cellphone = ( int ) $_INPUT ['cellphone'];
$poco_id = ( int ) $_INPUT ['poco_id'];

$user_obj = POCO::singleton ( 'pai_user_class' );
$bind_obj = POCO::singleton ( 'pai_bind_poco_class' );
$relate_obj = POCO::singleton ( 'pai_relate_poco_class' );

if (empty ( $cellphone )) {
	echo "<script>alert('�ֻ�����Ϊ��');</script>";
	exit ();
}

if (! preg_match ( '/^1\d{10}$/isU', $cellphone )) {
	
	echo "<script>alert('�ֻ���ʽ����');</script>";
	exit ();
}

if (empty ( $poco_id )) {
	echo "<script>alert('POCOID����Ϊ��');</script>";
	exit ();
}

if (strlen($poco_id)>9 || strlen($poco_id)<4) {
    echo "<script>alert('POCOID��ʽ����');</script>";
    exit ();
}

//�ֻ��Ƿ���ע���ԼԼ
$ret = $user_obj->check_cellphone_exist ( $cellphone );

if ($ret) {
	
	//��ǰPOCOID������YUEID
	$relate_yue_id = $relate_obj->get_relate_yue_id ( $poco_id );
	
	//��ǰ�ֻ�������YUEID
	$yue_id = $user_obj->get_user_id_by_phone ( $cellphone );
	
	//��ȡԼԼPC��󶨵�POCOID
	$bind_poco_id = $bind_obj->get_bind_poco_id ( $yue_id );
	
	 if($bind_poco_id || $relate_yue_id)
     {
     	echo "<script>alert('��POCOID�Ѱ󶨹��ֻ��������ٰ���');</script>";
		exit ();
     }
	
	//��POCO�ʺ�
	$bind_obj->bind_poco_id ( $yue_id, $poco_id );
	
	echo "<script>alert('�󶨳ɹ�');</script>";
	exit ();
	
	
} else {
	//����YUE�ʺ�
	$user_info_arr ['pwd'] = "yueus123456"; //ֱ�Ӵ�POCO�ʺŵ�MD5
	$user_info_arr ['cellphone'] = $cellphone;
	$user_info_arr ['nickname'] = "�ֻ��û�"; //POCO�ǳ�
	$user_info_arr ['poco_id'] = $poco_id;
	$user_id = $user_obj->create_account_by_pc ( $user_info_arr, $err_msg );
	if ($user_id > 0) {
		echo "<script>alert('�ʺŴ����ɹ�');</script>";
	} else {
		echo "<script>alert('�ʺŴ���ʧ��{$user_id}');</script>";
	}
}

?>