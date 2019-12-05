<?php

	/**
	* SmartTemplate Extension urlencode
	* Inserts URL-encoded String
	*
	* Usage Example:
	* Content:  $template->assign('PARAM', 'Delete User!');
	* Template: go.php?param={urlencode:PARAM}
	* Result:   go.php?param=Delete+User%21
	*
	* @author Philipp v. Criegern philipp@criegern.com
	*/
	function smarttemplate_extension_nocdn ( $param )
	{
		if(stripos($param,'http://')===0 && (defined("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK") && G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK))
		{
			define('G_SMARTTEMPLATE_REPLACE_NOCDN_URL',1);
			$param = preg_replace("/^http:\/\//i", '__http-poco-nocdn__://', $param);
		}
		
		return $param;

	}

?>