<?php

//发布需求数据获取


//include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
/**
 * 报表数据
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年5月8日
 * @version 1
 */
 

/**
 * 摄影师点击发需求的pv,uv
 */
/*
echo "<table>";
 for ($i = 1; $i < 19; $i++)
 {
 	$table_num = sprintf('%02d', $i);
 	$sql_str = "select  COUNT(login_id) AS PV,COUNT(DISTINCTROW(login_id)) AS UV,
 			COUNT(DISTINCTROW(ip)) AS IP from yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num} 
 	WHERE request_filename_param  LIKE '%3Ddemand%'";
    //$sql_str="SELECT COUNT(login_id) AS PV, COUNT(DISTINCTROW(login_id)) AS UV FROM yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num}";
    $result = db_simple_getdata($sql_str,true,22);
    echo "<tr>";
    echo "<td>2015-05-$table_num</td>";
    echo "<td>{$result['PV']}</td>";
    echo "<td>{$result['UV']}</td>";
    echo "</tr>";
  }
  echo "</table>";
  exit;
*/
 /**
  * 模特点击工作机会
  * 
  * */
 
/*  echo "<table>";
 for ($i = 1; $i < 19; $i++)
 {
	 $table_num = sprintf('%02d', $i);
	 $sql_str = "select  COUNT(login_id) AS PV,COUNT(DISTINCTROW(login_id)) AS UV,
	 COUNT(DISTINCTROW(ip)) AS IP from yueyue_log_tmp_db.yueyue_tmp_log_201505{$table_num}
	 WHERE request_filename_param  LIKE '%camera_demand%'";
	 $result = db_simple_getdata($sql_str,true,22);
	 echo "<tr>";
	 echo "<td>2015-05-$table_num</td>";
	 echo "<td>{$result['PV']}</td>";
	 echo "<td>{$result['UV']}</td>";
	 echo "</tr>";
 }
 echo "</table>";
 exit;*/

/**
 * 
 *从摄影师发布需求到达成订单的响应时间 
 * 
 **/

/* echo "<table>";
 for ($i = 1; $i<19; $i++ )
 {
 	$create_time = "2015-05-".sprintf('%02d', $i);

 	$sql_str = "SELECT order_id,audit_time from pai_user_library_db.model_oa_order_tbl
 	           WHERE audit_status='pass' AND source = 4 AND FROM_UNIXTIME(audit_time,'%Y-%m-%d')='{$create_time}' ";
 	$ret = db_simple_getdata($sql_str,false,101);
 	if(!is_array($ret)) $ret = array();
 	$show_total = 0;
 	$time_total = 0;
 	foreach ($ret as $key=>$val)
 	{
 		$info = get_avg_min_time($val['order_id'],$val['audit_time']);
 		if($info)
 		{
 			$time_total += $info;
 			$show_total +=1;
 		}
 	}

 	 $avg_time = intval($time_total/$show_total);
 	 echo "<tr>";
	 echo "<td>$create_time</td>";
	 echo "<td>{$avg_time}</td>";
	 echo "</tr>";
 }
 echo "</table>";


 function get_avg_min_time($order_id,$audit_time)
 {
 	$sql_str = "SELECT min(add_time) as add_time from pai_user_library_db.model_oa_enroll_tbl WHERE order_id = {$order_id} ";
 	$ret = db_simple_getdata($sql_str,true,101);
 	if(!is_array($ret) || empty($ret)) return false;
 	$timm = intval($ret['add_time']-$audit_time);
 	if($timm <0) return 0;
 	return $timm;
 }
exit;*/

/**
 * 
 *  通过需求获取到订单数和订单流水
 * 
 * */

$user_obj  = POCO::singleton('pai_user_class');
echo "<table>";
for ($i = 1; $i<19; $i++ )
{
	$create_time = "2015-05-".sprintf('%02d', $i);
	
	//$lower_time = $create_time-24*3600*30;
	$sql_str = "SELECT cameraman_phone,order_id,audit_time from pai_user_library_db.model_oa_order_tbl
	WHERE audit_status='pass' AND source = 4 AND FROM_UNIXTIME(audit_time,'%Y-%m-%d') = '{$create_time}'";
	$ret = db_simple_getdata($sql_str,false,101);
	/* print_r($ret);
	exit; */
	if(!is_array($ret)) $ret = array();
	$show_total = 0;
	$time_total = 0;
	foreach ($ret as $key=>$val)
	{
		$from_date_id = $user_obj->get_user_id_by_phone($val['cameraman_phone']);
		$info = get_detail_ret($val['order_id'],$from_date_id,$create_time);
		if($info)
		{
		    $time_total += $info;
			//$show_total +=1;
		}
	}
	unset($ret);
	//$avg_time = intval($time_total/$show_total);
	echo "<tr>";
	echo "<td>$create_time</td>";
			echo "<td>{$time_total}</td>";
			echo "</tr>";
}
echo "</table>";	
 
 function get_detail_ret($order_id,$from_date_id,$create_time)
 {
 	$sql_str = "SELECT add_time,user_id as to_date_id from pai_user_library_db.model_oa_enroll_tbl 
 	           WHERE order_id = {$order_id}";
 	//echo $sql_str;
 	$ret = db_simple_getdata($sql_str,false,101);
 	if(!is_array($ret) || empty($ret)) return false;
 	//print_r($ret);
 	$total_count = 0;
 	foreach ($ret as $key=>$val)
 	{
 		$overtime = $val['add_time']+48*3600;
 		$sql_date_str = "SELECT sum(budget) as c from event_db.event_date_tbl e,event_db.event_details_tbl d WHERE
 		e.to_date_id ={$val['to_date_id']}  AND e.from_date_id = {$from_date_id}  AND e.add_time >= {$val['add_time']} 
 		AND e.add_time <= {$overtime} AND e.date_status='confirm' AND d.event_status ='2' AND d.event_id=e.event_id";
 		$date_ret = db_simple_getdata($sql_date_str, true);
 		$total_count += intval($date_ret['c']);
 		//exit;
 		unset($date_ret);
 	}
 	unset($ret);
 	//echo $total_count;
 	return $total_count;
 	
 }
  

?>