<?php 


	include_once 'common.inc.php';
    check_authority(array('cameraman'));
    //$tpl = new SmartTemplate("model_add_end.tpl.htm");
 	//$model_add_obj  = POCO::singleton('pai_model_add_class');
    $phone = $_INPUT['phone'] ? $_INPUT['phone'] : 0;
    $phone = (int)$phone;
    $regPartton="/^1[3-8]+\d{9}$/i";
    if (!preg_match($regPartton, $phone )) 
    {
    	echo "<script type='text/javascript'>window.alert('手机号码格式有误');location.href='cameraman_add_first.php';</script>";
    	exit;
    }
    $user_obj = POCO::singleton('pai_user_class');
    $user_id = $user_obj->get_user_id_by_phone($phone);
    if (empty($user_id)) 
    {
        $insert_data['cellphone'] = $phone;
        $insert_data['pwd']       = 123456;
        $insert_data['role']      = 'cameraman';
        $insert_data['nickname']  = "手机用户".substr($phone,-4);
        $user_obj->create_account($insert_data);
        //die('ok');
        $user_id = $user_obj->get_user_id_by_phone($phone);
        $insert_model ["user_id"] = $user_id;
    }
    header("location:cameraman_detail.php?uid={$user_id}");
    exit;

 ?>