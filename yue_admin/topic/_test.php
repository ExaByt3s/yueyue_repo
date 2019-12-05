<?php
include_once 'common.inc.php';


$topic_obj = POCO::singleton('pai_topic_class');

$ret=$topic_obj->get_topic_tpl_list(667);

$new_ret = poco_iconv_arr($ret,'GBK', 'UTF-8');

echo json_encode($new_ret);

?>