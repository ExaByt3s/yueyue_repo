<?php
/**
 * �ύ����ҳ
 * @author Bowie
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
/**
 * common
 */
require_once("cms_common.inc.php");

$act = $_REQUEST["act"];
$cms_db_obj = POCO::singleton ( 'cms_db_class' );

switch ($act) 
{
	case "channel_add":
		
		/**
		 * ���Ƶ��
		 */
		$channel_name 	= trim($_INPUT["channel_name"]);
		$remark 		= $_INPUT["remark"];
		
		if(strlen($channel_name)==0) js_pop_msg("������Ƶ�����ƣ�");
		if(strlen($remark) > 255) js_pop_msg("��ע���ȴ���255���ַ�");
		
		$insert_data = array(
		"channel_name" => $channel_name,
		"remark" => $remark,
		);
		
		$channel_id = $cms_db_obj->insert_cms("channel_tbl", $insert_data);
		
		if ($channel_id > 0) 	js_pop_msg("���Ƶ�����", true);
		else 					js_pop_msg("���ʧ�ܣ����Ժ�����", false);
		
		break;
		
	case "channel_update":
		
		/**
		 * �޸�Ƶ��
		 */
		$channel_id		= $_INPUT["channel_id"]*1;
		$channel_name 	= trim($_INPUT["channel_name"]);
		$remark 		= $_INPUT["remark"];
		
		if($channel_id*1 < 1) js_pop_msg("��������channel_id=0");
		if(strlen($channel_name)==0) js_pop_msg("������Ƶ�����ƣ�");
		if(strlen($remark) > 255) js_pop_msg("��ע���ȴ���255���ַ�");
		
		$update_data = array(
		"channel_name" => $channel_name,
		"remark" => $remark,
		);
		
		$affected_rows = $cms_db_obj->update_cms("channel_tbl", "channel_id={$channel_id}", $update_data);
		
		if ($affected_rows > 0) js_pop_msg("�޸�Ƶ�����", true);
		else 					js_pop_msg("��ȷ���Ѷ�ԭ���ݽ����޸�", false);
	
		break;		
		
	case "rank_add":
		
		/**
		 * ��Ӱ�
		 */
		$channel_id	= $_INPUT["channel_id"]*1;
		$rank_name 	= trim($_INPUT["rank_name"]);
		$rank_url	= trim($_INPUT["rank_url"]);
		$remark 	= $_INPUT["remark"];
		$img_size	= $_INPUT["img_size"];
		$sort_order	= $_INPUT["sort_order"] * 1;
		
		if($cms_db_obj->get_cms_count("channel_tbl", "channel_id={$channel_id}") < 1) js_pop_msg("��ѡ�������Ƶ����");
		if(strlen($rank_name)==0) js_pop_msg("����������ƣ�");
		if(strlen($remark) > 255) js_pop_msg("��ע���ȴ���255���ַ�");
		
		$insert_data = array(
		"channel_id" => $channel_id,
		"rank_name"  => $rank_name,
		"rank_url"	 => $rank_url,
		"remark" 	 => $remark,
		"img_size" 	 => $img_size,
		"post_date"  => time(),
		"sort_order" => $sort_order,
		);
		
		$rank_id = $cms_db_obj->insert_cms("rank_tbl", $insert_data);
		
		if ($rank_id > 0) 	js_pop_msg("��Ӱ����", true);
		else 				js_pop_msg("���ʧ�ܣ����Ժ�����", false);		
		
		break;
		
	case "rank_update":
		
		/**
		 * �޸İ�
		 */
		$rank_id	= $_INPUT["rank_id"]*1;
		$channel_id	= $_INPUT["channel_id"]*1;
		$rank_name 	= trim($_INPUT["rank_name"]);
		$rank_url	= trim($_INPUT["rank_url"]);
		$remark 	= $_INPUT["remark"];
		$img_size	= $_INPUT["img_size"];
		$sort_order	= $_INPUT["sort_order"] * 1;
		
		if($rank_id < 1)	js_pop_msg("��������rank_id=0��");
		if($cms_db_obj->get_cms_count("channel_tbl", "channel_id={$channel_id}") < 1) js_pop_msg("��ѡ�������Ƶ����");
		if(strlen($rank_name)==0) js_pop_msg("����������ƣ�");
		if(strlen($remark) > 255) js_pop_msg("��ע���ȴ���255���ַ�");
		
		$update_data = array(
		"channel_id" => $channel_id,
		"rank_name"  => $rank_name,
		"rank_url"	 => $rank_url,
		"remark" 	 => $remark,
		"img_size" 	 => $img_size,
		"sort_order" => $sort_order,
		);
		
		$affected_rows = $cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", $update_data);
		
		if ($affected_rows > 0)	js_pop_msg("�޸İ����", true);
		else 					js_pop_msg("��ȷ���Ѷ�ԭ���ݽ����޸�", false);		
		
		break;
	case "rank_del":
		/* ɾ���� */
		$rank_id = $_INPUT['rank_id'] * 1;
		$channel_id = $_INPUT['channel_id'] * 1;

		if ($rank_id > 0)
		{
			/* ɾ��������ص�$rank_id�� */
			$cms_db_obj->delete_cms("rank_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("issue_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl_freeze", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl_last_issue", "rank_id={$rank_id}");
		}
		echo "<script>alert('ɾ���ɹ���');window.location.href='cms_rank_list.php?channel_id={$channel_id}';</script>";
		break;
	case "rank_edit_passel_sort_order":
		/* �����޸İ����� */
		$rank_id_arr    = array();
		$sort_order_arr = array();
		$channel_id     = $_INPUT['channel_id'];
		$rank_id_arr    = $_INPUT['rank_id_arr'];
		$sort_order_arr = $_INPUT['sort_order_arr'];

		$rank_count = count($rank_id_arr);
		for ($i = 0; $i < $rank_count; $i++)
		{
			$rank_id     = $rank_id_arr[$i] * 1;
			$update_data = array('sort_order' => $sort_order_arr[$i] * 1);
			$rank_id > 0 && $cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", $update_data);
		}
		$result = ($rank_count > 0 && $rank_id > 0) ? '�޸ĳɹ���' : '�޸�ʧ�ܣ�';
		echo "<script>alert('{$result}');window.location.href='cms_rank_list.php?channel_id={$channel_id}';</script>";
		break;
	case "rank_edit_place_number":
		/* �����޸�$rank_id������ */
		$log_id_arr = array();
		$log_id_arr = $_INPUT['log_id_arr'];
		$rank_id    = $_INPUT['rank_id'] * 1;
		$issue_id   = $_INPUT['issue_id'] * 1;
		$freeze     = $_INPUT['freeze'] * 1;
		$log_count  = 0;
		$issue_id > 0 && $rank_id > 0 && $log_count = count($log_id_arr);
		for ($i = 0; $i < $log_count; $i++)
		{
			$log_id = $log_id_arr[$i];
			if ($log_id > 0)
			{
				$update_data = array('place_number'=>$_INPUT['place_number_'.$log_id_arr[$i]] * 1);
				$recore_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";
				/* ���¼�¼ */
				$affected_rows = $cms_db_obj->update_cms($recore_tbl_name, "log_id={$log_id} AND issue_id={$issue_id}", $update_data);
				if ($affected_rows && false==$freeze) 
				{
					cms_system_class::record_data_last_issue_sync_by_log_id($log_id, $issue_id);//�����ڰ�����ͬ��
				}
			}
		}
		$result = $log_id > 0 ? '�޸ĳɹ���' : '�޸�ʧ�ܣ�';
		echo "<script>alert('{$result}');window.location.href='cms_issue_list.php?rank_id={$rank_id}';</script>";
		break;

	case "record_import":
		
		/**
		 * ���ݵ���
		 */
		$issue_id 				= $_INPUT["issue_id"]*1;
		
		$b_import_arr			= $_INPUT["b_import"];
		$place_number_arr	 	= $_INPUT["place_number"];
		$user_id_arr	 		= $_INPUT["user_id"];
		$title_arr		 		= $_INPUT["title"];
		$link_url_arr	 		= $_INPUT["link_url"];
		$img_url_arr	 		= $_INPUT["img_url"];
		$content_arr		 	= $_INPUT["content"];
		$remark_arr		 		= $_INPUT["remark"];
		
		if ($issue_id < 1) js_pop_msg("��������issue_id={$issue_id}");
		
		$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "rank_id,freeze");	
		$rank_id = $issue_info["rank_id"]*1;
		$record_tbl_name = ($issue_info["freeze"]) ? "record_tbl_freeze" : "record_tbl";//�����������ŵ���ͬ�ı�
		
		$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}", "channel_id");
		$channel_id = $rank_info["channel_id"]*1;
		
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );

		$add_record_count = 0;
		$exists_record_count = 0;
		$inserted_user_arr = array();
		
		
		foreach ($b_import_arr as $key=>$b_import)
		{
			if ($b_import /*&& !in_array($user_id_arr[$key], $inserted_user_arr)*/) 
			{
				$user_id = $user_id_arr[$key]*1;
				
				/**
				 * ����û�
				 */
				$user_info = $pai_user_obj->get_user_info($user_id);

				$insert_data = array(
				"issue_id"		=> $issue_id,
				"rank_id"		=> $rank_id,
				"channel_id"	=> $channel_id,
				"place_number"	=> $place_number_arr[$key]*1,
				"user_id"		=> $user_id,
				"user_name"		=> $user_info["nickname"],
				"sex"			=> $user_info["sex"],
				"location_id"	=> $user_info["location_id"],
				"title"			=> (trim($title_arr[$key])) ? trim($title_arr[$key]) : $user_info["nickname"],
				"img_url"		=> (trim($img_url_arr[$key])) ? html_entity_decode(trim($img_url_arr[$key])) : $user_icon_obj->get_user_icon($user_id, 86),
				"link_url"		=> (trim($link_url_arr[$key])) ? html_entity_decode(trim($link_url_arr[$key])) : "",
				"content"		=> str_replace("<br rel=auto>","<br>",$content_arr[$key]),
				"remark"		=> str_replace("<br rel=auto>","<br>",$remark_arr[$key]),
				"admin_user_id" => $yue_login_id,
				"post_date"		=> time()
				);
				
				$cms_db_obj->insert_cms($record_tbl_name, $insert_data);
				$add_record_count++;

			}
		}
		
		$record_count = $cms_db_obj->get_cms_count($record_tbl_name, "issue_id={$issue_id}");
		$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","record_count={$record_count}");//���¼�¼����
		cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);//ͬ������һ�ڰ�����
		
		js_pop_msg("������ɣ��ɹ������¼ {$add_record_count} ��"/*�����Ѵ��ڼ�¼ {$exists_record_count} ��"*/,
				   false,
				   "./cms_issue_list.php?rank_id={$rank_id}&issue_id={$issue_id}");
		break;	
				
	default:
		break;
}

?>