<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$go_where = trim($_INPUT['g']);

switch($go_where)
{
	case 'czdt':
		$go_url = 'http://yp.yueus.com/mobile/m2/recharge/card.php';
		break;
}

header("Location: {$go_url}");
?>