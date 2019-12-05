<?php
set_time_limit(0);

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "select * from test.test_seller_user where status=0";
$arr = db_simple_getdata($sql,false,101);

$user_obj = POCO::singleton ( 'pai_user_class' );

foreach($arr as $val)
{
    $val['cellphone'] = trim($val['cellphone']);
    $user_info_arr['nickname'] = $val['nickname'];
    $user_info_arr['cellphone'] = $val['cellphone'];
    if(empty($val['pwd']))
    {
        $user_info_arr['pwd'] = 'yue123456';
    }
    else
    {
        $user_info_arr['pwd'] = $val['pwd'];
    }
    $user_id = $user_obj->get_user_id_by_phone($val['cellphone']);
    $pwd = $val['pwd'];
    var_dump($user_id);
    var_dump($pwd);
    if($user_id && $pwd)
    {
        $user_obj->update_pwd_by_user_id($user_id, $pwd);
    }
    else
    {
        $user_id = $user_obj->create_mall_account($user_info_arr, $err_msg = "");
    }
    var_dump($user_id);
    var_dump($err_msg);

    $sql = "update test.test_seller_user set status=1,user_id={$user_id} where id=".$val['id'];
    db_simple_getdata($sql,false,101);
}













?>