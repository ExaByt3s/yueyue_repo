<?php

/**
 * ������������
 * 
 * @since 2015-7-17
 * @author chenweibiao <chenwb@yueus.com>
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');
//include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$location_id = $client_data['data']['param']['location_id'];
$user_id = $client_data['data']['param']['user_id'];
$type_id = intval($client_data['data']['param']['type_id']);   // �����ID
// 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ
if (in_array($type_id, array('3', '12','5'))) {
    $tags_arr = array(
        '����', '����', '����',
    );
} else {
    $tags_arr = array(
        '���', '����/�ȼ���', '����', '��ˮ', '����', '�պ�', 'ŷ��', '��װ', '���ո���', '����', '�Ա�', '����/��չ',
    );
}

$tags = array(
    'mid' => '122SE01001',
    'dmid' => 's001',
    'tag_num' => strval(count($tags)),
    'seller_tags' => array(),
    'service_tags' => array(),
);
$options['data'] = $tags;
return $cp->output($options);
