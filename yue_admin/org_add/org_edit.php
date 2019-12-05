<?php 

 /*
 * 机构修改
 *
 */
 include("common.inc.php");
 include_once 'include/locate_file.php';
 include_once 'include/common_function.php';
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 $tpl = new SmartTemplate("org_edit.tpl.htm");
 $organization_obj    = POCO::singleton('pai_organization_class');
 $user_obj            = POCO::singleton('pai_user_class');
 $act     = $_INPUT['act']  ? $_INPUT['act'] : '';
 $user_id = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : 0;
 if (empty($user_id)) 
 {
   echo "<script type='text/javascript'>window.alert('非法操作');history.back();</script>";
        exit;
 }
 //更新数据
 if ($act == 'update') 
 {
    $nick_name  = $_INPUT['nick_name'] ? $_INPUT['nick_name'] : '';
    $pwd_hash   = $_INPUT['pwd_hash'] ?  $_INPUT['pwd_hash'] : '';
    $link_man   = $_INPUT['link_man'] ? $_INPUT['link_man'] : '';
    $address    = $_INPUT['address'] ? $_INPUT['address'] : '';
    $org_desc   = $_INPUT['org_desc'] ? $_INPUT['org_desc'] : '';
    $province    = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
    $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
    if ($nick_name == '') 
    {
        echo "<script type='text/javascript'>window.alert(\"机构名不能为空\");history.back();</script>";
        exit;
    }
    if ($pwd_hash) 
    {
        if (strlen($pwd_hash) < 6) 
        {
           echo "<script type='text/javascript'>window.alert(\"密码长度不能小于6位\");history.back();</script>";
           exit;
        }
        else
        {

            $info = $user_obj->update_pwd_by_user_id($user_id, $pwd_hash);
            //$data['pwd_hash'] = md5($pwd_hash);
        }
    }
    $user_info_arr['location_id'] = $location_id != 0 ? $location_id : $province;
    $info_id = $user_obj->update_user($user_info_arr, $user_id);

    $data['nick_name'] = $nick_name;
    $data['link_man']  = $link_man;
    $data['address']   = $address;
    $data['org_desc']  = $org_desc;
    $ret = $organization_obj->update_org($data, $user_id);
    //var_dump($info);exit;
    if ($ret || $info || $info_id) 
     {
        echo "<script type='text/javascript'>window.alert(\"更新机构成功\");location.href='org_list.php';</script>";
        exit;
     }
     echo "<script type='text/javascript'>window.alert(\"更新机构失败\");history.back();</script>";
     exit;
 }
 elseif ($act == 'delete') 
 {
     $ret = $organization_obj->del_org($user_id);
     if ($ret) 
     {
         echo "<script type='text/javascript'>window.alert(\"删除机构成功\");location.href='org_list.php';</script>";
        exit;
     }
     echo "<script type='text/javascript'>window.alert(\"删除机构失败\");history.back();</script>";
     exit;
 }
    $ret = $organization_obj->get_org_info_by_user_id($user_id);
    $user_info = $user_obj->get_user_info($user_id);
    //省和接口
    $location_id_info = get_poco_location_name_by_location_id ($user_info['location_id'], true, true);
    //省
    $province_list = change_assoc_arr($arr_locate_a);
    if (isset($location_id_info['level_1']) && is_array($location_id_info['level_1'])) 
    {
        $prov_id = substr($location_id_info['level_1']['id'], 0 , 6);
        foreach ($province_list as $key => $vo) 
        {
         if (isset($location_id_info['level_1']) && $vo['c_id'] == $prov_id) 
         {
            $province_list[$key]['selected_prov'] = "selected='true'";
          }
        }
        $city_list = ${'arr_locate_b'.$prov_id};
        if (!empty($city_list) && is_array($city_list) ) 
        {
          $city_list = change_assoc_arr($city_list);
          foreach ($city_list as $c_key => $vo) 
          {
           if ($vo['c_id'] == $location_id_info['level_1']['id']) 
           {
              $city_list[$c_key]['selected_city'] = "selected='true'";
           }
         }
        }
    }
 $tpl->assign('province_list', $province_list);
 $tpl->assign('city_list', $city_list);
 $tpl->assign($ret);
 $tpl->assign('YUE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
 $tpl->output();
 ?>