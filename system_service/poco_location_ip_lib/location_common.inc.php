<?
/** 
 *	friend common
 */
if (!defined("G_LOCATION_PATH"))
{
	define("G_LOCATION_PATH",realpath(dirname(__FILE__))."/");
}

/**
* POCO�����ݿ�instance
*/
global $DB;


/**
* �����������
*/
$_INPUT=$ibforums->input;
$_MEMBER=$ibforums->member;


/**
 * ��¼�û�
 */
$login_id = $_MEMBER['id'];
$login_nickname = $_MEMBER['nickname'];



//��ҳ��
//include_once(G_MYPOCO_PATH."include/page.inc.php");


//error_reporting(E_ERROR | E_PARSE);


/*****************
* ������
*/

if (!function_exists("add_space"))
{
	function add_space($lv)
	{
		$str='';
		if($lv!=0) {
			for($i=0;$i<=$lv;$i++ ) {
				$str.='--';
			}
		}
		return $str;
	}
}

/**
* ����������
*/
$_debug = $_INPUT["_debug"];
if (!function_exists("trace"))
{
	function trace($var,$title="",$server_id="")
	{
		global $_debug;
		if ($_debug)
		{
			echo "��trace ".$title."(server_id{$server_id})��";
			var_dump($var);
			echo "��<br />\r\n";
		}
	}
}


