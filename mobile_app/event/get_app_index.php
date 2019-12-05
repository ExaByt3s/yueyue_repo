<?php

define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id        = $client_data['data']['param']['location_id'];
$user_id            = $client_data['data']['param']['user_id'];

$data['pid'] = '';
$data['mid'] = '';


$data['button_1']['str']    = 'ģ����Լ';
$data['button_1']['dmid']   = '';
$data['button_1']['url']    = 'yueyue://goto?type=inner_app&pid=1220005';

$data['button_2']['str']    = '��Ӱ��ѵ';
$data['button_1']['dmid']   = '';
$data['button_2']['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=' . urlencode('��Ӱ��ѵ');

$data['button_3']['str']    = 'Ӱ����ƾ';
$data['button_1']['dmid']   = '';
$data['button_3']['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=' . urlencode('Ӱ����ƾ');

$data['button_5']['str']    = '��ױ����';
$data['button_1']['dmid']   = '';
$data['button_5']['url']    = 'yueyue://goto?type=inner_app&pid=1220094&query=' . urlencode('��ױ����');

$data['button_6']['str']    = '����';
$data['button_1']['dmid']   = '';
$data['button_6']['url']    = 'yueyue://goto?type=inner_app&pid=1220076';


$cms_obj        = new cms_system_class();
$key            = 196;
$list_info      = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number DESC', $key);

foreach($list_info AS $val)
{
    $banner['str'] = $val['title'];
    $banner['img'] = $val['img_url'];
    if($val['link_type'] != 'inner_web')
    {
        $banner['url'] = $val['link_url'];
    }else{
        $banner['url'] = "yueyue://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
    }
    $data['banner_list'][] = $banner;

}
$options['data'] = $data;

$cp->output($options);
?>