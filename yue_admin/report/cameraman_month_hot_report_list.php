<?php

 /**
  * @desc ��Ӱʦ30������
  * @authors xiao xiao (xiaojm@yueus.com)
  * @date    2015��4��17��
  * @version 1
  */
  include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
  //���ú���
  include_once("/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php");
  ini_set('memory_limit', '256M');
  include('common.inc.php');
  
  
  $cameraman_month_hot_obj = POCO::singleton('pai_cameraman_month_hot_report_class');
  $user_obj                = POCO::singleton('pai_user_class');
  
  $tpl = new SmartTemplate("cameraman_month_hot_report_list.tpl.htm");
  
  $date = $_INPUT['date'] ? trim($_INPUT['date']) : date('Y-m-d',time()-24*3600);
  $location_id  = intval($_INPUT['location_id']) ? intval($_INPUT['location_id']) : 101029001 ;

  $act  = trim($_INPUT['act']);
  
  $setParam = array();
  $where_str = '';
  
  if(strlen($date) >0)
  {
  	$setParam['date'] = $date;
  }

  //����
  if(strlen($location_id)>0)
  {
  	/* if(strlen($where_str) >0 ) $where_str .= ' AND ';
  	$where_str .= "location_id = {$location_id}"; */
  	$setParam['location_id'] = $location_id;
  }
  
  $list = $cameraman_month_hot_obj->get_month_hot_list($date,false,$location_id,$where_str,'score DESC,user_id DESC','0,30','*');
  
  if(!is_array($list)) $list = array();
  
  $sql_tmp_str = '';
  foreach ($list as $key=>$val)
  {
  	if($key != 0) $sql_tmp_str .= ',';
  	$sql_tmp_str .= $val['user_id'];
  }
  
  if(strlen($sql_tmp_str) >0)
  {
  	 $sql_in_str = "user_id IN ({$sql_tmp_str})";
  	 $user_ret = $user_obj->get_user_list(false, $sql_in_str, 'user_id DESC', "0,30",'user_id,nickname');
 	 if(is_array($user_ret)) $list = combine_arr($list, $user_ret, 'user_id');
  }
  
  if(!is_array($list)) $list = array();
  
  //��������
  if($act == 'export')
  {
  	$data = array();
    foreach ($list as $key=>$vo)
    {
       $data[$key]['user_id']        = $vo['user_id'];
       $data[$key]['nickname']       = $vo['nickname'];
       $data[$key]['score']          = $vo['score'];
    }
  	 $fileName = '��Ӱʦ30������';
  	 $title    = '��Ӱʦ30������';
  	 $headArr  = array("��ӰʦID","��Ӱʦ�ǳ�","ƽ����");
  	 getExcel($fileName,$title,$headArr,$data);
  	 exit;
  }
  
  //print_r($list);exit;
  $tpl->assign($setParam);
  $tpl->assign('list', $list);
  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
  
  $tpl->output();
  
  ?>