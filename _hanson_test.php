<?php
require_once('poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

var_dump($G_CLASS_FILES);



echo "<hr>";

var_dump($fp);

$arr = array(1,2,3);
$fp = new FirePHP;
$fp->fb($arr);

//$firePhp->fb($arr);

