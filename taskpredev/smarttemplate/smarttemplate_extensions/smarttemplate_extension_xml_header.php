<?php

	/**
	* SmartTemplate Extension 
	*/
	function smarttemplate_extension_xml_header ( $encoding="UTF-8" )
	{
		header('Content-type: text/xml');
		return "<?xml version=\"1.0\" encoding=\"$encoding\" ?>\n";
	}

?>