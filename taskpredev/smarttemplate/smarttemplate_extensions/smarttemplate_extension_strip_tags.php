<?php

	/**
	* SmartTemplate Extension trim
	* Removes leading and trailing Whitespaces and Line Feeds
	*
	* Usage Example:
	* Content:  $template->assign('LINK', ' Click Here  ');
	* Template: <a href="/">{trim:LINK}</a>
	* Result:   <a href="/">Click Here</a>
	*
	* @author Philipp v. Criegern philipp@criegern.com
	*/
	function smarttemplate_extension_strip_tags ( $param, $allowable_tags=null )
	{
		return strip_tags($param ,$allowable_tags);
	}

?>