<?php
/**
 * UBB PHPת��
 */

if (!function_exists("__transform_emotion_ubb"))
{
	function __transform_emotion_ubb($str, $is_18_size = false , $is_single_img = false)
	{
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[�ɰ�]","/�ɰ�"), "emotion_class"=>"lovely");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[΢Ц]","/��Ц"), "emotion_class"=>"smile");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��Ƥ]","/��Ƥ"), "emotion_class"=>"tricky");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]"), "emotion_class"=>"wink");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/ʾ��"), "emotion_class"=>"throw-kiss");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ɫ]","/ɫ"), "emotion_class"=>"colour");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"daze");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ʧ��]","/�ѹ�"), "emotion_class"=>"lose-hope");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/��Ц"), "emotion_class"=>"sinister");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[�亹]"), "emotion_class"=>"cold-sweat");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"sweat");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[����]", "emotion_class"=>"fall-ill");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[˥]", "emotion_class"=>"wane");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[˯��]","/˯"), "emotion_class"=>"sleep");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"hee-hee");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ץ��]","/��ĥ"), "emotion_class"=>"crazy");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��]","/��"), "emotion_class"=>"giddy");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/���"), "emotion_class"=>"tears");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[��Ц]", "emotion_class"=>"laugh");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ŭ]","/��ŭ"), "emotion_class"=>"angry");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��]","/����"), "emotion_class"=>"surprise");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��]","/ǿ"), "emotion_class"=>"praise");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ʤ��]","/ʤ��"), "emotion_class"=>"win");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"clap");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[ʾ��]", "emotion_class"=>"love");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[����]", "emotion_class"=>"benevolence");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[����]", "emotion_class"=>"photograph");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[õ��]","/õ��"), "emotion_class"=>"rose");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[����]", "emotion_class"=>"good-luck");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>"[��ף]", "emotion_class"=>"celebration");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[�ܶ�]","/�ܶ�"), "emotion_class"=>"fight");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��ߺ�]","/��ߺ�"), "emotion_class"=>"zuo-Hengheng");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"kiss");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��]","/��"), "emotion_class"=>"weak");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ȭͷ]","/ȭͷ"), "emotion_class"=>"fist");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��̫��]","/��̫��"), "emotion_class"=>"zuo-Taiji");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[��̫��]","/��̫��"), "emotion_class"=>"you-Taiji");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[OK]"), "emotion_class"=>"ok");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"heart-broken");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[̫��]","/̫��"), "emotion_class"=>"sun");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"moon");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"lightning");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"coffee");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"cake");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ơ��]","/ơ��"), "emotion_class"=>"beer");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"gift");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[����]","/����"), "emotion_class"=>"football");
		$G_IBF_EMOTIONS_ARR[] = array("code"=>array("[ը��]","/ը��"), "emotion_class"=>"bomb");


		
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