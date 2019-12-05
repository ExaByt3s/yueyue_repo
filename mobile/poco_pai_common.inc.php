<?php

//定义默认cookie域，例如用于登录、退出
if( preg_match('/(^|\.)yueus\.com$/isU', $_SERVER['SERVER_NAME']) )
{
	define('G_LOGIN_SESSION_PARTICULAR_COOKIE_DOMAIN', 'yueus.com');
}

// 因为现在有些头像显示不出，要查问题，所以去掉cdn先,测试完就会去掉 
//define('G_USER_ICON_DONT_USE_CDN_URL',1);

// 支持 REST
$method = $_SERVER['REQUEST_METHOD']; 
if ($method == 'PUT' || $method == 'DELETE') {
    parse_str(file_get_contents('php://input'), $params);
    $GLOBALS['_'.$method] = $params;

    foreach ($params as $key=>$val) {
        $_REQUEST[$key] = $val;
        $HTTP_GET_VARS[$key] = $val;
        //$HTTP_POST_VARS[$key] = $val;
    }
}
unset($method, $params, $key, $val);

//开启zlib拓展
define('G_USE_ZLIB_OB_GZHANDLER', 1);

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/include/pai_function.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
include_once("/disk/data/htdocs232/poco/pai/mobile/include/ubb_fun.php");

include_once('/disk/data/htdocs232/poco/pai/enroll_act.php');
include_once('/disk/data/htdocs232/poco/pai/event_act.php');
include_once('/disk/data/htdocs232/poco/pai/date_act.php');



$GLOBALS['pai_app'] = $my_app_pai;

$action_version = str_replace('/mobile/','',$_SERVER['REDIRECT_SCRIPT_URL']);

//define('G_PAI_ECPAY_DEV',1);

define('G_PAI_APP_VERSION','webbeta');



if (!defined('G_PAI_APP_PATH')) 
{
    define('G_PAI_APP_PATH', realpath(dirname(__FILE__)).'/');

}

/**
 * 域名控制
 */
//define('G_PAI_APP_DOMAIN', 'http://pai.poco.cn');
define('G_PAI_APP_DOMAIN', 'http://yp.yueus.com');


// 测试使用账户 hudw 2014.11.4
define('TEST_USER_ACCOUNT',serialize(array(175321481,173718999,173718999,66096046)));
//海todo 现使用yue_login_id  这里以后可以要改成用yue_login_id 而不是login_id
if ($yue_login_id>0)
{
	define('G_DB_GET_REALTIME_DATA', 1);// 如果是当前用户，实时数据
}


/**
 * 通过程序来手工请求一下imgtj的统计，统一请求CSS：pocotj_touch.css
 *
 * @param string $type  //参数，例如：网站注册 /reg/web
 * @param string $query //附加参数
 * @param array $ext_info_arr //附加的属性，数组，cookie/referer_outside
 */
if (!function_exists('yueyue_touch_log'))
{
	function yueyue_touch_log($type, $query, $ext_info_arr = null, $_attemper_server_id = 1, $b_save_log = false)
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

		//外部来源
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

		$_site_stat_req_url = "http://imgtj.poco.cn/pocotj_touch.css?url={$tj_type_str}&{$rand_str}{$referer_str}";

		$param_str = "";
		if (!empty($cookie_header_str))
		{
			$param_str = "--header 'Cookie:{$cookie_header_str}'";
		}

		$cmd = "wget --spider --tries=1 --timeout=10 '{$_site_stat_req_url}' {$param_str} -O /dev/stdout -o /dev/stdout";

		/**
         * 用attemper做异步，需要换成新方式
         */
		//include_once ("/disk/data/htdocs233/mypoco/poco_attemper_service/poco_attemper_service_class.inc.php");
		//$poco_attemper_service_obj = new poco_attemper_service_class($_attemper_server_id);
		//$attemper_task_id = $poco_attemper_service_obj->add_task_shell($cmd, 0, $b_save_log, true);
		
		
		return $attemper_task_id;

	}
}

if(!function_exists('yue_convert_weixin_topic_url'))
{
	function yue_convert_weixin_topic_url($url)
	{
		echo G_PAI_APP_PAGE_MODE;
	}
}
