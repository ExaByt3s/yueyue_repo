<?php
/**
 * ÖÐ¼äÌø×ªÒ³
 * ²¹³äÁ´½Ó»º´æ°æ±¾ºÅ
 * 
 * @author Henry
 * @copyright 2014-12-24
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$mode = trim($_INPUT['mode']);
if( strlen($mode)<1 ) $mode = 'wx';

$version_control = include('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
$url = trim($version_control[$mode]['page_url']);
if( strlen($url)>0 )
{
	$route = trim($_INPUT['route']);
	if( strlen($route)>0 ) $url .= '#' . $route;
	
	header('Location:' . $url);
}
