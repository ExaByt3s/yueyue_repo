<?php
/**
 * ȫ��������
 *
 */

if (!defined("G_FULLTEXT_SEARCH_HELPER_PATH"))
{
	define("G_FULLTEXT_SEARCH_HELPER_PATH",realpath(dirname(__FILE__))."/");
}



//ȫ�������½ӿ�
include_once(G_FULLTEXT_SEARCH_HELPER_PATH."fulltext_search_helper_core_class.inc.php");



/**
* POCO���include
*/
if (!$DB)
{
	$root_path=G_FULLTEXT_SEARCH_HELPER_PATH."../";
	if (defined("G_MYPOCO_PATH") && G_USE_COMMON_LITE*1==1)
	{
		include_once($root_path."sources/common_lite.php");
	}
	else
	{
		include_once($root_path."sources/common.php");
	}
}




class fulltext_search_helper_class
{
	var $key_word="";
	var $search_result="";
	var $result_count=0;
	var $fp;

	var $server_host;
	var $server_port;
	var $time_out;


	/**
	 * �½ӿڶ���
	 *
	 * @var object
	 */
	var $fulltext_search_helper_core_obj = null;


	/**
	 * ȡ�õ�ǰʱ����΢���ʾ
	 *
	 * @access private
	 * @return float
	 */
	function _microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}



	/**
	* �����ݿ��������
	* ���ظ�ʽ�Ƕ�ά���� $ret[$i]["fieldname"]
	* ����$bsingle_recordΪ��ʱֻȡһ�в��ҷ���һά���� $ret["fieldname"]
	* ����$server_idΪ��ѯ���ݿ�ķ�����:
	* 1 = $DB->query($sql,0,0);
	* 2 = $DB->query($sql,0,2);
	* @access private
	*/
	function _db_simple_getdata($sql,$bsingle_record=false,$server_id=false)
	{
		$__query_start_time = $this->_microtime_float();

		//ʹ��ȫ��dbʵ��
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();


		$server_id=$server_id*1;


		if (defined("G_DB_GET_REALTIME_DATA") || $this->b_get_realtime_data)
		{
			if ($server_id*1==0) $server_id=1;
		}


		if ($server_id===true || $server_id*1==1) //ʵʱ��231
		{
			$DB->query($sql,0,0);
		}
		else if ($server_id*1>0) //��cacheר�û�
		{
			$DB->query($sql,0,$server_id);
		}
		else  //Ĭ�����Զ���
		{
			$DB->query($sql);
		}

		$__query_end_time = $this->_microtime_float();
		$__query_time = sprintf("%0.4f",$__query_end_time - $__query_start_time);

		if ($DB->query_count>0)
		{
			while ($row=@$DB->fetch_row())
			{
				$__db_simple_getdata_result_ret_arr[]=$row;
			}
		}

		if (count($__db_simple_getdata_result_ret_arr)==1 && $bsingle_record==true)
		{
			$__db_simple_getdata_result_ret_arr=$__db_simple_getdata_result_ret_arr[0];
		}

		//�ͷ��ڴ�
		$DB->free_result();

		$this->_trace($sql,"{$__query_time}s",$server_id);

		return $__db_simple_getdata_result_ret_arr;
	}

	/**
	 * _db_simple_get_insert_id  ȡ����insert_id����Ҫ������ѯ�����
	 * ���ӣ� $log_id = _db_simple_get_insert_id();
	 * @access private
	 * @return int
	 */
	function _db_simple_get_insert_id()
	{
		global $DB;
		return $DB->get_insert_id();
	}

	/**
	 * _check_limit_str  ���limit ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
	 * ���ӣ� _check_limit_str("0,20");
	 * @access private
	 */
	function _check_limit_str($limitstr,$b_die=true)
	{
		$limitstr=trim($limitstr);
		$limitstr = str_replace(" ","",$limitstr);

		$c = preg_match("/^\d+,\d+$/i",$limitstr);

		if (!$c && $b_die) die(__FUNCTION__." limitstr error: $limitstr");

		return $c;
	}

	/**
	 * _check_order_by ���order by ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
	 * ���ӣ� _check_order_by("hit_count DESC");
	 * @access private
	 */
	function _check_order_by($orderby,$b_die=true)
	{
		$orderby=trim($orderby);

		$c = preg_match("/^(ORDER BY )?(([ ,A-Za-z0-9_])*( DESC)?)+$/i",$orderby);

		if (!$c && $b_die) die(__FUNCTION__."orderby error: $orderby");

		return $c;
	}

	/**
	 * �����ά����
	 * @access private
	 */
	function _aasort(&$array, $args)
	{
		$sort_rule="";
		foreach($args as $arg)
		{
			$order_field = substr($arg, 1, strlen($arg));
			foreach($array as $array_row)
			{
				$sort_array[$order_field][] = $array_row[$order_field];
			}
			$sort_rule .= '$sort_array['.$order_field.'], '.($arg[0] == "+" ? SORT_ASC : SORT_DESC).',';
		}
		eval ("array_multisort($sort_rule".' &$array);');
	}

	/**
	* �滻sql�в��������ӣ�
	* $sql="SELECT * FROM a WHERE field1=:x_field1";
	* _sqlSetParam($sql,"x_field1","ֵ");
	* $sql�����滻��"SELECT * FROM a WHERE field1='ֵ'"
	* @access private
	*/
	function _sqlSetParam(&$sql,$paramName,$paramValue)
	{
		$tmp=&$sql;

		if (get_magic_quotes_gpc())
		{
			$paramValue = stripslashes($paramValue);
		}


		$tmp=str_replace(":".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
		$tmp=str_replace("@".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
	}

	/**
	* ����������
	* @access private
	*/
	function _trace($var,$title="",$server_id="")
	{
		global $_debug;
		if (empty($_debug))
		{
			$_debug = $_REQUEST["_debug"];
		}

		if ($_debug)
		{
			echo "��_trace ".$title."(server_id{$server_id})��";
			var_dump($var);
			echo "��<br />\r\n";
		}
	}


	/**
     * ����ת����update_sql�ַ���
     * ���飺array("FIELD1"=>"val1","FIELD2"="val2"') 
     * ת�����ַ�����FIELD1='val1', FIELD2='val2'
     * @access private
     */
	function _db_arr_to_update_str($array,$b_quote_val=true)
	{
		$sign = "";
		$sql_str = "";
		foreach ($array as $k=>$v)
		{
			$sql_str.= $sign."{$k}=:x_{$k}";
			$this->_sqlSetParam($sql_str, "x_{$k}", $v);
			$sign = ",";
		}
		return $sql_str;
	}




	function fulltext_search_helper_class($server_host="121.9.248.27",$server_port=9990,$time_out=30)
	{
		$this->server_host = $server_host;
		$this->server_port = $server_port;
		$this->time_out = $time_out;

		/**
		 * �½ӿڶ���
		 */
		$this->fulltext_search_helper_core_obj = new fulltext_search_helper_core_class($server_host,9970,$time_out);
	}

	function _connect()
	{
		@fclose($this->fp);
		$this->fp = @fsockopen($this->server_host,$this->server_port,$errno, $errstr, $this->time_out);
		if (!$this->fp)
		{
			die("��sock����$errstr ($errno)<br>\n");
		}
	}

	function _send_sock($request_str)
	{
		$this->_connect();

		if (!fwrite($this->fp,$request_str))
		{
			die("����ָ��ʧ��");
		}
		//fputs($this->fp,$request_str);
		$this->search_result = "";

		while(!feof($this->fp))
		{
			$this->search_result.= fgets($this->fp,128);
		}

		@fclose($this->fp);
		return $this->search_result;

	}

	/**
     * ������¼�뵽������
     *
     * @param int $sort_id //����ID
     * @param int $content_id  //����ID
     * @param string $content  //����
     * @param int $add_time //���ʱ��
     */        
	function push_content($sort_id,$content_id,$content,$add_time=0)
	{
		if (in_array($sort_id, array(8,88))) return ;

		return $this->fulltext_search_helper_core_obj->insert_simple($sort_id,$content_id,$content,$add_time);
		
		//return $this->_add_handle_queue($sort_id,$content_id,$content);
	}

	/**
     * ����
     *
     * @param int $sort_id  ����ID
     * @param string $q  �ؼ���
     * @param string $limit_str  ��ҳ����
     * @param int $total_count   //��������
     */
	function search($sort_id=0,$q,$limit_str="",& $total_count)
	{
		//return ;
		
//		$sort_id=$sort_id*1;
//
//
//		$q=trim($q);
//		$limit_str = ereg_replace("[\r\n\t]{1,}","",$limit_str);
//
//		if ($q=="")
//		{
//			die("û�������Ĺؼ���");
//		}
//
//		$result_out = array();
//		$result=$this->_send_sock("0\r\n{$sort_id}\r\n{$q}\r\n");
//
//
//		$this->key_word=$q;
//		$result_temp = explode("\r\n", $result);
//		$result = ereg_replace("[\r\n\t]{1,}","",$result_temp[2]);
//		$result_arr= explode(",",$result);
//		$result_arr = array_unique($result_arr);
//		rsort($result_arr);
//		$result_count=count($result_arr);//һ��SOCK�������
//		$this->result_count=$result_count;
//
//		$result_out = array();
//
//		if (!empty($limit_str) && $result_count>0)
//		{
//		$limit_arr= explode(",",$limit_str);
//		$start_num=intval($limit_arr[0]);//��ĳ��λ�ÿ�ʼ��
//		$count_num=intval($limit_arr[1]);//���ض��������
//
//		if ($start_num>$result_count-1)
//		{
//		//die("start_num{$start_num}����");
//		return false;
//		}
//
//		if ($start_num+$count_num>$result_count-1)
//		{
//		$count_num=$result_count-$start_num-1;
//		}
//		for ($i=0;$i<$count_num;$i++)
//		{
//		$result_out[] = $result_arr[$start_num + $i];
//		}
//		}
//		else
//		{
//		$result_out = $result_arr;
//		}
//
//		$total_count=$result_count;
		$fulltext_search_helper_core_obj = new fulltext_search_helper_core_class();
		$order = ($sort_id==8) ? "docid" : "";
		$rows =$fulltext_search_helper_core_obj->select_simple($sort_id,$q,$limit_str,$order);
		foreach ($rows as $row)
		{
			if ($sort_id==8 && empty($row["id"]))
			{
				$result_out[]=$row["user_id"];
			}
			else
			{
				$result_out[]=$row["id"];
			}
		}

		$result_out = array_unique($result_out);
		return $result_out;
	}


	/**
	 * ����һ����������
	 *
	 */
	function _add_handle_queue($sort_id,$content_id,$content)
	{
		$sort_id=$sort_id*1;
		$content_id=$content_id*1;
		$content=ereg_replace("[\r\n\t]{1,}","",$content);

		$content = strip_tags($content);


		if ($sort_id<1)
		{
			return false;
		}
		if ($content_id<1)
		{
			return false;
		}

		if (empty($content))
		{
			return false;
		}

		$sql="INSERT IGNORE INTO fulltext_search_helper_db.push_content_queue_tbl (add_time, sort_id, content_id, content)";
		$sql.=" VALUES(:x_add_time, :x_sort_id, :x_content_id, :x_content)";
		$this->_sqlSetParam($sql,"x_add_time",time());
		$this->_sqlSetParam($sql,"x_sort_id",$sort_id);
		$this->_sqlSetParam($sql,"x_content_id",$content_id);
		$this->_sqlSetParam($sql,"x_content",$content);
		$this->_db_simple_getdata($sql,false,2);

		return true;
	}

	/**
	 * ȡ�ö�����handled=0��һ������
	 *
	 * @return 
	 */
	function get_one_queue()
	{
		$sql = " SELECT * FROM fulltext_search_helper_db.push_content_queue_tbl";
		$sql.= " WHERE handled=0 LIMIT 1";
		$rows = $this->_db_simple_getdata($sql,true,2);
		return empty($rows)?false:$rows;
	}

	/**
	 * ɾ������
	 *
	 */
	function delete_handled_queue()
	{
		$sql = " DELETE FROM fulltext_search_helper_db.push_content_queue_tbl";
		$sql.= " WHERE handled=1";
		$rows = $this->_db_simple_getdata($sql,false,2);
	}


	/**
 	 * �ܶ���
 	 *
 	 * @param int $user_id
 	 * @return bool
 	 */
	function handle_queue()
	{

		ignore_user_abort(true);

		while ($log_info  = $this->get_one_queue())//ȡ�ö���
		{
			$content     = $log_info['content'];
			$content_id  = $log_info['content_id'];
			$sort_id     = $log_info['sort_id'];
			$log_no     = $log_info['log_no'];

			$sql = "UPDATE fulltext_search_helper_db.push_content_queue_tbl SET handled=1 WHERE log_no={$log_no}";
			$this->_db_simple_getdata($sql,false,2);

			$content_length=strlen($content);
			$result2=$this->_send_sock("{$content_length}\r\n{$content_id}\r\n{$sort_id}\r\n{$content}");
			/*if (substr($result2,0,2)=="OK")
			{
			return true;
			}
			else
			{
			return false;
			}*/
		}

		$this->delete_handled_queue();//�������ɾ����ɵĶ���
		return true;
	}


}


?>