<?php

	/**
	* 移动的meta标签和跳转处理
	*/
	function smarttemplate_extension_wap_meta ( $url, $param="" )
	{

		$html = '<meta name="mobile-agent" content="format=xhtml;url='.$url.'">
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="'.$url.'"/> 
<script language="JavaScript" src="http://www.poco.cn/js_common/common/client.js"></script>
<script language="JavaScript">(function(n){var cd=window.Client.device;if(document.cookie.indexOf("pc_read")==-1&& cd && cd.name!="mac" && cd.name!="pc" && cd.name!="ipad"){window.location.href=n}})("'.$url.'")</script>';
		return $html;
	}

?>