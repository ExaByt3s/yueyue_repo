<?php
/**
 * ���ݵ���
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-05-04 10:37:31
 * @version 1
 */
 //include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
 include_once 'common.inc.php';
 //���볣�ú���
 include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
 include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
 include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
 ini_set('memory_limit', '256M');
 $cameraman_add_v2_obj  = POCO::singleton('pai_cameraman_add_v2_class');
 $user_obj = POCO::singleton('pai_user_class');
 
 //��Ӱʦ���ǩ��
 $cameraman_add_user_label = POCO::singleton('pai_cameraman_add_user_label_class');
 
 $tpl = new SmartTemplate("cameraman_search_v2_export.tpl.htm");
 
 $act       = trim($_INPUT['act']);
 //������ʽ
 $layout    = trim($_INPUT['layout']);
 $role      = trim($_INPUT['role']);
 
 $name      = trim($_INPUT['name']);
 $cellphone = trim($_INPUT['cellphone']);
 $user_id   = intval($_INPUT['user_id']);
 $start_num = intval($_INPUT['start_num']);
 $end_num   = intval($_INPUT['end_num']);
 //��������
 $sort      = trim($_INPUT['sort']);
 
 //����
 $where_str = '';
 //����ɸѡ
 if ($act == 'quick_search')
 {
 	if (strlen($name) >0)
 	{
 		if(strlen($where_str) >0) $where_str .= ' AND ';
 		$where_str .= "C.name like '%".mysql_escape_string($name)."%'";
 	}
 	if (strlen($cellphone) >0)
 	{
 		if(strlen($where_str)>0) $where_str .= ' AND ';
 		$where_str .= "P.cellphone = '".mysql_escape_string($cellphone)."'";
 	}
 	if($user_id >0)
 	{
 		if(strlen($where_str)>0) $where_str .= ' AND ';
 		$where_str .= "C.user_id = {$user_id}";
 	}
 	$order_sort = 'user_id DESC';
 	if(strlen($sort)>0)
 	{
 		if($sort == 'add_time_asc') $order_sort = 'C.add_time ASC,C.user_id DESC';
 		elseif($sort == 'add_time_desc') $order_sort = 'C.add_time DESC,C.user_id DESC';
 	}
    $limit = "0,50000";
    if($start_num >0 && $end_num >0)
    {
        $limit = "{$start_num},{$end_num}";
    }elseif($start_num <1 && $end_num >0 )
    {
        $limit = "0,{$end_num}";
    }
    elseif($start_num >0 && $end_num <1)
    {
        $limit = "{$start_num},99999999";
    }
 	$list = $cameraman_add_v2_obj->get_list(false, $where_str,$order_sort,$limit);

 }

 //������ʼ
 if($layout == 'txt')
 {
 	$user_str = implode(',', array_change_by_val($list, 'user_id'));
 	$filePath = "txt.txt";
 	file_put_contents($filePath, $user_str);
 	header("Content-type: application/octet-stream");
 	header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
 	header("Content-Length: ". filesize($filePath));
 	readfile($filePath);
 	exit;
 }
 elseif ($layout == 'url')
 {
 	$user_str = implode(',', array_change_by_val($list, 'user_id'));
 	$str = compressed($user_str);
 	$filePath = "url.txt";
 	file_put_contents($filePath, $str);
 	header("Content-type: application/octet-stream");
 	header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
 	header("Content-Length: ". filesize($filePath));
 	readfile($filePath);
 	exit;
 }
 elseif ($layout == 'excel')
 {
 	//�г���
 	if ($role == 'market')
 	{
 		//��Ӱʦ�ȼ�
 		$user_level_obj = POCO::singleton('pai_user_level_class');
 		$data = array();
 		foreach ($list as $key=>$val)
 		{
 			$data[$key]['user_id']  = $val['user_id'];
 			$data[$key]['nickname'] = $val['nickname'];
 			$data[$key]['cellphone'] = $val['cellphone'];
 			$data[$key]['level'] = $user_level_obj->get_user_level($val['user_id']);
 			$data[$key]['register_time'] = $val['register_time'];
 		}
 		$fileName = "�г���excel";
 		//$title    = "�г���excel";
 		$headArr = array('�û�ID','APP�ǳ�', '�ֻ�����', '�ȼ�', 'ע��ʱ��');
 		//getExcel($fileName,$title,$headArr,$data);
        Excel_v2::start($headArr,$data,$fileName);
 		exit;
 	}
 	//��Ӫ����
 	if($role == 'operate')
 	{
 		$data = array();
 		foreach ($list as $key=>$val)
 		{
 			$data[$key]['user_id']          = $val['user_id'];
 			$data[$key]['nickname']         = $val['nickname'];
 			$data[$key]['name']             = $val['name'];
 			$data[$key]['cellphone']        = $val['cellphone'];
 			$data[$key]['weixin_name']      = $val['weixin_name'];
 			$data[$key]['goods_style']      = str_replace('|', ',', $val['goods_style']);
 			$data[$key]['register_time']    = $val['register_time'];
 			$data[$key]['last_login_time']  = $val['last_login_time'];
 			$data[$key]['login_sum']  = $val['login_sum'];
 			$data[$key]['total_sum']  = $val['total_sum'];
 			$data[$key]['total_price']  = $val['total_price'];
 			$data[$key]['prev_month_price'] = $val['prev_month_price'];
 			$data[$key]['prev_month_num']   = $val['prev_month_num'];
 			$data[$key]['avg_month_price']  = $val['avg_month_price'];
 			//����ˮƽ
 			if($val['consumption_level']>=0 && $val['consumption_level']<=100 )  $data[$key]['consumption_name'] = '�ͼ�';
 			if($val['consumption_level']>=101 && $val['consumption_level']<=200 ) $data[$key]['consumption_name'] = '�ϵ�';
 			if($val['consumption_level']>=201 && $val['consumption_level']<=400 ) $data[$key]['consumption_name'] = '����';
 			if($val['consumption_level']>=401 && $val['consumption_level']<=600 ) $data[$key]['consumption_name'] = '�ϸ�';
 			if($val['consumption_level']>600) $data[$key]['consumption_name'] = '�߼�';
 			//��Ծ��
 			if($val['login_sum']==0) $data[$key]['login_name'] = '��Ĭ';
 			if($val['login_sum']>0 && $val['login_sum'] <= 5) $data[$key]['login_name'] = '��Ծ';
 			if($val['login_sum']>5) $data[$key]['login_name'] = '����';
 			
 			$data[$key]['photo_time'] = $val['photo_time'];
 			
 			//��ǩ
 			$data[$key]['label_name'] = $cameraman_add_user_label->get_info_by_user_id($val['user_id']);
 		}
 		$fileName = "��Ӫ��excel";
 		//$title    = "��Ӫ��excel";
 		$headArr = array('�û�ID','APP�ǳ�','����', '�ֻ�����', '΢��', '������','ע��ʱ��','����¼ʱ��','��¼����',' ���Ѵ���','�����ܽ��','���½��׶�','���½��״���','ƽ�����ѽ��','��������','��Ծ��','����ʱ��','��ǩ');
 		//getExcel($fileName,$title,$headArr,$data);
        Excel_v2::start($headArr,$data,$fileName);
 		exit;
 	}
 }

 $setParam = array();
 
 if(strlen($name) >0)
 {
 	$setParam['name'] = $name;
 }
 if (strlen($cellphone)>0)
 {
 	$setParam['cellphone'] = $cellphone;
 }
 if ($user_id > 0)
 {
 	$setParam['user_id'] = $user_id;
 }
 if (trim($sort) >0)
 {
 	$setParam['sort'] = $sort;
 }
 
 $tpl->assign($setParam);
 $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
 $tpl->output(); 

?>