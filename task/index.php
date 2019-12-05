<?php

/**
 * еп╤о©м╩╖╤к
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;

if($__is_weixin || $__is_android || $__is_iphone) 
{
	header("Location: ./m/list.php");
}
else
{
	header("Location: ./lead_list.php");
} 

?>