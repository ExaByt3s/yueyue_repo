<?php 

/* 
 *榜单控制器
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  include_once 'include/locate_file.php';
  include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
  $rank_event_log_obj = POCO::singleton('pai_rank_event_log_class');
  $rank_event_obj = POCO::singleton('pai_rank_event_class');
  //频道
  include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");
  $cms_db_obj             = POCO::singleton ( 'cms_db_class' );
  $cms_system_obj         = POCO::singleton ( 'cms_system_class' );
  $page_obj               = new show_page ();
  $show_count             = 20;
  $act                    = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $selected               = "selected='true'";
  //共用
  $data_log['act']        = $act;
  $data_log['audit_id']   = $yue_login_id;
  $data_log['audit_time'] = time();
  //列表
  if ($act == 'list') 
  {
     $tpl = new SmartTemplate("rank_event_list.tpl.htm");
     $start_add_time = $_INPUT['start_add_time'] ? $_INPUT['start_add_time'] : '';
     $end_add_time   = $_INPUT['end_add_time'] ? $_INPUT['end_add_time'] : '';
     $add_id         = $_INPUT['add_id'] ? intval($_INPUT['add_id']) : '';
     $province       = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
     $location_id    = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
     $role           = $_INPUT['role'] ? $_INPUT['role'] : '';
     $where_str = "1";
     if ($start_add_time && $end_add_time) 
     {
       $start_tmp_time = strtotime($start_add_time);
       $end_tmp_time   = strtotime($end_add_time)+24*36000;
       $where_str .= " AND add_time BETWEEN {$start_tmp_time} AND {$end_tmp_time}";
     }
     if ($add_id) 
     {
        $where_str .= " AND add_id = {$add_id}";
     }

      //判断是否存在 地区管理员
      if(strlen($authority_list[0]['location_id']) >0)
      {
          $where_str .= " AND location_id IN ({$authority_list[0]['location_id']})";
          $setParam['aut_location'] = true;
      }
      //正常流程
      else
      {
          if ($province)
          {
              if ($location_id)
              {
                  $where_str .= " AND location_id = {$location_id}";
              }
              else
              {
                  $where_str .= " AND left(location_id,6) = {$province}";
                  //$location_id = $province;
              }
          }
      }
     if ($role) 
     {
       $where_str .= " AND role = '{$role}'";
     }
     $total_count = $rank_event_obj->get_rank_event_list(true, $where_str);
     $page_obj->setvar (
        array
        (
          'start_add_time' => $start_add_time,
          'end_add_time'   => $end_add_time,
          'add_id'         => $add_id,
          'province'       => $province,
          'location_id'    => $location_id,
          'role'           => $role
          )
      );
     $page_obj->set ( $show_count, $total_count );
     $list = $rank_event_obj->get_rank_event_list(false , $where_str, 'location_id DESC,sort_order DESC', $page_obj->limit(), '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
          $list[$key]['city']        = get_poco_location_name_by_location_id($vo['location_id']);
          $list[$key]['add_time']    = date('Y-m-d H:i:s', $vo['add_time']);
          $list[$key]['nick_name']   = get_user_nickname_by_user_id($vo['add_id']);
          $rank_info = $cms_system_obj->get_rank_info_by_rank_id($vo['rank_id']);
          $list[$key]['rank_name']   = $rank_info['rank_name'];
          //print_r($rank_info);
          $list[$key]["channel_name"] = $option_channel[$rank_info["channel_id"]];
          $list[$key]['desc']         = poco_cutstr($vo['rank_desc'], 20, '....');
          $list[$key]['url_desc']     = poco_cutstr($vo['url'], 20, '....');
       }
     }
     //print_r($list);exit;
     $province_list = change_assoc_arr($arr_locate_a);
     if ($province) 
     {
       foreach ($province_list as $key => $vo) 
       {
           if ($vo['c_id'] == $province) 
           {
               $province_list[$key]['selected_prov'] = "selected='true'";
           }
       } 
       $city_list = ${'arr_locate_b'.$province};
       $city_list = change_assoc_arr($city_list);
       foreach ($city_list as $c_key => $vo) 
       {
          if ($vo['c_id'] == $location_id) 
          {
             $city_list[$c_key]['selected_city'] = "selected='true'";
          }
       }
     }
     if ($role == 'model') 
     {
       $role_1 = $selected;
     }
     elseif ($role == 'cameraman') 
     {
       $role_2 = $selected;
     }
     //加log(无需写log)
     //$rank_event_log_obj->add_info($data_log);
     $tpl->assign($setParam);
     $tpl->assign('province_list', $province_list);
     $tpl->assign('city_list', $city_list);
     $tpl->assign('list', $list);
     $tpl->assign('start_add_time', $start_add_time);
     $tpl->assign('end_add_time', $end_add_time);
     $tpl->assign('add_id', $add_id);
     $tpl->assign('role_1', $role_1);
     $tpl->assign('role_2', $role_2);
     $tpl->assign('total_count', $total_count);
     $tpl->assign ( "page", $page_obj->output ( 1 ) );
  }
  //添加
  elseif ($act == 'add') 
  {
    $tpl = new SmartTemplate("rank_event_edit.tpl.htm");
    $channel_list = $cms_db_obj->get_cms_list("channel_tbl");
    $province_list = change_assoc_arr($arr_locate_a);
    $tpl->assign('channel_list', $channel_list);
    $tpl->assign('province_list', $province_list);
    $tpl->assign('act', 'insert');
  }
  //插入
  elseif ($act == 'insert') 
  {
     $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
     if(!$location_id)
     {
       echo "<script type='text/javascript'>window.alert('地区ID不能为空');location.href='rank_event.php?act=add';</script>";
       exit;
     }
      $location_id_root = trim($authority_list[0]['location_id']);
      if(strlen($location_id_root) >0)
      {
          $location_id_root = explode(',', $location_id_root);
          if(!in_array($location_id,$location_id_root))
          {
              //js_pop_msg('非法操作');
              echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=add';</script>";
              exit;
          }
      }

      $rank_id     = $_INPUT['rank_id'] ? intval($_INPUT['rank_id']) : 0;
     if(!$rank_id)
     {
       echo "<script type='text/javascript'>window.alert('榜单ID不能为空');location.href='rank_event.php?act=add';</script>";
       exit;
     }
     /*$rank_name   = $_INPUT['rank_name'] ? $_INPUT['rank_name'] : '';
     if(!$rank_name)
     {
       echo "<script type='text/javascript'>window.alert('名称不能为空');location.href='rank_event.php?act=add';</script>";
       exit;
     }*/
     $unit       = $_INPUT['unit'] ? intval($_INPUT['unit']) : 0;
     $rank_desc  = $_INPUT['rank_desc'] ? $_INPUT['rank_desc'] : '';
     $app_sort   = $_INPUT['app_sort'] ? intval($_INPUT['app_sort']) : 0;
     $dmid       = $_INPUT['dmid'] ? intval($_INPUT['dmid']) : 0;
     $sort_order = $_INPUT['sort_order'] ? intval($_INPUT['sort_order']) : 0;
     $role       = $_INPUT['role'] ? $_INPUT['role'] :'';
     $url        = $_INPUT['url'] ? $_INPUT['url'] :'';
     $data['rank_id']     = $rank_id;
     $data['unit']        = $unit;
     $data['rank_desc']   = $rank_desc;
     $data['app_sort']    = $app_sort;
     $data['dmid']        = $dmid;
     $data['sort_order']  = $sort_order;
     $data['location_id'] = $location_id;
     $data['add_id']      = $yue_login_id;
     $data['role']        = $role;
     $data['url']         = $url;
     $data['add_time']    = time();
     $data_log['data_log'] = $rank_event_obj->get_serialize_rank_event_list();
     $info = $rank_event_obj->add_info($data);
     if ($info) 
     {
      //echo $info;exit;
      //加log
       $data_log['rank_event_id'] = $info;
       $rank_event_log_obj->add_info($data_log);
       echo "<script type='text/javascript'>window.alert('添加榜单成功');location.href='rank_event.php?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('添加榜单失败');location.href='rank_event.php?act=add';</script>";
       exit;
  }
  //修改
  elseif ($act == 'edit') 
  {
     $tpl = new SmartTemplate("rank_event_edit.tpl.htm");
     $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
     if (!$id) 
     {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=list';</script>";
        exit;
     }
     $ret = $rank_event_obj->get_rank_event_info($id);
     $province_list = change_assoc_arr($arr_locate_a);
     $channel_list = $cms_db_obj->get_cms_list("channel_tbl");
     if ($ret) 
     {
        $unit     = $ret['unit'];
        $app_sort = $ret['app_sort'];
        $role     = $ret['role'];
        $province = substr($ret['location_id'], 0,6);
        foreach ($province_list as $key => $vo) 
        {
            if ($vo['c_id'] == $province) 
            {
                $province_list[$key]['selected_prov'] = "selected='true'";
            }
        } 
        $city_list = ${'arr_locate_b'.$province};
        $city_list = change_assoc_arr($city_list);
        foreach ($city_list as $c_key => $vo) 
        {
           if ($vo['c_id'] == $ret['location_id']) 
           {
              $city_list[$c_key]['selected_city'] = "selected='true'";
           }
        }
        if ($unit == 1) 
        {
          $unit_1 = $selected;
        }
        elseif ($unit == 2) 
        {
          $unit_2 = $selected;
        }
        elseif ($unit == 3) 
        {
          $unit_3 = $selected;
        }
        elseif ($unit == 4) 
        {
          $unit_4 = $selected;
        }
        if ($app_sort == 1) 
        {
          $sort_1 = $selected;
        }
        elseif ($app_sort == 2) 
        {
          $sort_2 = $selected;
        }
        if ($role == 'model') 
        {
          $role_1 = $selected;
        }
        elseif ($role == 'cameraman') 
        {
           $role_2 = $selected;
        }

        //榜单
        $rank_info = $cms_system_obj->get_rank_info_by_rank_id($ret['rank_id']);
        if ($rank_info) 
        {
          $channel_id = $rank_info['channel_id'];
          foreach ($channel_list as $key => $vo) 
          {
            if ($vo['channel_id'] == $channel_id) 
            {
              $channel_list[$key]['channel_selected'] = $selected;
            }
            # code...
          }
          $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
          $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");
          //取榜单
          if ($rank_list) 
          {
            foreach ($rank_list as $rank_key => $rank_vo) 
            {
              if ($rank_vo['rank_id'] == $ret['rank_id']) 
              {
                $rank_list[$rank_key]['rank_selected'] = $selected;
              }
            }
            # code...
          }
        }

     }
     $tpl->assign('channel_list', $channel_list);
     //$tpl->assign('channel_id', $channel_id);
     $tpl->assign('rank_list', $rank_list);
     $tpl->assign('province_list', $province_list);
     $tpl->assign('city_list', $city_list);
     $tpl->assign($ret);
     $tpl->assign('act', 'update');
     $tpl->assign('unit_1', $unit_1);
     $tpl->assign('unit_2', $unit_2);
     $tpl->assign('unit_3', $unit_3);
     $tpl->assign('unit_4', $unit_4);
     $tpl->assign('sort_1', $sort_1);
     $tpl->assign('sort_2', $sort_2);
     $tpl->assign('role_1', $role_1);
     $tpl->assign('role_2', $role_2);
     //$tpl->assign('id', $id);
  }
  elseif ($act == 'update') 
  {
    $id          = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
    if (!$id) 
     {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=list';</script>";
        exit;
     }
     $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
     if(!$location_id)
     {
       echo "<script type='text/javascript'>window.alert('地区ID不能为空');location.href='rank_event.php?act=add';</script>";
       exit;
     }
      $location_id_root = trim($authority_list[0]['location_id']);
      if(strlen($location_id_root) >0)
      {
          $location_id_root = explode(',', $location_id_root);
          if(!in_array($location_id,$location_id_root))
          {
              echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=edit&id={$id}';</script>";
              exit;
          }
      }
    $rank_id     = $_INPUT['rank_id'] ? intval($_INPUT['rank_id']) : 0;
    if(!$rank_id)
    {
      echo "<script type='text/javascript'>window.alert('榜单ID不能为空');location.href='rank_event.php?act=edit&id={$id}';</script>";
      exit;
    }
    /*$rank_name   = $_INPUT['rank_name'] ? $_INPUT['rank_name'] : '';
    if(!$rank_name)
    {
      echo "<script type='text/javascript'>window.alert('名称不能为空');location.href='rank_event.php?act=edit&id={$id}';</script>";
      exit;
    }*/
    $unit        = $_INPUT['unit'] ? intval($_INPUT['unit']) : 0;
    $rank_desc   = $_INPUT['rank_desc'] ? $_INPUT['rank_desc'] : '';
    $app_sort    = $_INPUT['app_sort'] ? intval($_INPUT['app_sort']) : 0;
    $dmid     = $_INPUT['dmid'] ? intval($_INPUT['dmid']) : 0;
    $sort_order  = $_INPUT['sort_order'] ? intval($_INPUT['sort_order']) : 0;
    $role        = $_INPUT['role'] ? $_INPUT['role'] : '';
    $url         = $_INPUT['url'] ? $_INPUT['url'] : '';
    //$province    = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
     $data['rank_id']     = $rank_id;
     //$data['rank_name'] = $rank_name;
     $data['unit']        = $unit;
     $data['rank_desc']   = $rank_desc;
     $data['app_sort']    = $app_sort;
     $data['dmid']        = $dmid;
     $data['sort_order']  = $sort_order;
     $data['role']        = $role;
     $data['url']         = $url;
     $data['location_id'] = $location_id;
     $data_log['data_log'] = $rank_event_obj->get_serialize_rank_event_list();
     $info = $rank_event_obj->update_info($data, $id);
     if ($info) 
     {
       //加log
       $data_log['rank_event_id'] = $info;
       //print_r($data_log);exit;
       $rank_event_log_obj->add_info($data_log);
       echo "<script type='text/javascript'>window.alert('榜单修改成功');location.href='rank_event.php?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('榜单修改失败');location.href='rank_event.php?act=edit&id={$id}';</script>";
       exit;
  }
  //删除
  elseif ($act == 'del') 
  {
    $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
    if (!$id) 
     {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=list';</script>";
        exit;
     }
      //判断是否为rank 管理者 2015-5-21
      $ret = $rank_event_obj->get_rank_event_info($id);
      $location_id_root = trim($authority_list[0]['location_id']);
      if(strlen($location_id_root) >0)
      {
          $location_id_root = explode(',', $location_id_root);
          if(!in_array($ret['location_id'],$location_id_root))
          {
              echo "<script type='text/javascript'>window.alert('非法操作');location.href='rank_event.php?act=list';</script>";
              exit;
          }
      }
      //判断是否为rank 管理者

     $data_log['data_log'] = $rank_event_obj->get_serialize_rank_event_list();
     $info = $rank_event_obj->delete_info($id);
     if ($info) 
     {
       //加log
       $data_log['rank_event_id'] = $id;
       $rank_event_log_obj->add_info($data_log);
       echo "<script type='text/javascript'>window.alert('榜单删除成功');location.href='rank_event.php?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('榜单删除失败');location.href='rank_event.php?act=list';</script>";
       exit;
  }

  //获取所有rank
  elseif ($act == 'rank') 
  {
     $channel_id = $_INPUT['channel_id'] ? intval($_INPUT['channel_id']) : 0;
     if (empty($channel_id)) 
     {
       echo 0;
     }
     $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
     $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");//取榜单
     if ($rank_list) 
     {
       foreach ($rank_list as $key => $vo) 
       {
         $rank_list[$key]['rank_name'] = iconv("GB2312", "UTF-8" , $vo['rank_name']);
       }
     }
     $arr  = array
     (
           'msg' => 'success' ,
           //'prov_id' => $prov_id,
           'ret' => $rank_list
      );
     echo json_encode($arr);
     exit;
  }

  //测试使用
  elseif ($act == 'test') 
  {
    $location_id = '101029001';
    # code...
    $ret = $rank_event_obj->get_rank_event_by_location_id($location_id);
    print_r($ret);
    exit;
  }


  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>