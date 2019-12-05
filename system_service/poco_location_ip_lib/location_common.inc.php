<?
/** 
 *	friend common
 */
if (!defined("G_LOCATION_PATH"))
{
	define("G_LOCATION_PATH",realpath(dirname(__FILE__))."/");
}

/**
* POCO的数据库instance
*/
global $DB;


/**
* 分离变量依赖
*/
$_INPUT=$ibforums->input;
$_MEMBER=$ibforums->member;


/**
 * 登录用户
 */
$login_id = $_MEMBER['id'];
$login_nickname = $_MEMBER['nickname'];



//分页类
//include_once(G_MYPOCO_PATH."include/page.inc.php");


//error_reporting(E_ERROR | E_PARSE);


/*****************
* 函数集
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
* 调试输出如果
*/
$_debug = $_INPUT["_debug"];
if (!function_exists("trace"))
{
	function trace($var,$title="",$server_id="")
	{
		global $_debug;
		if ($_debug)
		{
			echo "【trace ".$title."(server_id{$server_id})：";
			var_dump($var);
			echo "】<br />\r\n";
		}
	}
}


/**
* 替换sql中参数，例子：
* $sql="SELECT * FROM a WHERE field1=:x_field1";
* sqlSetParam($sql,"x_field1","值");
* $sql将会替换成"SELECT * FROM a WHERE field1='值'"
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
* 主数据库操作函数
* 返回格式是二维数组 $ret[$i]["fieldname"]
* 参数$bsingle_record为真时只取一行并且返回一维数组 $ret["fieldname"]
* 参数$server_id为查询数据库的服务器:
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

		//使用全局db实例
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();


		$server_id=$server_id*1;


		if (defined("G_DB_GET_REALTIME_DATA"))
		{
			if ($server_id*1==0) $server_id=1;
		}
		else if ($this_query_use_cache || defined("G_DB_USE_FULL_QUERYCACHE")) //开全查询cache
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
		else if ($server_id>0) //用制定机
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

		//释放内存
		$DB->free_result();





		trace($sql,"{$__query_time}s",$server_id);




		if (!defined("G_DB_GET_REALTIME_DATA") &&
		(defined("G_DB_USE_FULL_QUERYCACHE") || $this_query_use_cache)) //开全查询cache
		{
			$_cache_time = max($this_query_use_cache, G_DB_USE_FULL_QUERYCACHE*1);

			if ($_cache_time<2)
			{
				$_cache_time = 1800; //默认30分钟
			}
			$poco_cache_obj->save_cache($_cache_key,$__db_simple_getdata_result_ret_arr,$_cache_time);
		}

		return $__db_simple_getdata_result_ret_arr;
	}
}


/**
 * db_simple_get_insert_id  取最后的insert_id，需要紧跟查询后调用
 * 例子： $log_id = db_simple_get_insert_id();
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
 * db_simple_get_affected_rows  取最后操作的影响记录数，需要紧跟查询后调用
 * 例子： $rows = db_simple_get_affected_rows();
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
 * check_limit_str  检查limit 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_limit_str("0,20");
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
 * check_order_by 检查order by 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_order_by("hit_count DESC");
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
* 排序多维数组
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
* 输出一个xml node
* @param    boolean    $parse   是否过滤保留字
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
* 输出rss的个xml头
*/
if (!function_exists("echo_xml_header"))
{
	function echo_xml_header($encoding="UTF-8")
	{
		echo "<?xml version=\"1.0\" encoding=\"$encoding\" ?>\n";  //xml版本
	}
}



/**
 * 数组转换成update_sql字符串
 * 数组：array("FIELD1"=>"val1","FIELD2"="val2"') 
 * 转换成字符串：FIELD1='val1', FIELD2='val2'
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
 * 错误信息提示，并退出程序
 * @param string $msg 错误信息
 */
if (!function_exists("js_pop_msg"))
{
	function js_pop_msg($msg,$b_reload=false,$url=NULL)
	{
		echo "<script language='javascript'>
				try{window.top.showMsgBox('信息','{$msg}');}
				catch(e){alert('{$msg}');}";
		if($url) echo "window.top.location = '{$url}';";
		if($b_reload) echo "window.top.location.reload();";
		echo "</script>";
		exit;
	}
}

/**
 * 定义show_msg_box函数
 */
if(!function_exists("show_msg_box"))
{
	function show_msg_box($msg,$title,$icon="_empty",$width)
	{
		
		if(!$title) $title = "提示";	
		if (!empty($msg))
		{	
			echo '<script language="javascript">';
			echo 'var arrayObj = new Array();';
			//echo "var tmp = { btnCmd:\"window.top.location.href='{$locate_url}';\",btnText:'确定' };";
			//echo "arrayObj.push(tmp);";
			echo "window.top.showMsgBox('{$title}',\"{$msg}\",{},'{$icon}',0,true,$width);";
			echo "</script>";
			exit;				
		}
	}
}

@define('LOCATION_SYS', TRUE);

//项目基础类
include_once(G_LOCATION_PATH."include/location_db_base_action_class.inc.php");
include_once(G_LOCATION_PATH."include/location_get_ip_class.inc.php");
include_once(G_LOCATION_PATH."include/location_conf_class.inc.php");

?>