<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/MobileAPP/key.inc.php');

ksort($_POST);

foreach($_GET AS $key=>$val)
{
   if($key != 'hash')
   {
        $get_str .= $key . '=' . $val . '&';
   } 
}
$get_str = trim($get_str, '&');

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '?';
$check_hash = md5($url . $get_str . YUEPAI_KEY);

$code = 1;
$msg  = '����';
$return_array = array();

if(!empty($user_id) && !empty($hash))
{
    if($check_hash == $hash)
    {
        //$nickname = iconv('gbk', 'utf8', POCO::execute(array('member.get_user_nickname_by_user_id'), array($user_id)));
        //$icon     = POCO::execute(array('member.get_user_icon'), array($user_id, 64));
        
        $nickname = iconv('gbk', 'utf8', get_user_nickname_by_user_id($user_id));
        $icon     = get_user_icon($user_id, $size = 86);   
        

        
        if($nickname || $icon)
        {
            $code = 0;
            $msg  = '����';
            
            $user_obj = POCO::singleton('pai_user_class');
            $role = $user_obj->check_role($user_id);
            if($role == 'model')
            {
                $return_array['value'] = array('nickname'=>$nickname, 
                                                'icon'=>$icon, 
                                                'url'=>"http://yp.yueus.com/mobile/app?from_app=1#model_card/{$user_id}", 
                                                'wifi_url'=>"http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/{$user_id}");                 
            }else{
                $return_array['value'] = array('nickname'=>$nickname, 
                                                'icon'=>$icon, 
                                                'url'=>"http://yp.yueus.com/mobile/app?from_app=1#zone/{$user_id}/cameraman", 
                                                'wifi_url'=>"http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/{$user_id}/cameraman");
            }
            
            //ԼԼС���� ���⴦��
            if($user_id == 10002) 
            {
                $return_array['value']['url'] = '';
                $return_array['value']['wifi_url'] = '';
            }
            
        }else{
            $msg = 'û�з���';
        }
    }else{
        $msg = 'HASH����';
    }    
}else{
    $msg = 'ȱ�ٲ���';
}

json_msg($code, $msg, $return_array);
?>