<?php

	/**
	* SmartTemplate Extension yueus_img_resize
	*
	* Usage Example:
	* Content:  $template->assign('img_url', 'xxx_640.jpg');
	* Template: {yueus_img_resize:img_url,'165'}
	* Result:   xxx_165.jpg
	*
	*/
	function smarttemplate_extension_yueus_img_resize($img_url, $size='')
	{
		if (in_array ( $size, array (32,64,86,100,145,165,260,320,440,468,640 ) ))
		{
			$size_str = '_' . $size;
		}
		else
		{
			$size_str = '';
		}
		
		if (preg_match ( "/_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i", $img_url ))
		{
			$img_url = preg_replace ( "/_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i", "{$size_str}.$2", $img_url );
		} 
		else
		{ 
			$img_url = preg_replace ( '/.(jpg|png|jpeg|gif|bmp)/i', "{$size_str}.$1", $img_url ); 
		}
		
		return $img_url;
	}

?>