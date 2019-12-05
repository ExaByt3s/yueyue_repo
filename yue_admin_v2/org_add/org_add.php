<?php 

 /*
 * 机构增加
 *
 */
 include("common.inc.php");
 include_once ('/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php');
 $tpl = new SmartTemplate("org_add.tpl.htm");
 $organization_obj    = POCO::singleton('pai_organization_class');
 $user_obj            = POCO::singleton('pai_user_class');
 $act  = $_INPUT['act']  ? $_INPUT['act'] : '';
 //更新数据
 if ($act == 'insert') 
 {
 	$nick_name  = trim($_INPUT['nick_name']);
    $pwd_hash   = trim($_INPUT['pwd_hash']);
    if (strlen($nick_name) <1)
    {
        echo "<script type='text/javascript'>window.alert(\"机构名不能为空\");history.back();</script>";
        exit;
    }
    if (strlen($pwd_hash) <1)
    {
        echo "<script type='text/javascript'>window.alert(\"密码不能为空\");history.back();</script>";
        exit;
    }
    if (strlen($pwd_hash) < 6) 
    {
        echo "<script type='text/javascript'>window.alert(\"密码长度不能小于6位\");history.back();</script>";
        exit;
    }
    $link_man    = trim($_INPUT['link_man']);
    $province    = intval($_INPUT['province']);
    $location_id = intval($_INPUT['location_id']);
    $address     = trim($_INPUT['address']);
    $org_desc    = trim($_INPUT['org_desc']);
    if($location_id <1)
    {
        echo "<script type='text/javascript'>window.alert(\"地区不能为空\");history.back();</script>";
        exit;
    }
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
       $user_info_arr['location_id'] = $location_id;
       $user_info_arr['role']      = 'organization';
       $user_info_arr['nick_name'] = $nick_name;
       $user_info_arr['pwd']       = $pwd_hash;
       $user_info_arr['cellphone'] = $cellphone;
       $user_id = $user_obj->create_account($user_info_arr);
       $update_data['cellphone']   = $user_id;
       $user_obj->update_user($update_data, $user_id);
    }
    if ($user_id) 
    {
        $data['user_id']   = $user_id;
        $data['nick_name'] = $nick_name;
        $data['link_man']  = $link_man;
        $data['address']   = $address;
        $data['org_desc']  = $org_desc;
        $data['status'] = 1;
        $data['add_time']  = time();
        //print_r($data);exit;
        $ret = $organization_obj->add_org($data);
        //exit;
        if ($ret) 
        {
           echo "<script type='text/javascript'>window.alert(\"添加机构成功\");location.href=\"org_list_v2.php\"</script>";
           exit;
        }
    }
   echo "<script type='text/javascript'>window.alert(\"添加机构失败\");history.back();</script>";
   exit;
 }
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();