<?php


//include_once ('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
/**
 * 报表数据
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年5月8日
 * @version 1
 */
 

/**
 * 外拍流水
 */

$user_id = $_INPUT['user_id'] ? $_INPUT['user_id'] : 100832;

$poco_id = get_relate_poco_id($user_id);
$num   = 0;
$price = 0;
$sql_str = "SELECT event_id,budget from event_db.event_details_tbl where user_id={$poco_id} AND FROM_UNIXTIME(complete_time,'%Y-%m') ='2015-03'
AND type_icon='photo' AND new_version=2 AND event_status='2'";
$result = db_simple_getdata($sql_str, FALSE);

if(!is_array($result)) return false;

	foreach ($result as $vo) 
	{
		$budget = $vo['budget'];
		$sql_order_str = "SELECT enroll_id,user_id,event_id,enroll_time as date_add_time,phone,enroll_num,pay_time,pay_status,table_id,original_price,discount_price,is_use_coupon
				FROM event_db.event_enroll_tbl WHERE event_id ={$vo['event_id']}";
	    $ret = db_simple_getdata($sql_order_str,false);
	    if(!is_array($ret)) $ret = array();
	    foreach ($ret as $key => $val) 
	    {
	    	$from_date_id = get_user_id_by_poco_id($val['user_id']);
	    	$info = get_actity_code($val['enroll_id']);
	    	if($info)
	    	{
	    		$price  += $budget * $val['enroll_num'];
	    		$num += 1;
	    	}

	    }
	}
	echo "成交订单数为{$num},价钱为{$price}";

    function get_user_id_by_poco_id($poco_id=0)
	{
		$poco_id = intval($poco_id);
		if($poco_id <1) return 0;
		$sql_str = "SELECT user_id FROM pai_db.pai_relate_poco_tbl WHERE poco_id = {$poco_id}";
		$ret = db_simple_getdata($sql_str,true,101);
		if(!is_array($ret)) return 0;
		return intval($ret['user_id']);
	}

	function get_actity_code($enroll_id)
	{
		$sql_code_str = "SELECT enroll_id,event_id,update_time,is_checked FROM pai_db.pai_activity_code_tbl WHERE enroll_id={$enroll_id} AND is_checked=1";
		$ret = db_simple_getdata($sql_code_str,false,101);
		if(!is_array($ret) || empty($ret))
		{
			return false;
		}
		return true;
	}

?>