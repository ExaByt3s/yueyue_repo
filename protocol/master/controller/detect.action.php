<?php
/**
 * Created by PhpStorm.
 * User: willike
 * Date: 2015/9/23
 * Time: 14:25
 */
defined('PROTOCOL_MASTER_ROOT') or die('ERROR: detect error!');

$layout_file = PROTOCOL_MASTER_ROOT . 'view/layout.tpl.html';
if (!file_exists($layout_file)) {
	exit('Detect System Error: pls contact willike!');
}
$layout_content = file_get_contents($layout_file);
$fr = filter_input(INPUT_GET, 'fr');
if (!empty($fr)) {
	$fr_file = PROTOCOL_MASTER_ROOT . 'view/' . $fr . '.tpl.html';
	$fr_content = file_get_contents($fr_file);
	$layout_content .= $fr_content;
}
if (empty($layout_content)) {
	exit('Detect System Error: pls contact willike.');
}
$host_url = 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST');
$dir_url = $host_url . '/protocol/master/';

$matchs = array();
preg_match_all('/(src="(.*?)")|(href="(.*?)")/i', $layout_content, $matchs);
$url_list = array();
if (!empty($matchs)) {
	// 有匹配项
	foreach ($matchs[0] as $url) {
		$url = trim(str_replace(array('href=','src='), '', $url),'"');
		if(empty($url)){
			continue;
		}
		if (preg_match('/^http(s)?:\/\//i', $url)) {
			$url = $url;
		} else if (preg_match('/^\/\/\w+/', $url)) {
			$url = 'http:' . $url;
		} else if (preg_match('/^\/\w+/', $url)) {
			$url = $host_url . $url;
		} else {
			$url = $dir_url . $url;
		}
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			continue;
		}
		if(preg_match('/\.js(\??(\w+\&?=?\w*)*)?$/i', $url)){
			$url_list['js'][] = $url;	
		}elseif(preg_match('/\.css(\??(\w+\&?=?\w*)*)?$/i', $url)){
			$url_list['css'][] = $url;
		}
	}
}
return array('url_list' => $url_list);
