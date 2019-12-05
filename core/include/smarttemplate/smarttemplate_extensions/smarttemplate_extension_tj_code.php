<?php

	/**
	* SmartTemplate Extension tj_code
	* 输出poco统计代码
	*
	* Usage Example:
	* Template: {tj_code:'aaa.css'}
	*/
	function smarttemplate_extension_tj_code ( $request_filename='poco_tj.css', $b_hot_map_tj=true )
	{
		if(empty($request_filename)) $request_filename = 'poco_tj.css';

		$str = "<!-- new log start -->\n";

		$str .="<script>__poco_site_stat_request_filename = '{$request_filename}';</script>\n";
		$str .="<script src='http://www1.poco.cn/site_stat_code.js'></script>\n";

		if($b_hot_map_tj) $str .= "<script src='http://www1.poco.cn/poco_log_analyser/js/site_stat_clickheat.js'></script>\n";

		$str .= "<!-- new log end; -->";

		return $str;

	}

?>