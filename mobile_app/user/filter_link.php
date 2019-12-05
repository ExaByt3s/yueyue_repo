<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
//include_once("../protocol_common.inc.php");
//
//$cp = new poco_communication_protocol_class();
//$client_data = $cp->get_input();
//
//$user_id        = $client_data['data']['param']['user_id'];
//$access_token   = $client_data['data']['param']['access_token'];
//$url            = $client_data['data']['param']['url'];

$url = 'http://www.yueus.com/event/45368?share_event_id=45368&ph=14453271241';
$url_array = parse_url($url);
print_r($url_array);

$url = 'http://www.yueus.com/share_card/101507';
$url_array = parse_url($url);
print_r($url_array);

//$options['data'] = $data;
//$cp->output($options);
?>