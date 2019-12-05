<?php

/**
 * Í³¼Æ×´Ì¬
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-7-24
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];

$options['data'] = array(
    'baidu_tongji' => 1,
    
);
return $cp->output($options);
