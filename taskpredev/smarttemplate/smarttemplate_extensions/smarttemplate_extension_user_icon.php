<?php

	/**
	* SmartTemplate Extension substr
	* Insert specific part of a string
	*
	* Usage Example:
	* Content:  $template->assign('HEADLINE', 'My Title');
	* Template: <font size=4>{substr:HEADLINE,0,1}</font>{substr:HEADLINE,1}
	* Result:   <font size=4>M</font>y Title
	*
	* @author Philipp v. Criegern philipp@criegern.com
	*/
	function smarttemplate_extension_user_icon ( $user_id, $size=86,$show_time_cache=false )
	{
		$len = strlen($param);
		if ($len <= $sublength){
			$param = $param;
		}else{
			$param = substr($param,"0",$sublength); 
			$parity= 0;
			for($j=0;$j<$sublength;$j++){ 
				$temp_str=substr($param,$j,1); 
					if(Ord($temp_str)>127) $parity+=1; 
			} 
			if($parity%2==1) {
				$param=substr($param,0,($sublength-1)); 
			} else {
				$param=substr($param,0,$sublength); 
			}
		}
		return $param;
	}

?>