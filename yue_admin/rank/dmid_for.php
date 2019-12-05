<?php 

/* 
 *dmid控制器
 *xiao xiao
 * 2014-1-28
*/
  include('common.inc.php');
  include('include/common_function.php');
  include_once 'include/locate_file.php';
  include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
  $dmid_for_obj = POCO::singleton('pai_dmid_for_class');
  $page_obj = new show_page ();
  $show_count = 20;
  $act = $_INPUT['act'] ? $_INPUT['act'] : 'list';
  $selected = "selected='true'";
  //列表
  if ($act == 'list') 
  {
     $tpl = new SmartTemplate("dmid_for_list.tpl.htm");
     $start_add_time = $_INPUT['start_add_time'] ? $_INPUT['start_add_time'] : '';
     $end_add_time   = $_INPUT['end_add_time'] ? $_INPUT['end_add_time'] : '';
     $add_id         = $_INPUT['add_id'] ? intval($_INPUT['add_id']) : '';
     $province       = $_INPUT['province'] ? intval($_INPUT['province']) : 0;
     $location_id    = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
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
      $total_count = $dmid_for_obj->get_dmid_for_list(true, $where_str);
     $page_obj->setvar (
        array
        (
          'start_add_time' => $start_add_time,
          'end_add_time'   => $end_add_time,
          'add_id'         => $add_id,
          'province'       => $province,
          'location_id'    => $location_id
          )
      );
     $page_obj->set ( $show_count, $total_count );
     $list = $dmid_for_obj->get_dmid_for_list(false , $where_str, 'dmid DESC', $page_obj->limit(), '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
          $list[$key]['city']        = get_poco_location_name_by_location_id($vo['location_id']);
          $list[$key]['add_time']    = date('Y-m-d H:i:s', $vo['add_time']);
          $list[$key]['start_time']  = date('Y-m-d', $vo['start_time']);
          $list[$key]['end_time']    = date('Y-m-d', $vo['end_time']);
          $list[$key]['nick_name']   = get_user_nickname_by_user_id($vo['add_id']);
          //$list[$key]['rank_name']   = $rank_info['rank_name'];
          $list[$key]['desc']        = poco_cutstr($vo['dmid_desc'], 20, '....');
          
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
     $tpl->assign($setParam);
     $tpl->assign('province_list', $province_list);
     $tpl->assign('city_list', $city_list);
     $tpl->assign('list', $list);
     $tpl->assign('start_add_time', $start_add_time);
     $tpl->assign('end_add_time', $end_add_time);
     $tpl->assign('add_id', $add_id);
     $tpl->assign('total_count', $total_count);
     $tpl->assign ( "page", $page_obj->output ( 1 ) );
  }
  //添加
  elseif ($act == 'add') 
  {
    $tpl = new SmartTemplate("dmid_for_edit.tpl.htm");
    $province_list = change_assoc_arr($arr_locate_a);
    $tpl->assign('province_list', $province_list);
    $tpl->assign('act', 'insert');
  }
  //插入
  elseif ($act == 'insert') 
  {
     $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
     if(!$location_id)
     {
       echo "<script type='text/javascript'>window.alert('地区ID不能为空');location.href='?act=add';</script>";
       exit;
     }
      $location_id_root = trim($authority_list[0]['location_id']);
      if(strlen($location_id_root) >0)
      {
          $location_id_root = explode(',', $location_id_root);
          if(!in_array($location_id,$location_id_root))
          {
              //js_pop_msg('非法操作');
              echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=add';</script>";
              exit;
          }
      }
     $dmid_name   = $_INPUT['dmid_name'] ? $_INPUT['dmid_name'] : '';
     if ($dmid_name == '') 
     {
       echo "<script type='text/javascript'>window.alert('名称不能为空');location.href='?act=add';</script>";
     }
     $start_time  = $_INPUT['start_time'] ? $_INPUT['start_time'] : '';
     if ($start_time == '') 
     {
       echo "<script type='text/javascript'>window.alert('开始时间不能为空');location.href='?act=add';</script>";
     }
     $end_time    = $_INPUT['end_time'] ? $_INPUT['end_time'] : '';
     if ($end_time == '') 
     {
       echo "<script type='text/javascript'>window.alert('结束时间不能为空');location.href='?act=add';</script>";
     }
     $dmid_desc   = $_INPUT['dmid_desc'] ? $_INPUT['dmid_desc'] : '';
     $data['location_id'] = $location_id;
     $data['dmid_name']   = $dmid_name;
     $data['start_time']  = strtotime($start_time);
     $data['end_time']    = strtotime($end_time);
     $data['dmid_desc']   = $dmid_desc;
     $data['add_id']      = $yue_login_id;
     $data['add_time']    = time();
     $info = $dmid_for_obj->add_info($data);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('添加促销成功,促销ID为'+$info);location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('添加促销失败');location.href='?act=add';</script>";
       exit;
  }
  //修改
  elseif ($act == 'edit') 
  {
     $tpl = new SmartTemplate("dmid_for_edit.tpl.htm");
     $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
     if (!$id) 
     {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=list';</script>";
        exit;
     }
     $ret = $dmid_for_obj->get_dmid_for_info($id);
     $province_list = change_assoc_arr($arr_locate_a);
     if ($ret) 
     {
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
        $ret['start_time'] = date('Y-m-d' , $ret['start_time']);
        $ret['end_time']   = date('Y-m-d' , $ret['end_time']);
     }
     $tpl->assign('province_list', $province_list);
     $tpl->assign('city_list', $city_list);
     $tpl->assign($ret);
     $tpl->assign('act', 'update');
     //$tpl->assign('id', $id);
  }
  elseif ($act == 'update') 
  {
    $id          = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
    if (!$id) 
    {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=list';</script>";
        exit;
    }
    $location_id = $_INPUT['location_id'] ? intval($_INPUT['location_id']) : 0;
    if(!$location_id)
    {
      echo "<script type='text/javascript'>window.alert('地区ID不能为空');location.href='?act=add';</script>";
      exit;
    }
    $location_id_root = trim($authority_list[0]['location_id']);
    if(strlen($location_id_root) >0)
    {
        $location_id_root = explode(',', $location_id_root);
        if(!in_array($location_id,$location_id_root))
        {
            echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=add';</script>";
            exit;
        }
    }
     $dmid_name   = $_INPUT['dmid_name'] ? $_INPUT['dmid_name'] : '';
     if ($dmid_name == '') 
     {
       echo "<script type='text/javascript'>window.alert('名称不能为空');location.href='?act=add';</script>";
     }
     $start_time  = $_INPUT['start_time'] ? $_INPUT['start_time'] : '';
     if ($start_time == '') 
     {
       echo "<script type='text/javascript'>window.alert('开始时间不能为空');location.href='?act=add';</script>";
     }
     $end_time    = $_INPUT['end_time'] ? $_INPUT['end_time'] : '';
     if ($end_time == '') 
     {
       echo "<script type='text/javascript'>window.alert('结束时间不能为空');location.href='?act=add';</script>";
     }
    
     $data['location_id'] = $location_id;
     $data['dmid_name']   = $dmid_name;
     $data['start_time']  = strtotime($start_time);
     $data['end_time']    = strtotime($end_time);
     $data['dmid_desc']   = $dmid_desc;
     $info = $dmid_for_obj->update_info($data, $id);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('活动修改成功');location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('活动修改失败');location.href='?act=edit&id={$id}';</script>";
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
     $ret = $dmid_for_obj->get_dmid_for_info($id);

     //删除检验
     $location_id_root = trim($authority_list[0]['location_id']);
     if(strlen($location_id_root) >0)
     {
         $location_id_root = explode(',', $location_id_root);
         if(!in_array($ret['location_id'],$location_id_root))
         {
             echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=list';</script>";
             exit;
         }
     }


     $info = $dmid_for_obj->delete_info($id);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('活动删除成功');location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('活动删除失败');location.href='?act=list';</script>";
       exit;
  }
  //测试促销ID是否存在
  elseif ($act == 'check') 
  {
      $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
      if (!$id) 
      {
        echo fals;
        exit;
      }
      $info =  $dmid_for_obj->get_dmid_for_info($id);
      if ($info) 
      {
         echo "success";
         exit;
      }
      echo "fail";
      exit;
  }
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>