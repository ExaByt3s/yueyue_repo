<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$cellphone = ( int ) $_INPUT ['cellphone'];
$poco_id = ( int ) $_INPUT ['poco_id'];

$user_obj = POCO::singleton ( 'pai_user_class' );
$bind_obj = POCO::singleton ( 'pai_bind_poco_class' );
$relate_obj = POCO::singleton ( 'pai_relate_poco_class' );

if (empty ( $cellphone )) {
	echo "<script>alert('手机不能为空');</script>";
	exit ();
}

if (! preg_match ( '/^1\d{10}$/isU', $cellphone )) {
	
	echo "<script>alert('手机格式错误');</script>";
	exit ();
}

if (empty ( $poco_id )) {
	echo "<script>alert('POCOID不能为空');</script>";
	exit ();
}

if (strlen($poco_id)>9 || strlen($poco_id)<4) {
    echo "<script>alert('POCOID格式错误');</script>";
    exit ();
}

//手机是否有注册过约约
$ret = $user_obj->check_cellphone_exist ( $cellphone );

if ($ret) {
	
	//当前POCOID关联的YUEID
	$relate_yue_id = $relate_obj->get_relate_yue_id ( $poco_id );
	
	//当前手机关联的YUEID
	$yue_id = $user_obj->get_user_id_by_phone ( $cellphone );
	
	//获取约约PC版绑定的POCOID
	$bind_poco_id = $bind_obj->get_bind_poco_id ( $yue_id );
	
	 if($bind_poco_id || $relate_yue_id)
     {
     	echo "<script>alert('该POCOID已绑定过手机，不能再绑定了');</script>";
		exit ();
     }
	
	//绑定POCO帐号
	$bind_obj->bind_poco_id ( $yue_id, $poco_id );
	
	echo "<script>alert('绑定成功');</script>";
	exit ();
	
	
} else {
	//创建YUE帐号
	$user_info_arr ['pwd'] = "yueus123456"; //直接传POCO帐号的MD5
	$user_info_arr ['cellphone'] = $cellphone;
	$user_info_arr ['nickname'] = "手机用户"; //POCO昵称
	$user_info_arr ['poco_id'] = $poco_id;
	$user_id = $user_obj->create_account_by_pc ( $user_info_arr, $err_msg );
	if ($user_id > 0) {
		echo "<script>alert('帐号创建成功');</script>";
	} else {
		echo "<script>alert('帐号创建失败{$user_id}');</script>";
	}
}

?>