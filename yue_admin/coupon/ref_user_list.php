<?php

/**
 *���͸��û��Ż�ȯ����
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-27 15:56:29
 * @version 1
 */

include_once ('common.inc.php');
//��̨���ú���
include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
//�Ż�ȯ��
$coupon_obj = POCO::singleton('pai_coupon_class');
//��ҳ��
$page_obj   = new show_page();
$show_total = 30;
$tpl        = new SmartTemplate('ref_user_list.tpl.htm');

//���ղ���
$act      = trim($_INPUT['act']);
$user_id  = $_INPUT['user_id'] ? intval($_INPUT['user_id']) : 0;
$add_time = trim($_INPUT['add_time']);
$batch_id = $_INPUT['batch_id'] ? intval($_INPUT['batch_id']) : 0;
//�Ƿ�ʹ��
$is_used  = trim($_INPUT['is_used']);
//����
$where_str = '';
$setParam  = array();
if($user_id)
{
	$setParam['user_id'] = $user_id;
}
if($batch_id)
{
   $setParam['batch_id'] = $batch_id;
}
if ($add_time) 
{
   if(strlen($where_str) > 0) $where_str .= " AND ";
   $where_str .= "FROM_UNIXTIME(add_time,'%Y-%m-%d') = '{$add_time}'";
   $setParam['add_time'] = $add_time;
}
//�Ƿ�ʹ���ж�
if($is_used != 'all' )
{
	$is_used = (int)$is_used;
	if(strlen($where_str) > 0) $where_str .= " AND ";
   $time = time();
   //��ʹ��
   if ($is_used == 1) 
   {
      $where_str .= "is_used = {$is_used}";
   }
   else
   {
      $where_str .= "is_used = 0";
      //����
      if ($is_used == 2) $where_str .= " AND end_time < {$time}";
      //δʹ��
      if($is_used == 0)  $where_str .= " AND end_time >= {$time}";
   }
}
$setParam['is_used'] = $is_used;
//echo $where_str;
$total_count = $coupon_obj->get_ref_user_list($user_id, $batch_id, true, $where_str);
$page_obj->setvar($setParam);
$page_obj->set($show_total, $total_count);
$list        = $coupon_obj->get_ref_user_list($user_id, $batch_id,false, $where_str, 'batch_id DESC,id DESC' , $page_obj->limit());
//print_r($list);exit;
if(!is_array($list)) $list = array();
$where_in_str = '';

foreach($list as $key=> $vo)
{
   if($vo['is_used'] == 1)
   {
      $list[$key]['status'] = '��ʹ��';
   }
   elseif($vo['is_used'] == 0)
   {
      if($vo['end_time'] < time())
      {
         $list[$key]['status'] = '�ѹ���';
      }
      else
      {
         $list[$key]['status'] = 'δʹ��';
      }
   }
   if($key != 0)
   {
   	 $where_in_str .= ',';
   }
   $where_in_str .= "{$vo['batch_id']}";
}

//��ȡ��������
if(strlen($where_in_str) > 0) 
{
   $where_batch_str = "batch_id IN ({$where_in_str})";
   //echo $where_batch_str;
   $batch_list = $coupon_obj->get_batch_list(0, false , $where_batch_str,'cate_id DESC,batch_id DESC', '0,30', 'batch_id,batch_name,coupon_face_value,scope_module_type_name');
   //�ϲ�������ά����
   if(is_array($batch_list)) $list = combine_arr2($list, $batch_list, 'batch_id');
}
//print_r($list);
if(!is_array($list)) $list = array();

foreach ($list as $key => $vo) 
{
   $list[$key]['add_time']   = date('Y-m-d', $vo['add_time']);
   $list[$key]['start_time'] = date('Y-m-d', $vo['start_time']);
   $list[$key]['end_time']   = date('Y-m-d', $vo['end_time']);
}

$tpl->assign($setParam);
$tpl->assign('list', $list);
//���ò���
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>