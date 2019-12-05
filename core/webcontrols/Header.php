<?php
/**
 * 框架 默认头部控件
 * 
 * @param $attribs 控件的参数
 * 
 * @return string
 */

function _ctlHeader($attribs)
{
	ob_start();
	//include_once("/disk/data/htdocs233/poco_main/index_include_v2/header_lower.php");
	$_MYPOCO_APP_DEFAULT_TOP_HTML = ob_get_contents();
	ob_clean();
	
	/**
	 * 替换一吓返回地址
	 * http://www1.poco.cn/login.php?locate=http%3A//www.poco.cn 登录
	 * http://www1.poco.cn/reg1/reg.php?referer=http%3A//www.poco.cn 注册
	 */
	$app_name = $attribs['app_name'];
	if(!empty($app_name))
	{
		$search = array(
		'http://www1.poco.cn/login.php?locate=http%3A//www.poco.cn',
		'http://www1.poco.cn/reg1/reg.php?referer=http%3A//www.poco.cn',	
		);
		
		// 取出应用网址
		$app_url = $attribs['app_url'];
		
		$replace = array(
		'http://www1.poco.cn/login.php?locate='.urlencode($app_url),
		'http://www1.poco.cn/reg1/reg.php?referer='.urlencode($app_url),
		);
		
		$_MYPOCO_APP_DEFAULT_TOP_HTML = str_replace($search, $replace, $_MYPOCO_APP_DEFAULT_TOP_HTML);
	
		/**
		 * 添加统计代码
		 */
		$_MYPOCO_APP_DEFAULT_TOP_HTML.= "
		<!-- log start -->
		<script language=\"javascript\">
		__poco_site_stat_request_filename = 'mypoco_apps_".$app_name.".css';
		</script>
		<script type=\"text/javascript\" src=\"http://www1.poco.cn/site_stat_code.js\" ></script>
		<!-- log end; -->";
	}
	
	return $_MYPOCO_APP_DEFAULT_TOP_HTML;
}

?>