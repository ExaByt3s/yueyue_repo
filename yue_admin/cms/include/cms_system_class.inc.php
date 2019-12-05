<?php
/**
 * best pocoers ϵͳ��
 * @author Bowie
 */


class cms_system_class
{

	function cms_system_class()
	{

	}

	/**
	 * �����ڰ�����¼ͬ��
	 *
	 * @param int $log_id
	 * @param int $issue_id
	 */
	function record_data_last_issue_sync_by_log_id($log_id, $issue_id)
	{
		if ($log_id*1 < 1) 		die(__CLASS__."::".__FUNCTION__." ERROR: log_id={$log_id}");
		if ($issue_id*1 < 1) 	die(__CLASS__."::".__FUNCTION__." ERROR: issue_id={$issue_id}");

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		/**
		 * ȡ���ݣ�����Ƿ�����
		 */
		$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "rank_id,issue_number");
		if ($issue_info["rank_id"])
		{
			$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$issue_info["rank_id"]}", "last_issue");
		}


		/**
		 * ������£�ͬ�������°񵥱�
		 */
		if ($rank_info["last_issue"]==$issue_info["issue_number"])
		{
			/**
			 * ����ͬ������
			 */
			$record_info = $cms_db_obj->get_cms_info("record_tbl", "log_id={$log_id}");

			if (empty($record_info))
			{
				$cms_db_obj->delete_cms("record_tbl_last_issue", "log_id={$log_id}");
			}
			else
			{
				$cms_db_obj->replace_cms("record_tbl_last_issue", $record_info);
			}
		}
	}

	/**
	 * �����ڰ���������ͬ��
	 *
	 * @param int $rank_id
	 */
	function record_data_last_issue_sync_by_rank_id($rank_id)
	{
		if ($rank_id*1 < 1) 		die(__CLASS__."::".__FUNCTION__." ERROR: rank_id={$rank_id}");


		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$issue_list = $cms_db_obj->get_cms_list("issue_tbl", "rank_id={$rank_id} AND freeze=0", "issue_id", "issue_number DESC", "", "0,1");
		$issue_id = $issue_list[0]["issue_id"];

		$cms_db_obj->delete_cms("record_tbl_last_issue", "rank_id={$rank_id}");//ɾ�������ڰ��¼
		if ($issue_id > 0)
		{
			$record_list = $cms_db_obj->get_cms_list("record_tbl", "issue_id={$issue_id}");
			foreach ($record_list as $record_info)
			{
				$cms_db_obj->replace_cms("record_tbl_last_issue", $record_info);
			}
		}
	}

	/**
	 * ȡ����Ϣ
	 *
	 * @param int $rank_id
	 * @param string $return_key
	 * @return mixed
	 */
	function get_rank_info_by_rank_id($rank_id, $return_key="")
	{
		if ($rank_id*1<1) return array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}");

		if ($return_key)
		{
			return $rank_info[$return_key];
		}
		else
		{
			return $rank_info;
		}
	}

	/**
	 * ȡ����Ϣ���ϰ��¼
	 *
	 * @param int $rank_id				��id
	 * @param int $issue_number			����
	 * @param bool $b_get_record_info	�Ƿ�ȡ�ϰ��¼��Ĭ��ȡ
	 * @param string $record_limit		ȡ�ϰ��¼����
	 * @param bool $b_rand_issue_number �Ƿ�ȡ���һ��
	 * @param bool $b_get_freeze		�Ƿ�ȡ���ص�����
	 * @return array
	 */
	function get_issue_by_rank_id($rank_id, $issue_number=0, $b_get_record_info=true, $record_limit="0,10", $sort="ASC", $b_rand_issue_number=false, $b_get_freeze=false)
	{

		if ($rank_id*1<1) return array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$ret = array();
		$issue_number = $issue_number*1;
		$sort = trim(strtoupper($sort));

		if (!in_array($sort, array("DESC","ASC"))) $sort = "ASC";

		$where_sql = "rank_id='{$rank_id}' ";
		if (!$b_get_freeze) 	$where_sql.=" AND freeze=0";
		if ($issue_number > 0) 	$where_sql.=" AND issue_number='{$issue_number}'";

		$issue_limit = ($b_rand_issue_number) ? "" : "0,1";//������ȡһ�ڣ���ȡȫ��������ȡlimit 1

		$issue_list = $cms_db_obj->get_cms_list("issue_tbl", $where_sql, "*", "issue_number DESC", "", $issue_limit);


		if ($b_rand_issue_number)
		{
			$issue_info = $issue_list[rand(0,count($issue_list)-1)];		//������ȡһ�ڣ����ҽ������
			$issue_id = $issue_info["issue_id"]*1;
			$issue_number = $issue_id;
		}
		else
		{
			$issue_info = $issue_list[0];
			$issue_id = $issue_info["issue_id"]*1;
		}

		$ret = $issue_info;
		if ($b_get_record_info && $issue_id > 0)
		{
			if($issue_info['freeze']) 
			{
				$record_tbl_name = 'record_tbl_freeze';
			}
			else 
			{
				$record_tbl_name = ($issue_number > 0) ? "record_tbl" : "record_tbl_last_issue";//ȡĳһ�� �� Ĭ��ȡ����һ��
			}
			$ret["record_arr"] = $cms_db_obj->get_cms_list($record_tbl_name,
			"issue_id={$issue_id}",
			"*",
			"place_number {$sort}",
			"",
			$record_limit);
		}
		
		if (!isset($_REQUEST['_no_cdn_img']) && !defined('G_YUE_CMS_NO_CDN_IMG')) 
		{
			//cdnͼƬת��
			include_once("/disk/data/htdocs233/mypoco/include/poco_content_output_cdn_parser_class.inc.php");
			$content_output_cdn_parser_obj	=	new poco_content_output_cdn_parser_class();
			
			foreach ($ret["record_arr"] as $k=>$v)
			{
				$ret["record_arr"][$k]['img_url'] = $content_output_cdn_parser_obj->parse($ret["record_arr"][$k]['img_url']);
				$ret["record_arr"][$k]['remark'] = $content_output_cdn_parser_obj->parse($ret["record_arr"][$k]['remark']);
			}
		}

		return $ret;
	}

	/**
	 * ȡһ���񵥣������б�
	 *
	 * @param bool $b_select_conut	�Ƿ�ȡ����
	 * @param int $rank_id			��rank_id
	 * @param string $limit			limit
	 * @param string $order_by		����
	 * @param string $freeze		����״̬(Ĭ��0,ȡδ����ģ�1Ϊ����ģ�flaseΪȫ��)
	 * @return mixed
	 */
	function get_issue_list_by_rank_id($b_select_conut=false, $rank_id, $limit="0,10", $order_by="issue_number DESC", $freeze=0)
	{
		if ($rank_id*1<1) die(__CLASS__."::".__FUNCTION__." ERROR:rank_id={$rank_id}");

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$tbl_name = "issue_tbl";
		$where_sql = "rank_id={$rank_id}";

		$where_sql.= (false===$freeze) ? "" : " AND freeze=:x_freeze";
		sqlSetParam($where_sql, "x_freeze", $freeze);

		if ($b_select_conut)
		{
			return  $cms_db_obj->get_cms_count($tbl_name, $where_sql);
		}
		else
		{

			return  $cms_db_obj->get_cms_list($tbl_name, $where_sql, "*", $order_by, "", $limit);
		}
	}

	/**
	 * ȡһ���񵥣�ȫ����¼�б�
	 *
	 * @param bool $b_select_conut
	 * @param mixed $rank_id
	 * @param string $limit
	 * @param string $order_by
	 * @param bool $b_get_freeze
	 * @param bool $where_str
	 * @return array
	 */
	function get_record_list_by_rank_id($b_select_conut=false, $rank_id, $limit="0,10", $order_by="place_number ASC", $b_get_freeze=false, $where_str="")
	{
		if (is_array($rank_id))
		{
			$rank_id_str = implode(",", $rank_id);
		}
		else
		{
			$rank_id_str = $rank_id*1;
		}

		if(empty($rank_id_str)) return ($b_select_conut) ? 0 : array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$tbl_name = ($b_get_freeze) ? "record_tbl_freeze": "record_tbl";
		$where_sql = "rank_id IN ({$rank_id_str})";
		$where_sql.= ($where_str) ? " AND {$where_str}" : "";

		if ($b_select_conut)
		{
			return  $cms_db_obj->get_cms_count($tbl_name, $where_sql);
		}
		else
		{
			//return  $cms_db_obj->get_cms_list($tbl_name, $where_sql, "*", $order_by, "", $limit);
			$ret["rank_id"] = $rank_id;
			$ret["record_arr"] = $cms_db_obj->get_cms_list($tbl_name, $where_sql, "*", $order_by, "", $limit);

			if (!isset($_REQUEST['_no_cdn_img']) && !defined('G_YUE_CMS_NO_CDN_IMG'))
			{
				//cdnͼƬת��
				include_once("/disk/data/htdocs233/mypoco/include/poco_content_output_cdn_parser_class.inc.php");
				$content_output_cdn_parser_obj	=	new poco_content_output_cdn_parser_class();

				foreach ($ret["record_arr"] as $k=>$v)
				{
					$ret["record_arr"][$k]['img_url'] = $content_output_cdn_parser_obj->parse($ret["record_arr"][$k]['img_url']);
					$ret["record_arr"][$k]['remark'] = $content_output_cdn_parser_obj->parse($ret["record_arr"][$k]['remark']);
				}
			}
			
			return $ret["record_arr"];
		}
	}

	/**
	 * ȡһ�ڰ񵥵ļ�¼
	 *
	 * @param bool $b_select_conut
	 * @param int $issue_id
	 * @param string $limit
	 * @param string $order_by
	 * @param mixed $freeze
	 * @param string $where_str
	 * @return mixed
	 */
	function get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,10", $order_by="place_number ASC", $freeze=null, $where_str="")
	{
		if ($issue_id*1<1) return array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		if (is_null($freeze))
		{
			$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "freeze");
			$freeze = $issue_info["freeze"];
		}

		$tbl_name = ($freeze) ? "record_tbl_freeze": "record_tbl";
		$where_sql = "issue_id={$issue_id}";
		$where_sql.= ($where_str) ? " AND {$where_str}" : "";

		if ($b_select_conut)
		{
			return  $cms_db_obj->get_cms_count($tbl_name, $where_sql);
		}
		else
		{
			return  $cms_db_obj->get_cms_list($tbl_name, $where_sql, "*", $order_by, "", $limit);
		}
	}

	/**
	 * ȡһ���񵥵ļ�¼����û��ȡ�������ܣ�
	 *
	 * @param int $rank_id
	 * @param string $limit
	 * @param string $order_by
	 * @return array
	 */
	function get_record_by_rank_id($rank_id, $limit="0,10", $order_by="issue_id")
	{
		if($rank_id*1<1) return array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$ret = array();
		$ret = $cms_db_obj->get_cms_list("record_tbl",
		"rank_id={$rank_id}",
		"*",
		$order_by,
		"",
		$limit);

		return $ret;
	}

	/**
	 * ȡ����һ�ڵļ�¼�б�
	 *
	 * @param bool $b_select_conut	
	 * @param string $limit
	 * @param string $order_by
	 * @param mixed $rank_id
	 * @param string $where_str
	 */
	function get_last_issue_record_list($b_select_conut=false, $limit="0,10", $order_by="place_number ASC", $rank_id, $where_str="")
	{
		if (is_array($rank_id))
		{
			$rank_id_str = implode(",", $rank_id);
		}
		else
		{
			$rank_id_str = $rank_id*1;
		}

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		$tbl_name = "record_tbl_last_issue";
		$where_sql = "1";
		$where_sql.= ($rank_id_str) ? " AND rank_id IN ($rank_id_str)" : "";
		$where_sql.= ($where_str) ? " AND ".$where_str : "";

		if ($b_select_conut)
		{
			return  $cms_db_obj->get_cms_count($tbl_name, $where_sql);
		}
		else
		{
			return  $cms_db_obj->get_cms_list($tbl_name, $where_sql, "*", $order_by, "", $limit);
		}
	}

	/**
	 * ȡ�������Ű�����һ�ڵ������¼
	 *
	 * @param int $rand_count
	 * @return array
	 */
	function get_last_issue_rand_list($rand_count=10)
	{
		if ($rand_count*1 < 1) return array();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		$where_sql = "user_id>0 AND rank_id IN (174,1,46,56,57,58,59,60,61,62,63,64,65,66,127,128,129,130,131,132,133,134,135,136,137,113,112,2,3,4,5,6,8,9,10,11,12,13,14,15,16,18,19,20,181,350,351,352,353,1224,1233,1226,1523,225,217,181)";
		$total_count = $cms_db_obj->get_cms_count("record_tbl_last_issue", $where_sql,"user_id");
		$limit = rand(0, ($total_count-$rand_count)).",".$rand_count;
		$ret =  $cms_db_obj->get_cms_list("record_tbl_last_issue", $where_sql, "*", "log_id DESC", "user_id", $limit);
		shuffle($ret);
		return $ret;
	}

	/**
	 * ȡһ��ʱ���֮�ڵ��ϰ��û��б�
	 *
	 * @param bool $b_select_count
	 * @param int $form_date
	 * @param int $to_date
	 * @param int $channel_id
	 * @param string $limit
	 * @param string $order_by
	 * @return array
	 */
	function get_user_list_by_date($b_select_count=false, $form_date="", $to_date="", $channel_id=0, $limit="0,10", $order_by="log_id DESC")
	{
		if($form_date < 1) $form_date = time()-3600*24*7;
		if($to_date   < 1) $to_date	  = time();

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		$where_sql = "post_date BETWEEN '{$form_date}' AND '{$to_date}'";
		$where_sql.= ($channel_id) ? " AND channel_id IN ({$channel_id})" : "";
		if ($b_select_count)
		{
			return $cms_db_obj->get_cms_count("record_tbl", $where_sql, "user_id");
		}
		else
		{
			$ret =  $cms_db_obj->get_cms_list("record_tbl", $where_sql, "*", $order_by, "user_id", $limit);
			return $ret;
		}
	}
	
	/**
	 * �½�һ����
	 *
	 * @param int $channel_id		Ƶ��id
	 * @param string $rank_name		������
	 * @param string $rank_url		��url
	 * @param string $remark		��ע
	 * @param int $img_size			ͼƬĬ��size
	 * @param int $sort_order		����
	 * @return int
	 */
	function add_rank($channel_id, $rank_name, $rank_url='', $remark='', $img_size=0, $sort_order=0)
	{
		$channel_id = (int)$channel_id;
		$rank_name 	= trim($rank_name);
		$rank_url	= trim($rank_url);
		$remark 	= $remark;
		$img_size	= (int)$img_size;
		$sort_order	= (int)$sort_order;
		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		
		if($cms_db_obj->get_cms_count("channel_tbl", "channel_id={$channel_id}") < 1) return "err:channel_id error";
		if(strlen($rank_name)==0) return "err:rank_name is empty";
		if(strlen($remark) > 255) return "err:remark > 255";
		
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
		return $rank_id;
	}

	/**
	 * �½�һ�ڰ�
	 *
	 * @param int $rank_id			��id
	 * @param int $issue_number		��������(Ϊ0ʱ���Զ�Ϊ����һ��)
	 * @param string $issue_name	��������(Ϊ��ʱ��������-��x��)
	 * @param string $begin_date	��ʼʱ��(Ϊ0ʱ��Ĭ��Ϊ����)
	 * @param string $end_date		����ʱ��(Ϊ0ʱ��Ĭ��Ϊ����)
	 * @param bool $freeze			�Ƿ�����(Ĭ��Ϊ��ʾ)
	 * @return int
	 */
	function add_issue_by_rank_id($rank_id, $issue_number=0, $issue_name="", $begin_date=0, $end_date=0, $freeze=false)
	{
		global $yue_login_id;

		$rank_id		= $rank_id*1;
		$issue_number 	= $issue_number*1;
		$issue_name 	= trim($issue_name);
		$begin_date 	= $begin_date ? strtotime($begin_date) : mktime(0,0,0,date("m"),date("d"),date("Y"));
		$end_date 		= $end_date	? strtotime($end_date) : mktime(0,0,0,date("m"),date("d"),date("Y"));
		$freeze			= (bool)$freeze;
		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		if ($issue_number<1 || strlen($issue_name)==0)
		{
			$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}", "rank_name,last_issue");
			$issue_list = $cms_db_obj->get_cms_list("issue_tbl", "rank_id='{$rank_id}'", "issue_number", "issue_number DESC", "", "0,1");
			$issue_info = $issue_list[0];

			if ($issue_number<1)  		$issue_number 	= $issue_info["issue_number"]+1;
			if (strlen($issue_name)==0) $issue_name 	= "{$rank_info["rank_name"]}-��{$issue_number}��";
		}

		if ($rank_id < 1) 		return "err:rank_id={$rank_id}";
		if ($issue_number < 1) 	return "err:issue_number={$issue_number}";
		if (strlen($issue_name)==0)	return "err:issue_name={$issue_name}";
		if ($end_date < $begin_date) return "err:time is error ".date("Ymd",$begin_date)."--".date("Ymd",$end_date);
		if ($cms_db_obj->get_cms_count("issue_tbl", "rank_id={$rank_id} AND issue_number={$issue_number}") > 0) return "err:issue_number={$issue_number} exists";

		$insert_data = array(
		"rank_id"		=> $rank_id,
		"issue_number"	=> $issue_number,
		"issue_name"	=> $issue_name,
		"begin_date"	=> $begin_date,
		"end_date"		=> $end_date,
		"issuer_user_id"=> $yue_login_id,
		"freeze"		=> $freeze,
		"post_date"		=> time(),
		);

		$issue_id = $cms_db_obj->insert_cms("issue_tbl", $insert_data);

		if ($issue_id > 0)
		{
			$last_issue = $cms_db_obj->get_cms_info("issue_tbl", "rank_id={$rank_id} AND freeze=0", "MAX(issue_number) AS last_issue");
			$cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", "last_issue='{$last_issue["last_issue"]}'");//�����������

			if (false==$freeze)
			{
				//ͬ�����°�����
				cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);
			}
			return $issue_id;
		}
		else
		{
			return "err:issue_id=0";
		}
	}

	/**
	 * ��Ӱ�һ����¼
	 *
	 * @param int $issue_id			������id
	 * @param int $user_id			�û�id(����Ϊ0)
	 * @param string $title			����(Ϊ��ʱ��Ĭ��Ϊ�û���)
	 * @param int $place_number		����(Ϊ0ʱ���Զ����Ϊ���һ��)
	 * @param string $link_url		�������(Ϊ��ʱ��Ĭ��Ϊ�û��ռ��ַ)
	 * @param string $img_url		ͼƬ��ַ(Ϊ��ʱ��Ĭ��Ϊ�û�ͷ��)
	 * @param string $content		����
	 * @param string $remark		��ע
	 * @return int
	 */
	function add_record_by_issue_id($issue_id,$user_id,$title,$place_number,$link_url,$img_url,$content,$remark,$link_type)
	{
		global $yue_login_id;

		$issue_id 		= $issue_id*1;
		$user_id 		= $user_id*1;
		$title 			= trim($title);
		$place_number 	= $place_number*1;
		$link_url 		= trim($link_url);
		$img_url 		= trim($img_url);
		$remark	 		= trim($remark);
		$content	 	= trim($content);
		$link_type		= $link_type;

        //$array_black_list = array(106048,106028,106032,106047,106023,105983,106220,106224,106200,106201,106226,106229,106233,106356,106357,106334,106331,106328,106443,106465,106457,106450,106458,106424,106428,106429,106444,106447,106477,106462,106464,106439,106446,106453,106459,106586,106590,106592,106545,106555,106587,106591,106553,106559,106569,106572,106576,106702,106704,106685,106688,106659,106694,106695,106687,105983,106023,106028,106032,106047,106048,106200,106201,106220,106224,106226,106229,106233,106328,106331,106334,106356,106357,106424,106428,106429,106439,106443,106444,106446,106447,106450,106453,106457,106458,106459,106462,106464,106465,106477,106545,106553,106555,106559,106569,106572,106576,106586,106587,106590,106591,106592,106659,106685,106687,106688,106694,106695,106702,106704,114588,116212,114102,114112,114081,114066,114068,114261,114265,114242,114245,114246,114213,114218,114222,114396,114397,114377,114554,114557,114533,114537,114657,114664,114667,114659,114653,114650,114640,114783,114784,114789,114799,114800,114801,114935,114930,114943,114946,115069,115070,115071,115191,115193,115196,115195,115192,115534,115536,115537,115551,115547,115543,115625,115626,115628,115691,115702,115694,115696);
	    //if(in_array($user_id, $array_black_list)) return "err:log_id=0";;


		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		if ($issue_id < 1) return "err:issue_id={$issue_id}";

		//ȡ�ڰ��Ƶ��id�Ͱ�id
		$issue_info  = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "rank_id,freeze");
		$rank_id = $issue_info["rank_id"]*1;//��id
		$freeze	= $issue_info["freeze"]*1;//�Ƿ񶳽�

		$rank_info  = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}", "channel_id");
		$channel_id = $rank_info["channel_id"]*1;//Ƶ��id

		$record_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";//�����������ŵ���ͬ�ı�

		if ($place_number < 1)
		{
			//ȡ��������
			$issue_list = $cms_db_obj->get_cms_list($record_tbl_name, "issue_id={$issue_id} AND rank_id={$rank_id}","place_number", "place_number DESC", "", "0,1");
			$place_number = ($issue_list[0]["place_number"]*1+1);
		}

		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_info = $pai_user_obj->get_user_info($user_id, "nickname,sex,location_id,role");

		if (strlen($title)==0) $title = $user_info["nickname"];
		if (strlen($link_url)==0) 
		{
			//û�����ӣ�ֱ�����û��ռ�����
			$link_url = "";
		}
		
		if (strlen($img_url)==0)
		{
			$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
			$img_url = $user_icon_obj->get_user_icon($user_id, 86);
		}

		$insert_data = array(
		"issue_id"		=> $issue_id,
		"rank_id"		=> $rank_id,
		"channel_id"	=> $channel_id,
		"place_number"	=> $place_number,
		"user_id"		=> $user_id,
		"user_name"		=> $user_info["nickname"],
		"sex"			=> $user_info["sex"],
		"location_id"	=> $user_info["location_id"],
		"role"			=> $user_info["role"],
		"title"			=> $title,
		"img_url"		=> $img_url,
		"link_url"		=> $link_url,
		"link_type"		=> $link_type,
		"content"		=> $content,
		"remark"		=> $remark,
		"admin_user_id" => $yue_login_id,
		"post_date"		=> time(),
		);

		$log_id = $cms_db_obj->insert_cms($record_tbl_name, $insert_data);

		if ($log_id > 0)
		{
			$record_count = $cms_db_obj->get_cms_count($record_tbl_name, "issue_id={$issue_id}");
			$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","record_count={$record_count}");//��������

			if (!$freeze)
			{
				cms_system_class::record_data_last_issue_sync_by_log_id($log_id, $issue_id);//�����ڰ�����ͬ��
			}

			return $log_id;
		}
		else
		{
			return "err:log_id=0";
		}
	}

	/**
	 * �޸�һ���񵥼�¼
	 *
	 * @param int $log_id			��¼id
	 * @param int $issue_id			�ڰ�id
	 * @param array $update_data	�޸���������
	 * @param bool $freeze			�Ƿ�Ϊ����İ�(Ĭ��Ϊnull,��Щ���������Ч��)
	 * @return unknown
	 */
	function update_issue_record_by_log_id($log_id, $issue_id, $update_data, $freeze=null)
	{
		if ($log_id < 1) return "err:log_id={$log_id}";
		if ($issue_id < 1) return "err:issue_id={$issue_id}";

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		
		
		if (is_null($freeze))
		{
			$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "freeze");
			$freeze = (bool)$issue_info["freeze"];
		}

		$recore_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";
		
		$cms_info = $cms_db_obj->get_cms_info($recore_tbl_name, "log_id={$log_id} AND issue_id={$issue_id}");
		
		if ($cms_info['user_id'])
		{
			$pai_user_obj = POCO::singleton ( 'pai_user_class' );
			$user_info = $pai_user_obj->get_user_info($cms_info['user_id'], "nickname,sex,location_id,role");

			$update_data["user_name"]	= $user_info["nickname"];
			$update_data["sex"]			= $user_info["sex"];
			$update_data["location_id"]	= $user_info["location_id"];
			$update_data["role"]		= $user_info["role"];
		}


		$affected_rows = $cms_db_obj->update_cms($recore_tbl_name, "log_id={$log_id} AND issue_id={$issue_id}", $update_data);
		if ($affected_rows && false==$freeze)
		{
			cms_system_class::record_data_last_issue_sync_by_log_id($log_id, $issue_id);//�����ڰ�����ͬ��
		}
		return $log_id;
	}

	/**
	 * ȡһ���񵥼�¼����ϸ��Ϣ
	 *
	 * @param int $log_id
	 * @param int $issue_id
	 * @param bool $freeze			�Ƿ�Ϊ����İ�(Ĭ��Ϊnull,��Щ���������Ч��)
	 * @param string $select_str	��ѯ���ֶ�
	 * @return array
	 */
	function get_issue_record_info_by_log_id($log_id, $issue_id, $freeze=null , $select_str="*")
	{
		if ($log_id < 1) return "err:log_id={$log_id}";
		if ($issue_id < 1) return "err:issue_id={$issue_id}";
		if (!$select_str) $select_str = "*";

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		if (is_null($freeze))
		{
			$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "freeze");
			$freeze = (bool)$issue_info["freeze"];
		}

		$recore_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";

		return $cms_db_obj->get_cms_info($recore_tbl_name, "log_id={$log_id}", $select_str);
	}

	/**
	 * ɾ���񵥼�¼
	 *
	 * @param int $log_id	��¼id
	 * @param int $issue_id �ڰ�id
	 * @param bool $freeze	�Ƿ�Ϊ����İ�(Ĭ��Ϊnull,���˲��������Ч��)
	 * @return bool
	 */
	function del_issue_record_by_log_id($log_id, $issue_id, $freeze=null)
	{
		$log_id 	= $log_id*1;
		$issue_id	= $issue_id*1;

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		if ($log_id < 1) return "err:log_id={$log_id}";
		if ($issue_id < 1) return "err:issue_id={$issue_id}";

		if (is_null($freeze))
		{
			$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "freeze");
			$freeze = (bool)$issue_info["freeze"];
		}

		$recore_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";

		$affected_rows = $cms_db_obj->delete_cms($recore_tbl_name, "log_id={$log_id} AND issue_id={$issue_id}");//ɾ����¼

		if ($affected_rows)
		{
			$record_count = $cms_db_obj->get_cms_count($recore_tbl_name, "issue_id={$issue_id}");
			$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","record_count={$record_count}");//��������

			if (false==$freeze)
			{
				cms_system_class::record_data_last_issue_sync_by_log_id($log_id, $issue_id);//�����ڰ�����ͬ��
			}

			return $log_id;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * �ⶳһ�ڰ�
	 *
	 * @param int $issue_id
	 * @return bool
	 */
	function unfreeze_issue_by_issue_id($issue_id)
	{
		if ($issue_id < 1) return "err:issue_id={$issue_id}";

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		//��֤һ�ţ���һ���Ƕ����˵�
		$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}","freeze,rank_id");
		$rank_id = $issue_info["rank_id"]*1;

		if (1==$issue_info["freeze"] && $rank_id>0)
		{
			//�������������ݹ���
			$sql = "INSERT INTO pai_cms_db.cms_record_tbl
				(issue_id, rank_id, channel_id, place_number, user_id, user_name, sex, location_id, role, title, img_url, 
				link_url, link_type, content, remark, admin_user_id, post_date)
				SELECT issue_id, rank_id, channel_id, place_number, user_id, user_name, sex, location_id, role, title, img_url, 
				link_url, link_type, content, remark, admin_user_id, post_date FROM pai_cms_db.cms_record_tbl_freeze 
				WHERE issue_id={$issue_id}";
			db_simple_getdata($sql,false,101);

			//ɾ������������
			$cms_db_obj->delete_cms("record_tbl_freeze", "issue_id={$issue_id}");

			//����״̬Ϊ�ⶳ
			$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","freeze=0");

			//�����������
			$last_issue = $cms_db_obj->get_cms_info("issue_tbl", "rank_id={$rank_id} AND freeze=0", "MAX(issue_number) AS last_issue");
			$cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", "last_issue='{$last_issue["last_issue"]}'");

			//ͬ������һ�ڰ�
			cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * ����һ�ڰ�
	 *
	 * @param int $issue_id
	 * @return bool
	 */
	function freeze_issue_by_issue_id($issue_id)
	{
		if ($issue_id < 1) return "err:issue_id={$issue_id}";

		$cms_db_obj = POCO::singleton ( 'cms_db_class' );

		//��֤һ�ţ���һ����δ�������
		$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}","freeze,rank_id");
		$rank_id = $issue_info["rank_id"]*1;

		if (0==$issue_info["freeze"] && $rank_id>0)
		{
			//�������붳���
			$sql = "INSERT INTO pai_cms_db.cms_record_tbl_freeze
				(issue_id, rank_id, channel_id, place_number, user_id, user_name, sex, location_id, role, title, img_url, 
				link_url, link_type, content, remark, admin_user_id, post_date)
				SELECT issue_id, rank_id, channel_id, place_number, user_id, user_name, sex, location_id, role, title, img_url, 
				link_url, link_type, content, remark, admin_user_id, post_date FROM pai_cms_db.cms_record_tbl
				WHERE issue_id={$issue_id}";
			db_simple_getdata($sql,false,101);

			//ɾ�������������
			$cms_db_obj->delete_cms("record_tbl", "issue_id={$issue_id}");

			//����״̬Ϊ����
			$cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id}","freeze=1");

			//�����������
			$last_issue = $cms_db_obj->get_cms_info("issue_tbl", "rank_id={$rank_id} AND freeze=0", "MAX(issue_number) AS last_issue");
			$cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", "last_issue='{$last_issue["last_issue"]}'");

			//ͬ������һ�ڰ�
			cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
?>