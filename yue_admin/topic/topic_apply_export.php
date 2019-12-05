<?php
/**
 * ���ݵ���
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-03 10:37:31
 * @version 1
 */
include_once '/disk/data/htdocs232/poco/pai/yue_admin/audit/include/Classes/PHPExcel.php';
include_once 'common.inc.php';
include_once 'top.php';
//���볣�ú���
include_once '/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php';
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
$ids = $_INPUT['ids'] ? $_INPUT['ids'] : 0;
$act = $_INPUT['act'] ? $_INPUT['act'] : 'show';
$tpl = new SmartTemplate("topic_apply_export.tpl.htm");
$topic_enroll_obj  = POCO::singleton('pai_topic_enroll_class');
$model_style_v2_obj  = POCO::singleton('pai_model_style_v2_class');
$order_org_obj  = POCO::singleton('pai_order_org_class');
//ģ�ؿ���
$model_card_obj = POCO::singleton('pai_model_card_class');
//�û���
$user_obj      = POCO::singleton('pai_user_class');
if(empty($ids))
{
	echo "<script type='text/javascript'>window.alert('�Ƿ�����');history.back();</script>";
	exit;
}
//��ʾ����
if($act == 'show')
{
	$tpl->assign('ids', $ids);
	//$tpl->assign('ids', $ids);
}
else
{
	$where_str = "id IN($ids)";
    $total_count = $topic_enroll_obj->get_topic_enroll_list(true,$where_str);
    $user_arr_id = $topic_enroll_obj->get_topic_enroll_list(false, $where_str, $order_by = 'sort_status ASC,id DESC', "0,{$toal_count}", "user_id");
    //�����ı�
	if ($act == 'txt') 
	{
	    $user_str = implode(',', array_change_by_val($user_arr_id, 'user_id'));
	    //echo $user_str;exit;
	    $filePath = "txt.txt";
	    file_put_contents($filePath, $user_str);
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header("Content-Length: ". filesize($filePath));
        readfile($filePath);
        exit;
	}
	//���Ӹ�ʽ
	if ($act == 'url') 
	{
		$user_str = implode(',', array_change_by_val($user_arr_id, 'user_id'));
		$str = compressed($user_str);
		$filePath = "url.txt";
	    file_put_contents($filePath, $str);
	    header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header("Content-Length: ". filesize($filePath));
        readfile($filePath);
	}
	//excel ��ʽ
	if ($act == 'excel') 
	{
		$data = array();
		foreach ($user_arr_id as $key => $vo) 
		{
			/*$style_data = $model_style_v2_obj->get_model_style_by_user_id($vo['user_id']);
			print_r($style_data);*/
			$user_info = $user_obj->get_user_info($vo['user_id']);
			$data[$key]['user_id'] = $vo['user_id'];
			$data[$key]['location_name'] = get_poco_location_name_by_location_id($user_info['location_id']);
			$data[$key]['nickname'] = $user_info['nickname'];
			$ret = $model_card_obj->get_model_card_info($vo['user_id']);
			$style_data = $model_style_v2_obj->get_model_style_combo($vo['user_id']);
			$style = "";
			foreach ($style_data['main'] as $main_val) 
			{
				$style .= $main_val['style']."&nbsp;".$main_val['price']."Ԫ/".$main_val['hour']."Сʱ";
			}
			foreach ($style_data['other'] as $other_val) 
			{
				$style .= $other_val['style']."&nbsp;".$other_val['price']."Ԫ/".$other_val['hour']."Сʱ";
			}
			$data[$key]['style']  = $style;
			$data[$key]['cup']    = $ret['chest_inch'].$ret['cup'];
			$data[$key]['height'] = $ret['height'];
			$data[$key]['cellphone'] = $user_obj->get_phone_by_user_id($vo['user_id']);
			$data[$key]['yue_count'] = $order_org_obj->get_order_count_by_user_id(true, $vo['user_id']);
		    $data[$key]['yue_price'] = $order_org_obj->get_sum_price_by_user_id(true, $vo['user_id']);
		}
		$fileName = "����excel";
        $title    = "����excel";
        $headArr = array('�û�ID','����','�ǳ�', '�û����/�۸�', '��Χ', '���', '�绰', 'Լ�Ĵ���','Լ���ܽ��');
        getExcel($fileName,$title,$headArr,$data);
	}
	exit;
}
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();