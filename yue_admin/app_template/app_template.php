<?php 

/* 
 *app模板控制器
 *xiao xiao
 * 2014-2-11
*/
  include('common.inc.php');
  include('include/common_function.php');
  $app_template_obj = POCO::singleton('pai_app_template_class');
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
     $tpl = new SmartTemplate("app_template_list.tpl.htm");
     $start_add_time = $_INPUT['start_add_time'] ? $_INPUT['start_add_time'] : '';
     $end_add_time   = $_INPUT['end_add_time'] ? $_INPUT['end_add_time'] : '';
     $add_id         = $_INPUT['add_id'] ? intval($_INPUT['add_id']) : '';
     $type           = $_INPUT['type'] ? $_INPUT['type'] : '';
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
     if ($type) 
     {
       $where_str .= " AND type = '{$type}'";
     }
     $total_count = $app_template_obj->get_app_template_list(true, $where_str);
     $page_obj->setvar (
        array
        (
          'start_add_time' => $start_add_time,
          'end_add_time'   => $end_add_time,
          'add_id'         => $add_id,
          'type'           => $type
          )
      );
     $page_obj->set ( $show_count, $total_count );
     $list = $app_template_obj->get_app_template_list(false , $where_str, 'id DESC', $page_obj->limit(), '*');
     if ($list) 
     {
       foreach ($list as $key => $vo) 
       {
          //$list[$key]['city']        = get_poco_location_name_by_location_id($vo['location_id']);
          $list[$key]['add_time']    = date('Y-m-d H:i:s', $vo['add_time']);
          //$list[$key]['nick_name']   = get_user_nickname_by_user_id($vo['add_id']);
       }
     }
     $tpl->assign('list', $list);
     $tpl->assign('start_add_time', $start_add_time);
     $tpl->assign('end_add_time', $end_add_time);
     $tpl->assign('add_id', $add_id);
     $tpl->assign('type', $type);
     $tpl->assign('total_count', $total_count);
     $tpl->assign ( "page", $page_obj->output ( 1 ) );
  }
  //添加
  elseif ($act == 'add') 
  {
    $tpl = new SmartTemplate("app_template_edit.tpl.htm");
    $tpl->assign('act', 'insert');
  }
  //插入
  elseif ($act == 'insert') 
  {
     $tpl_name = $_INPUT['tpl_name'] ? $_INPUT['tpl_name'] : '';
     if(!$tpl_name)
     {
       echo "<script type='text/javascript'>window.alert('名称不能为空');history.back();</script>";
       exit;
     }
     $type    = $_INPUT['type'] ? $_INPUT['type'] : '';
     $img_url = $_INPUT['img_url'] ? $_INPUT['img_url'] :'';
     $data['tpl_name'] = $tpl_name;
     $data['type']     = $type;
     $data['img_url']  = $img_url;
     $data['add_id']   = $yue_login_id;
     $data['add_time'] = time();
     $info = $app_template_obj->add_info($data);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('添加模板成功');location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('添加模板失败');location.href='?act=add';</script>";
       exit;
  }
  //修改
  elseif ($act == 'edit') 
  {
     $tpl = new SmartTemplate("app_template_edit.tpl.htm");
     $id = $_INPUT['id'] ? intval($_INPUT['id']) : 0;
     if (!$id) 
     {
        echo "<script type='text/javascript'>window.alert('非法操作');location.href='?act=list';</script>";
        exit;
     }
     $ret = $app_template_obj->get_app_template_info($id);
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
     $tpl_name = $_INPUT['tpl_name'] ? $_INPUT['tpl_name'] : '';
     if(!$tpl_name)
     {
       echo "<script type='text/javascript'>window.alert('名称不能为空');history.back();</script>";
       exit;
     }
     $type    = $_INPUT['type'] ? $_INPUT['type'] : '';
     $img_url = $_INPUT['img_url'] ? $_INPUT['img_url'] :'';
     $data['tpl_name'] = $tpl_name;
     $data['img_url']  = $img_url;
     $data['type']     = $type;
     $info = $app_template_obj->update_info($data, $id);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('模板修改成功');location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('模板修改失败');history.back();</script>";
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
     $info = $app_template_obj->delete_info($id);
     if ($info) 
     {
       echo "<script type='text/javascript'>window.alert('模板删除成功');location.href='?act=list';</script>";
       exit;
     }
     echo "<script type='text/javascript'>window.alert('模板删除失败');location.href='?act=list';</script>";
       exit;
  }
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  $tpl->output();



 ?>