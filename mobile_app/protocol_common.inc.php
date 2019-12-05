<?php
// 将图片换成bgp线路
define('POCO_CONTENT_OUPUT_BGP_PARSER_LINK', 1);
//include_once("/disk/data/htdocs233/pic2/mypoco/mtmpfile/API/poco_protocol/poco_communication_protocol_class.inc.php");
$yue_protocol_path = str_replace('\\', '/', dirname(dirname(__FILE__))) . '/protocol/';
require($yue_protocol_path . 'yue_protocol.inc.php');
?>