/**
* �滻sql�в��������ӣ�
* $sql="SELECT * FROM a WHERE field1=:x_field1";
* sqlSetParam($sql,"x_field1","ֵ");
* $sql�����滻��"SELECT * FROM a WHERE field1='ֵ'"
*/
if (!function_exists("sqlSetParam"))
{
	function sqlSetParam(&$sql,$paramName,$paramValue)
	{
		$tmp=&$sql;

		if (get_magic_quotes_gpc())
		{
			$paramValue = stripslashes($paramValue);
		}


		$tmp=str_replace(":".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
		$tmp=str_replace("@".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
	}
}


if (!function_exists("microtime_float"))
{
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

/**
* �����ݿ��������
* ���ظ�ʽ�Ƕ�ά���� $ret[$i]["fieldname"]
* ����$bsingle_recordΪ��ʱֻȡһ�в��ҷ���һά���� $ret["fieldname"]
* ����$server_idΪ��ѯ���ݿ�ķ�����:
* 1 = $DB->query($sql,0,0);
* 2 = $DB->query($sql,0,2);
*/
if (!function_exists("db_simple_getdata"))
{
	function db_simple_getdata($sql,$bsingle_record=false,$server_id=false,$this_query_use_cache=false)
	{
		//pingaddtest
		global $to_log_sql;
		if(function_exists("log_sql") && $to_log_sql)
		{
			log_sql($sql);
		}
		//pingaddtest
		
		//var_dump($server_id);var_dump(G_DB_USE_FULL_QUERYCACHE);
		$this_query_use_cache=$this_query_use_cache*1;
		$_cache_key = __CLASS__."::".__FUNCTION__.serialize(func_get_args());

		$__query_start_time = microtime_float();

		//ʹ��ȫ��dbʵ��
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();


		$server_id=$server_id*1;


		if (defined("G_DB_GET_REALTIME_DATA"))
		{
			if ($server_id*1==0) $server_id=1;
		}
		else if ($this_query_use_cache || defined("G_DB_USE_FULL_QUERYCACHE")) //��ȫ��ѯcache
		{
			$poco_cache_obj = new poco_cache_class();
			$cache_data = $poco_cache_obj->get_cache($_cache_key);
			if (!empty($cache_data))
			{
				return $cache_data;
			}
		}


		if ($server_id===true || $server_id*1==1)
		{
			$DB->query($sql,0,0);
		}
		else if ($server_id>0) //���ƶ���
		{
			$DB->query($sql,0,$server_id);
		}
		else
		{
			$DB->query($sql);
		}

		$__query_end_time = microtime_float();
		$__query_time = sprintf("%0.4f",$__query_end_time - $__query_start_time);


		if ($DB->query_count>0)
		{
			while ($row=$DB->fetch_row())
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





		trace($sql,"{$__query_time}s",$server_id);




		if (!defined("G_DB_GET_REALTIME_DATA") &&
		(defined("G_DB_USE_FULL_QUERYCACHE") || $this_query_use_cache)) //��ȫ��ѯcache
		{
			$_cache_time = max($this_query_use_cache, G_DB_USE_FULL_QUERYCACHE*1);

			if ($_cache_time<2)
			{
				$_cache_time = 1800; //Ĭ��30����
			}
			$poco_cache_obj->save_cache($_cache_key,$__db_simple_getdata_result_ret_arr,$_cache_time);
		}

		return $__db_simple_getdata_result_ret_arr;
	}
}


/**
 * db_simple_get_insert_id  ȡ����insert_id����Ҫ������ѯ�����
 * ���ӣ� $log_id = db_simple_get_insert_id();
 * @return int
 */
if (!function_exists("db_simple_get_insert_id"))
{
	function db_simple_get_insert_id()
	{
		global $DB;
		return $DB->get_insert_id();
	}
}
/**
 * db_simple_get_affected_rows  ȡ��������Ӱ���¼������Ҫ������ѯ�����
 * ���ӣ� $rows = db_simple_get_affected_rows();
 * @return int
 */
if (!function_exists("db_simple_get_affected_rows"))
{
	function db_simple_get_affected_rows()
	{
		global $DB;
		return $DB->get_affected_rows();
	}
}

/**
 * check_limit_str  ���limit ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
 * ���ӣ� check_limit_str("0,20");
 */
if (!function_exists("check_limit_str"))
{
	function check_limit_str($limitstr,$b_die=true)
	{
		$limitstr=trim($limitstr);
		$limitstr = str_replace(" ","",$limitstr);

		$c = preg_match("/^\d+,\d+$/i",$limitstr);

		if (!$c && $b_die) die(__FUNCTION__." limitstr error: $limitstr");

		return $c;
	}
}

/**
 * check_order_by ���order by ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
 * ���ӣ� check_order_by("hit_count DESC");
 */
if (!function_exists("check_order_by"))
{
	function check_order_by($orderby,$b_die=true)
	{
		$orderby=trim($orderby);

		$c = preg_match("/^(ORDER BY )?(([ ,A-Za-z0-9_])*( DESC)?)+$/i",$orderby);

		if (!$c && $b_die) die(__FUNCTION__."orderby error: $orderby");

		return $c;
	}
}

/**
* �����ά����
*/
if (!function_exists("aasort"))
{
	function aasort(&$array, $args)
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
}


/**
* ���һ��xml node
* @param    boolean    $parse   �Ƿ���˱�����
*/
if (!function_exists("echo_rss_node"))
{
	function echo_rss_node($key,$value,$parse=true,$encoding="UTF-8",$attr_arr)
	{
		$value=trim($value);

		if ( $parse==true ) $value=xml_cleanup($value);

		if ( $encoding=="UTF-8" ) $value=iconv("GBK","UTF-8",$value);

		$attr_str = "";
		if (is_array($attr_arr) && count($attr_arr)>0)
		{
			foreach ($attr_arr as $k=>$v)
			{
				if ( $parse==true ) $v=xml_cleanup($v);

				if ( $encoding=="UTF-8" ) $v=iconv("GBK","UTF-8",$v);
				$attr_str.= " $k=\"".$v."\"";
			}

		}

		$value = empty($value) ? "" : "<![CDATA[$value]]>";
		echo "<{$key}{$attr_str}>{$value}</$key>\n";
	}
}



/**
* ���rss�ĸ�xmlͷ
*/
if (!function_exists("echo_xml_header"))
{
	function echo_xml_header($encoding="UTF-8")
	{
		echo "<?xml version=\"1.0\" encoding=\"$encoding\" ?>\n";  //xml�汾
	}
}



/**
 * ����ת����update_sql�ַ���
 * ���飺array("FIELD1"=>"val1","FIELD2"="val2"') 
 * ת�����ַ�����FIELD1='val1', FIELD2='val2'
 * @access private
 */
if (!function_exists("db_arr_to_update_str"))
{
	function db_arr_to_update_str($array)
	{
		$sign = "";
		$sql_str = "";
		foreach ($array as $k=>$v)
		{
			$sql_str.= $sign."{$k}=:x_{$k}";
			sqlSetParam($sql_str, "x_{$k}", $v);
			$sign = ",";
		}
		return $sql_str;
	}
}


/**
 * ������Ϣ��ʾ�����˳�����
 * @param string $msg ������Ϣ
 */
if (!function_exists("js_pop_msg"))
{
	function js_pop_msg($msg,$b_reload=false,$url=NULL)
	{
		echo "<script language='javascript'>
				try{window.top.showMsgBox('��Ϣ','{$msg}');}
				catch(e){alert('{$msg}');}";
		if($url) echo "window.top.location = '{$url}';";
		if($b_reload) echo "window.top.location.reload();";
		echo "</script>";
		exit;
	}
}

/**
 * ����show_msg_box����
 */
if(!function_exists("show_msg_box"))
{
	function show_msg_box($msg,$title,$icon="_empty",$width)
	{
		
		if(!$title) $title = "��ʾ";	
		if (!empty($msg))
		{	
			echo '<script language="javascript">';
			echo 'var arrayObj = new Array();';
			//echo "var tmp = { btnCmd:\"window.top.location.href='{$locate_url}';\",btnText:'ȷ��' };";
			//echo "arrayObj.push(tmp);";
			echo "window.top.showMsgBox('{$title}',\"{$msg}\",{},'{$icon}',0,true,$width);";
			echo "</script>";
			exit;				
		}
	}
}

@define('LOCATION_SYS', TRUE);

//��Ŀ������
include_once(G_LOCATION_PATH."include/location_db_base_action_class.inc.php");
include_once(G_LOCATION_PATH."include/location_get_ip_class.inc.php");
include_once(G_LOCATION_PATH."include/location_conf_class.inc.php");

?>