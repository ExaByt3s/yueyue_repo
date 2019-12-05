<?php
/**
 * common ģ��
 * 
 */

class app_common_class
{
	/**
	 * ���캯��
	 *
	 */
	public function __construct() 
	{
		
	}
	
	/**
	 * cdn�������
	 */
	public function content_output_cdn_parser($content)
	{
		return $content;
	}
	
	/**
	 * ˢ��cdn
	 */
	public function refresh_urls_cache($url)
	{
		return true;
	}
	
	/**
	 * ͨ��������ȡ����ID
	 */
	public function get_location_2_location_id($location)
	{
		if(!defined('LOCATION_SYS')) 
		{
			include(G_POCO_APP_PATH . '/../system_service/poco_location_ip_lib/location_common.inc.php');
		}
		
		$location_obj = new location_conf_class();
		return $location_obj->get_location_2_location_id($location);
	}
	
	/**
	 * ͨ��IP��ȡ����ID
	 * @param string $ip
	 * @return array
	 */
	public function get_ip_location_info($ip)
	{
		if ($GLOBALS['_POCO_PAGE_CACHE'][__CLASS__.'::'.__FUNCTION__][$ip]) 
		{
			return $GLOBALS['_POCO_PAGE_CACHE'][__CLASS__.'::'.__FUNCTION__][$ip];
		}
		
		if(!defined('LOCATION_SYS')) 
		{
			include(G_POCO_APP_PATH . '/../system_service/poco_location_ip_lib/location_common.inc.php');
		}
		$location_obj = new location_conf_class();
		if (preg_match("/^\d+$/",$ip))
		{
			$ip = long2ip($ip);
		}
		
		$ret = $location_obj->get_location_ip_2_location($ip);
		
		$GLOBALS['_POCO_PAGE_CACHE'][__CLASS__.'::'.__FUNCTION__][$ip] = $ret;
		
		return $ret;
	}
	
}

?>