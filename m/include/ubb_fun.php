<?php
/**
 * UBB PHP×ª»»
 */

if (!function_exists("__transform_emotion_ubb"))
{
	function __transform_emotion_ubb($str, $is_18_size = false , $is_single_img = false)
	{
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[¿É°®]","/¿É°®"), "emotion_class"=>"lovely");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Î¢Ð¦]","/º©Ð¦"), "emotion_class"=>"smile");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[µ÷Æ¤]","/µ÷Æ¤"), "emotion_class"=>"tricky");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[¼·ÑÛ]"), "emotion_class"=>"wink");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[·ÉÎÇ]","/Ê¾°®"), "emotion_class"=>"throw-kiss");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[É«]","/É«"), "emotion_class"=>"colour");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[·¢´ô]","/·¢´ô"), "emotion_class"=>"daze");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ê§Íû]","/ÄÑ¹ý"), "emotion_class"=>"lose-hope");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÒõÏÕ]","/»µÐ¦"), "emotion_class"=>"sinister");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Àäº¹]"), "emotion_class"=>"cold-sweat");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Á÷º¹]","/Á÷º¹"), "emotion_class"=>"sweat");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[Éú²¡]", "emotion_class"=>"fall-ill");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[Ë¥]", "emotion_class"=>"wane");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ë¯¾õ]","/Ë¯"), "emotion_class"=>"sleep");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÎûÎû]","/ßÚÑÀ"), "emotion_class"=>"hee-hee");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[×¥¿ñ]","/ÕÛÄ¥"), "emotion_class"=>"crazy");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÔÎ]","/ÔÎ"), "emotion_class"=>"giddy");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Á÷Àá]","/´ó¿Þ"), "emotion_class"=>"tears");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[´óÐ¦]", "emotion_class"=>"laugh");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Å­]","/·¢Å­"), "emotion_class"=>"angry");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[¾ª]","/¾ªÑÈ"), "emotion_class"=>"surprise");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÔÞ]","/Ç¿"), "emotion_class"=>"praise");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ê¤Àû]","/Ê¤Àû"), "emotion_class"=>"win");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÅÄÕÆ]","/¹ÄÕÆ"), "emotion_class"=>"clap");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[Ê¾°®]", "emotion_class"=>"love");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[°®ÐÄ]", "emotion_class"=>"benevolence");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[ÅÄÕÕ]", "emotion_class"=>"photograph");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ãµ¹å]","/Ãµ¹å"), "emotion_class"=>"rose");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[ÐÒÔË]", "emotion_class"=>"good-luck");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[Çì×£]", "emotion_class"=>"celebration");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[·Ü¶·]","/·Ü¶·"), "emotion_class"=>"fight");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[×óºßºß]","/×óºßºß"), "emotion_class"=>"zuo-Hengheng");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ç×Ç×]","/Ç×Ç×"), "emotion_class"=>"kiss");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Èõ]","/Èõ"), "emotion_class"=>"weak");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[È­Í·]","/È­Í·"), "emotion_class"=>"fist");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[×óÌ«¼«]","/×óÌ«¼«"), "emotion_class"=>"zuo-Taiji");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÓÒÌ«¼«]","/ÓÒÌ«¼«"), "emotion_class"=>"you-Taiji");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[OK]"), "emotion_class"=>"ok");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÐÄËé]","/ÐÄËé"), "emotion_class"=>"heart-broken");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Ì«Ñô]","/Ì«Ñô"), "emotion_class"=>"sun");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÔÂÁÁ]","/ÔÂÁÁ"), "emotion_class"=>"moon");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÉÁµç]","/ÉÁµç"), "emotion_class"=>"lightning");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[¿§·È]","/¿§·È"), "emotion_class"=>"coffee");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[µ°¸â]","/µ°¸â"), "emotion_class"=>"cake");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Æ¡¾Æ]","/Æ¡¾Æ"), "emotion_class"=>"beer");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ÀñÎï]","/ÀñÎï"), "emotion_class"=>"gift");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[×ãÇò]","/×ãÇò"), "emotion_class"=>"football");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[Õ¨µ¯]","/Õ¨µ¯"), "emotion_class"=>"bomb");


		
		foreach ( $G_IBF_EMOTIONS_ARR as $key=>$emotion_info )
		{
			if($is_single_img)
			{
				$str = str_ireplace($emotion_info['code'],"<img style='width:20px;' border=0 src=\"http://m.poco.cn/mobile/icon/{$emotion_info['emotion_class']}.png\" />",$str);
			}
			else
			{
				if($is_18_size)
				{
					$str = str_ireplace($emotion_info['code'],"<expression class='expression-16-{$emotion_info['emotion_class']}'></expression>",$str);
				}
				else
				{
					$str = str_ireplace($emotion_info['code'],"<emotion class='emotion-16-{$emotion_info['emotion_class']}'></emotion>",$str);
				}
			}
		}

		return $str;
	}
}
?>