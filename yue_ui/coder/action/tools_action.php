<?php
/**
 * ���Թ�����
 */

$_COOKIE['test_mode_qing_demo'] = 1;  //��ͨ���Ȩ��

//����Ӧ�ù����ļ�
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

(int)$_REQUEST['realtime'] == 1 && define("G_DB_GET_REALTIME_DATA",1);

//if (!in_array($login_id, array(52740577,7734111,44634066,37374003,52118133,65849144,173480363,173793838,173718999,54761328,64974092))) die('��Ȩ�޲���');

$user_arr = array(
	100000,//����
	100004,//����
);

if (!in_array($yue_login_id, $user_arr)) 
die('��Ȩ�޲���');

//define('runcode', 1);
$text = $_REQUEST['text'] ? $_REQUEST['text'] : '';

$text = urldecode($text);

$text = mb_convert_encoding($text,'gbk','utf-8');

preg_match("/define\('runcode', 1\);/", $text) && eval($text);
if($login_id == 65849144 && $text == '') $text = "define('runcode', 1);";

$str = <<<EOF
EOF;

echo $str;