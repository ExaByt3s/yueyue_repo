<?php


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$topic_enroll_obj = POCO::singleton ( 'pai_topic_enroll_class' );

$topic_id = (int)$_INPUT['topic_id'];

if(!$yue_login_id)
{
    js_pop_msg('���ȵ�¼');
}

if(!$topic_id)
{
    js_pop_msg('��������');
}

$check_enroll = $topic_enroll_obj->check_topic_enroll($topic_id,$yue_login_id);


if($check_enroll)
{
    js_pop_msg('�ѱ�������');
}
else
{
    $insert_data['topic_id'] = $topic_id;
    $insert_data['user_id'] = $yue_login_id;
    $insert_data['add_time'] = time();


    $topic_enroll_obj->add_topic_enroll($insert_data);


    js_pop_msg('�����ɹ�');
}





?>