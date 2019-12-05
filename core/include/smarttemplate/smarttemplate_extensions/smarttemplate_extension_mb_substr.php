<?php

	/**
	* SmartTemplate Extension mailto

	*/
	function smarttemplate_extension_mb_substr ( $str, $start=0, $len=10, $encoding='GBK'  )
	{
		return mb_substr($str, $start, $len, $encoding);
	}

?>