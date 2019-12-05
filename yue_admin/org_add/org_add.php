<?php 

 /*
 * 机构增加
 *
 */
 include("common.inc.php");
 include("include/common_function.php");
 include_once 'include/locate_file.php';
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'org_add', $val = 'is_insert');
 $tpl = new SmartTemplate("org_add.tpl.htm");
 $organization_obj    = POCO::singleton('pai_organization_class');
 $user_obj            = POCO::singleton('pai_user_class');
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 //更新数据
 if ($act == 'insert') 
 {
 	$nick_name  = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
    $pwd_hash   = $_INPUT['pwd_hash'] ?  $_INPUT['pwd_hash'] : '';
    //$cellphone  = $_INPUT['cellphone'] ?  intval($_INPUT['cellphone']) : 0;
    if ($nick_name == '') 
    {
        echo "<script type='text/javascript'>window.alert(\"机构名不能为空\");history.back();</script>";
        exit;
    }
    if ($pwd_hash == '') 
    {
        echo "<script type='text/javascript'>window.alert(\"密码不能为空\");history.back();</script>";
        exit;
    }
    if (strlen($pwd_hash) < 6) 
    {
        echo "<script type='text/javascript'>window.alert(\"密码长度不能小于6位\");history.back();</script>";
        exit;
    }
    $link_man    = $_INPUT['link_man'] ? $_INPUT['link_man'] : '';
    $province    = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
    $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
    $address     = $_INPUT['address'] ? $_INPUT['address'] : '';
    $org_desc    = $_INPUT['org_desc'] ? $_INPUT['org_desc'] : '';

    $cellphone = (int)("1200".makerand(0,9,7));
    while($user_id = $user_obj->get_user_id_by_phone($cellphone)) 
    {
        //若存在则循环生成，直到不存在为止
        $cellphone = (int)("1200".makerand(0,9,7));
    }
    //$user_id = $user_obj->get_user_id_by_phone($cellphone);
    //为空添加ID
    if(empty($user_id))
    {
       $user_info_arr['location_id'] = $location_id != 0 ? $location_id : $province;
       $user_info_arr['role']      = 'organization';
       $user_info_arr['nick_name'] = $nick_name;
       $user_info_arr['pwd']       = $pwd_hash;
       $user_info_arr['cellphone'] = $cellphone;
       $user_id = $user_obj->create_account($user_info_arr);
       $update_data['cellphone']   = $user_id;
       $user_obj->update_user($update_data, $user_id);
    }
    //echo $user_id;
    //这里测试不修改密码
    /*else
    {
        $user_info_arr['role']      = 'organization';
        $user_obj->update_user($user_info_arr, $user_id);
        $user_obj->update_pwd_by_user_id($user_id, $pwd_hash);
    }
    $id = $organization_obj->get_org_id_by_user_id($user_id);
    if ($id) 
    {
        echo "<script type='text/javascript'>window.alert(\"手机号码已经被使用了\");history.back();</script>";
        exit;
    }*/
    if ($user_id) 
    {
        $data['user_id']   = $user_id;
        $data['nick_name'] = $nick_name;
        $data['link_man']  = $link_man;
        $data['address']   = $address;
        $data['org_desc']  = $org_desc;
        $data['add_time']  = time();
        //print_r($data);exit;
        $ret = $organization_obj->add_org($data);
        //exit;
        if ($ret) 
        {
           echo "<script type='text/javascript'>window.alert(\"添加机构成功\");location.href=\"org_list.php\"</script>";
           exit;
        }
    }
   echo "<script type='text/javascript'>window.alert(\"添加机构失败\");history.back();</script>";
   exit;
 }
 $province_list = change_assoc_arr($arr_locate_a);
 $tpl->assign('province_list', $province_list);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>