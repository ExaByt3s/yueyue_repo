<?php
/**
 * ��� Ĭ��ͷ���ؼ�
 * 
 * @param $attribs �ؼ��Ĳ���
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
	 * �滻һ�ŷ��ص�ַ
	 * http://www1.poco.cn/login.php?locate=http%3A//www.poco.cn ��¼
	 * http://www1.poco.cn/reg1/reg.php?referer=http%3A//www.poco.cn ע��
	 */
	$app_name = $attribs['app_name'];
	if(!empty($app_name))
	{
		$search = array(
		'http://www1.poco.cn/login.php?locate=http%3A//www.poco.cn',
		'http://www1.poco.cn/reg1/reg.php?referer=http%3A//www.poco.cn',	
		);
		
		// ȡ��Ӧ����ַ
		$app_url = $attribs['app_url'];
		
		$replace = array(
		'http://www1.poco.cn/login.php?locate='.urlencode($app_url),
		'http://www1.poco.cn/reg1/reg.php?referer='.urlencode($app_url),
		);
		
		$_MYPOCO_APP_DEFAULT_TOP_HTML = str_replace($search, $replace, $_MYPOCO_APP_DEFAULT_TOP_HTML);
	
		/**
		 * ���ͳ�ƴ���
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