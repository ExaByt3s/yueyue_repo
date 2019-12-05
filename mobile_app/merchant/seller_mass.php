<?php
/**
 * 群发接口
 *
 * @author willike
 * @since 2015/11/12
 */
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];  // 用户ID
$activity_id = $client_data['data']['param']['activity_id'];   // 活动ID
$stage_id = $client_data['data']['param']['stage_id'];   // 场次ID
$content = $client_data['data']['param']['content'];   // 群发内容
if (empty($user_id) || empty($activity_id) || empty($stage_id)) {
    $options['data'] = array(
        'result' => 0,
        'message' => '活动场次为空',
    );
    return $cp->output($options);
}
if (empty($content)) {
    $options['data'] = array(
        'result' => -1,
        'message' => '群发内容为空',
    );
    return $cp->output($options);
}
$batch_message_obj = POCO::singleton('pai_mall_activity_batch_message_class');
$result = $batch_message_obj->send_message($user_id, $activity_id, $stage_id, $content);

$options['data'] = $result;
return $cp->output($options);
