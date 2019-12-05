<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$limit              = $client_data['data']['param']['limit'];

$topic_obj = POCO::singleton ('pai_topic_class');

$where = ' is_effect = 1 ';
$count = $topic_obj->get_topic_list(true, $where);

$data['count'] = $count;

$result = $topic_obj->get_topic_list(FALSE, $where, 'sort DESC', $limit);
foreach($result AS $key=>$val)
{
    $topic_val['cover_image'] = $val['cover_image'];
    $topic_val['url']         = 'http://yp.yueus.com/mobile/app?from_app=1#topic/' . $val['id'];
    $topic_val['url_wifi']    = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/' . $val['id'];
    $topic_val['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($topic_val['url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $topic_val['url']));
    unset($topic_val['url_wifi']);

    $data['list_topic'][]     = $topic_val;
    
}

$options['data'] = $data;
$cp->output($options);
?>