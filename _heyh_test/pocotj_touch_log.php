<?php

var_dump(yueyuetj_touch_log('goods_search', ''));

/**
 * ͨ���������ֹ�����һ��imgtj��ͳ�ƣ�ͳһ����CSS��yueyue_touch.css
 *
 * @param string $type  //���������磺��վע�� /reg/web
 * @param string $query //���Ӳ���
 * @param array $ext_info_arr //���ӵ����ԣ����飬cookie/referer_outside
 */
function yueyuetj_touch_log($type, $query, $ext_info_arr = null, $_attemper_server_id = 0, $b_save_log = false)
{
	if(defined('G_NOT_TOUCH_SITE_STAT_LOG'))
	{
		return false;
	}

	$rand_str .= "touch=1&ip_addr={$ip_addr}&tmp=" . md5(uniqid("r", true));

	//ip
	global $_G_TOUCH_SITE_STAT_LOG_BY_RAND_IP,$ibforums;

	if($_G_TOUCH_SITE_STAT_LOG_BY_RAND_IP)
	{
		$ip_addr = rand(0, "4294967295");
	}
	else
	{
		$ip_addr = ip2long($ibforums->input['IP_ADDRESS']);
		$ip_addr = sprintf("%u", $ip_addr);
	}

	$tj_type_str = "touch://";
	$type = trim($type, '/');
	$tj_type_str .= $type;

	if($query) $tj_type_str .= '?' . $query;

	//�ⲿ��Դ
	if (!isset($ext_info_arr["referer_outside"]))
	{
		if (!empty($_SERVER["HTTP_REFERER"]))
		{
			$referer_str = '&referer_outside=' . urlencode($_SERVER["HTTP_REFERER"]);
		}
	}

	//cookie
	$cookie_arr = $_COOKIE;
	if (isset($ext_info_arr["cookie"]))
	{
		$cookie_arr = $ext_info_arr["cookie"];
	}

	if (!empty($cookie_arr) && is_array($cookie_arr))
	{
		$cookie_header_str = "";
		foreach ($cookie_arr as $k => $v)
		{
			$cookie_header_str .= "{$k}=" . urlencode($v) . ";";
		}
	}

	$_site_stat_req_url = "http://imgtj.yueus.com/yueyue_touch.css?url={$tj_type_str}&{$rand_str}{$referer_str}";

	$param_str = "";
	if (!empty($cookie_header_str))
	{
		$param_str = "--header 'Cookie:{$cookie_header_str}'";
	}

	$cmd = "wget --spider --tries=1 --timeout=10 '{$_site_stat_req_url}' {$param_str} -O /dev/stdout -o /dev/stdout";

	/**
 * ��attemper���첽
 */
	include_once ("/disk/data/htdocs232/poco/pai/system_service/attemper_service/yueyue_attemper_service_class.inc.php");
	$yueyue_attemper_service_obj = new yueyue_attemper_service_class();
	$attemper_task_id = $yueyue_attemper_service_obj->add_task_shell($cmd, 0, $b_save_log, true);

	return $attemper_task_id;

}


?>