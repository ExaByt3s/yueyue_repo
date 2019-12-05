<?php 
/*
 * xiao xiao
 * 模特查看控制器 审核控制器
*/
    include_once '../audit/include/Classes/PHPExcel.php';
    include('common.inc.php');
    include('include/common_function.php');
    include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
    $user_obj  = POCO::singleton('pai_user_class'); 
    $model_org_obj = POCO::singleton('pai_model_org_class');
    $user_icon_obj = POCO::singleton('pai_user_icon_class');
    $model_add_obj = POCO::singleton('pai_model_add_class');
    $model_audit_obj = POCO::singleton('pai_model_audit_class'); 
    //机构关联表
    $model_relate_org_obj = POCO::singleton ( 'pai_model_relate_org_class' );
    $tpl = new SmartTemplate("model_org_list.tpl.htm");
    //$page_obj = new show_page ();
    //$show_count = 20;
    //$nickname    = $user_obj->get_user_nickname_by_user_id($yue_login_id);
    $state       = isset($_INPUT['state']) ? intval($_INPUT['state']) : 1;
    $act         = $_INPUT['act'] ? $_INPUT['act'] : '';
    $ids         = $_INPUT['ids'] ? $_INPUT['ids'] : '';
/*  $cup_id      = $_INPUT['cup_id'] ? intval($_INPUT['cup_id']) : 0;
    $cup_a       = $_INPUT['cup_a'] ? intval($_INPUT['cup_a']) : 0; 
    $min_age     = $_INPUT['min_age'] ? intval($_INPUT['min_age']) : ''; 
    $max_age     = $_INPUT['max_age'] ? intval($_INPUT['max_age']) : ''; 
    $min_height  = $_INPUT['min_height'] ? intval($_INPUT['min_height']) : '';
    $max_height  = $_INPUT['max_height'] ? intval($_INPUT['max_height']) : '';
    $min_weight  = $_INPUT['min_weight'] ? intval($_INPUT['min_weight']) : '';
    $max_weight  = $_INPUT['max_weight'] ? intval($_INPUT['max_weight']) : '';
    $sort        = $_INPUT['sort'] ? $_INPUT['sort'] : 'ptime_desc';*/
    /*if (empty($nickname)) 
    {
        echo "<script type='text/javascript'>window.alert('没有权限');location.href='../index.php'</script>";
        exit;
    }*/
    //$retUrl = $_SERVER['HTTP_REFERER'];
    //上架
    if ($act == 'up') 
    {
        //print_r($ids);exit;
        //更新权限
        //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'organization', 'is_update');
        //var_dump($ids);exit;
        if (empty($ids) || !is_array($ids)) 
        {
            echo "<script type='text/javascript'>window.alert('传过来数据不能为空!');location.href='model_org_list.php?state=0'</script>";
            exit;
        }
        $reason = '机构审核通过';
        foreach ($ids as $key => $vo) 
        {
            $model_audit_obj->update_model(array("is_approval"=>1,"audit_time"=>time(),"audit_user_id"=>$yue_login_id, "reason"=> $reason), $vo);
            //$model_org_obj->model_org_insert_data($data);
        }
         echo "<script type='text/javascript'>window.alert('上架成功');location.href='model_org_list.php?state=1'</script>";
        exit;
    }
    //下架
    if ($act == 'down') 
    {
        //更新权限
        //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'organization', 'is_update');
        if (empty($ids) || !is_array($ids)) 
        {
            echo "<script type='text/javascript'>window.alert('传过来数据不能为空!');location.href='model_org_list.php?state=0'</script>";
            exit;
        }
        $reason = '机构审核不通过';
        foreach ($ids as $key => $vo) 
        {
            $model_audit_obj->update_model(array("is_approval"=>2,"audit_time"=>time(),"audit_user_id"=>$yue_login_id, "reason"=> $reason), $vo);
            //$model_org_obj->model_org_insert_data($data);
        }
         echo "<script type='text/javascript'>window.alert('下架成功');location.href='model_org_list.php?state=1'</script>";
        exit;
    }
    //查询权限
    //check_authority_by_list($ret_type = 'exit_type',$authority_list, 'organization', 'is_select');
    $where_str = "org_id = {$yue_login_id}";
    //$page_obj->setvar(array('state' =>$state));
    $total_count = $model_relate_org_obj->get_model_org_list_by_org_id(true,$where_str);
    //查询关联表
    $list = $model_relate_org_obj->get_model_org_list_by_org_id(false,$where_str, '0,{$total_count}', 'id DESC',$fields = 'user_id,priority');
    if (!empty($list) && is_array($list)) 
    {
        //$list = array_change_by_val($list, 'user_id');
        //上架的
        if ($state == 1) 
        {
            //echo "上架";
            foreach ($list as $key => $vo) 
            {
                $user_id = (int)($vo['user_id']);
                $where_str_audit = "user_id = {$user_id} AND is_approval = 1";
                //echo $where_str_audit;
                $info = $model_audit_obj->get_id_by_user_id_where_str($where_str_audit);
                if (!$info) 
                {
                    unset($list[$key]);
                }
            }
            
        }
        //下架
        if ($state == 0) 
        {
            //echo "下架";
           foreach ($list as $key => $vo) 
            {
                //echo $vo;
                $user_id = (int)($vo['user_id']);
                $where_str_audit = "user_id = {$user_id} AND is_approval <> 1";
                //echo $where_str_audit;
                $info = $model_audit_obj->get_id_by_user_id_where_str($where_str_audit);
                //var_dump($info);
                if (!$info) 
                {
                    unset($list[$key]);
                }
            }
        }
    }
    //print_r($list);exit;
    if (!empty($list) && is_array($list)) 
    {
        //$key_list = 0;
        foreach ($list as $key_list => $vo) 
        {
            $list[$key_list]['nickname']         = get_user_nickname_by_user_id($vo['user_id']);
            $list[$key_list]['cellphone']        = $user_obj->get_phone_by_user_id($vo['user_id']);
            $list[$key_list]['icon']             = $user_icon_obj->get_user_icon($vo['user_id'], 32);
            $list[$key_list]['thumb']            = $user_icon_obj->get_user_icon($vo['user_id'], 100);
            $list[$key_list]['name']             = $model_add_obj->get_user_name_by_user_id($vo['user_id']); 
            //$key_list++;

            /*$list[$key]['app_name']     = $model_org_obj->get_model_nickname_by_uid($vo['uid']); 
            $list[$key]['weixin_id']    = $model_org_obj->get_model_weixin_by_uid($vo['uid']);
            $list[$key]['city']         = get_poco_location_name_by_location_id($vo['location_id']);*/
        }
    }
    //print_r($list);
    $tpl->assign('state', $state);
    //$tpl->assign ( "page", $page_obj->output ( 1 ) );
    $tpl->assign("list", array_values($list));
    $tpl->assign("total_count", count($list));
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();

 ?>