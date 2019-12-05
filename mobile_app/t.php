<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('protocol_common.inc.php');

$fulltext_obj = POCO::singleton('pai_fulltext_class');
$fulltext_obj->cp_data();

?>