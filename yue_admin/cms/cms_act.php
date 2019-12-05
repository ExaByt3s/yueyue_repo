<?php
/**
 * 提交操作页
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
		 * 添加频道
		 */
		$channel_name 	= trim($_INPUT["channel_name"]);
		$remark 		= $_INPUT["remark"];
		
		if(strlen($channel_name)==0) js_pop_msg("请输入频道名称！");
		if(strlen($remark) > 255) js_pop_msg("备注长度大于255个字符");
		
		$insert_data = array(
		"channel_name" => $channel_name,
		"remark" => $remark,
		);
		
		$channel_id = $cms_db_obj->insert_cms("channel_tbl", $insert_data);
		
		if ($channel_id > 0) 	js_pop_msg("添加频道完成", true);
		else 					js_pop_msg("添加失败，请稍候再试", false);
		
		break;
		
	case "channel_update":
		
		/**
		 * 修改频道
		 */
		$channel_id		= $_INPUT["channel_id"]*1;
		$channel_name 	= trim($_INPUT["channel_name"]);
		$remark 		= $_INPUT["remark"];
		
		if($channel_id*1 < 1) js_pop_msg("参数错误：channel_id=0");
		if(strlen($channel_name)==0) js_pop_msg("请输入频道名称！");
		if(strlen($remark) > 255) js_pop_msg("备注长度大于255个字符");
		
		$update_data = array(
		"channel_name" => $channel_name,
		"remark" => $remark,
		);
		
		$affected_rows = $cms_db_obj->update_cms("channel_tbl", "channel_id={$channel_id}", $update_data);
		
		if ($affected_rows > 0) js_pop_msg("修改频道完成", true);
		else 					js_pop_msg("请确定已对原内容进行修改", false);
	
		break;		
		
	case "rank_add":
		
		/**
		 * 添加榜单
		 */
		$channel_id	= $_INPUT["channel_id"]*1;
		$rank_name 	= trim($_INPUT["rank_name"]);
		$rank_url	= trim($_INPUT["rank_url"]);
		$remark 	= $_INPUT["remark"];
		$img_size	= $_INPUT["img_size"];
		$sort_order	= $_INPUT["sort_order"] * 1;
		
		if($cms_db_obj->get_cms_count("channel_tbl", "channel_id={$channel_id}") < 1) js_pop_msg("请选择榜单所属频道！");
		if(strlen($rank_name)==0) js_pop_msg("请输入榜单名称！");
		if(strlen($remark) > 255) js_pop_msg("备注长度大于255个字符");
		
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
		
		if ($rank_id > 0) 	js_pop_msg("添加榜单完成", true);
		else 				js_pop_msg("添加失败，请稍候再试", false);		
		
		break;
		
	case "rank_update":
		
		/**
		 * 修改榜单
		 */
		$rank_id	= $_INPUT["rank_id"]*1;
		$channel_id	= $_INPUT["channel_id"]*1;
		$rank_name 	= trim($_INPUT["rank_name"]);
		$rank_url	= trim($_INPUT["rank_url"]);
		$remark 	= $_INPUT["remark"];
		$img_size	= $_INPUT["img_size"];
		$sort_order	= $_INPUT["sort_order"] * 1;
		
		if($rank_id < 1)	js_pop_msg("参数错误：rank_id=0！");
		if($cms_db_obj->get_cms_count("channel_tbl", "channel_id={$channel_id}") < 1) js_pop_msg("请选择榜单所属频道！");
		if(strlen($rank_name)==0) js_pop_msg("请输入榜单名称！");
		if(strlen($remark) > 255) js_pop_msg("备注长度大于255个字符");
		
		$update_data = array(
		"channel_id" => $channel_id,
		"rank_name"  => $rank_name,
		"rank_url"	 => $rank_url,
		"remark" 	 => $remark,
		"img_size" 	 => $img_size,
		"sort_order" => $sort_order,
		);
		
		$affected_rows = $cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", $update_data);
		
		if ($affected_rows > 0)	js_pop_msg("修改榜单完成", true);
		else 					js_pop_msg("请确定已对原内容进行修改", false);		
		
		break;
	case "rank_del":
		/* 删除榜单 */
		$rank_id = $_INPUT['rank_id'] * 1;
		$channel_id = $_INPUT['channel_id'] * 1;

		if ($rank_id > 0)
		{
			/* 删除所有相关的$rank_id榜单 */
			$cms_db_obj->delete_cms("rank_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("issue_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl_freeze", "rank_id={$rank_id}");
			$cms_db_obj->delete_cms("record_tbl_last_issue", "rank_id={$rank_id}");
		}
		echo "<script>alert('删除成功！');window.location.href='cms_rank_list.php?channel_id={$channel_id}';</script>";
		break;
	case "rank_edit_passel_sort_order":
		/* 批量修改榜单排序 */
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
		$result = ($rank_count > 0 && $rank_id > 0) ? '修改成功！' : '修改失败！';
		echo "<script>alert('{$result}');window.location.href='cms_rank_list.php?channel_id={$channel_id}';</script>";
		break;
	case "rank_edit_place_number":
		/* 批量修改$rank_id榜单排名 */
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
				/* 更新记录 */
				$affected_rows = $cms_db_obj->update_cms($recore_tbl_name, "log_id={$log_id} AND issue_id={$issue_id}", $update_data);
				if ($affected_rows && false==$freeze) 
				{
					cms_system_class::record_data_last_issue_sync_by_log_id($log_id, $issue_id);//最新期榜数据同步
				}
			}
		}
		$result = $log_id > 0 ? '修改成功！' : '修改失败！';
		echo "<script>alert('{$result}');window.location.href='cms_issue_list.php?rank_id={$rank_id}';</script>";
		break;

	case "record_import":
		
		/**
		 * 数据导入
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
		
		if ($issue_id < 1) js_pop_msg("参数错误，issue_id={$issue_id}");
		
		$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "rank_id,freeze");	
		$rank_id = $issue_info["rank_id"]*1;
		$record_tbl_name = ($issue_info["freeze"]) ? "record_tbl_freeze" : "record_tbl";//冻结与正常放到不同的表
		
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
				 * 添加用户
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
		$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","record_count={$record_count}");//更新记录总数
		cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);//同步最新一期榜单数据
		
		js_pop_msg("导入完成，成功导入记录 {$add_record_count} 条"/*，榜单已存在记录 {$exists_record_count} 条"*/,
				   false,
				   "./cms_issue_list.php?rank_id={$rank_id}&issue_id={$issue_id}");
		break;	
				
	default:
		break;
}

?>