<?php

/**
 * �Ż�ȯ �������ƽӿ�
 * 
 * @author chenweibiao<chenwb@yueus.com>
 * @since 2015-9-6
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$request_times = intval($client_data['data']['param']['request_times']);  // �������
$os_version = $client_data['data']['param']['os_version'];  // ϵͳ�汾
// ios7���£�itms-apps://ax.itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?type=Purple+Software&id= 935185009
// ios7���ϣ�https://itunes.apple.com/us/app/yue-yue-zui-gao-xiao-she-ying/id935185009?l=zh&ls=1&mt=8

$urls = array(
    '6' => 'itms-apps://ax.itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?type=Purple+Software&id= 935185009',
    '7' => 'https://itunes.apple.com/us/app/yue-yue-zui-gao-xiao-she-ying/id935185009?l=zh&ls=1&mt=8',
);
$url = version_compare($os_version, '7.0.0', '>=') ? $urls['7'] : $urls['6'];

$msg = array(
    // �ѵ�¼
    'listed' => array(
        'title' => 'ԼԼ�����죬�����к���~',
        'desc' => '���û�������������ۡ��󣬼���תAppStore��Ӧҳ�棬���·�20Ԫ�Ż�ȯ���û���',
    ),
    // δ��¼
    'unlisted' => array(
        'title' => 'ԼԼ�����죬�����к���~',
        'desc' => 'Ϊ��ףԼԼ1���꣬����AppStoreΪԼԼ�ṩ���������ɻ��20Ԫ�Ż�ȯ��' . "\n\r" . '����δ��¼����½��������ۣ�������ȡ�Ż�ȯ',),
);

{
    $pop_data = array(
        'pop' => 0, // �Ƿ񵯴�
        'msg' => $msg,
        'url' => $url,
    );
}

$options['data'] = $pop_data;
return $cp->output($options);
