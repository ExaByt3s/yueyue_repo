<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$type           = $client_data['data']['param']['type'];

$pai_user_obj = POCO::singleton ( 'pai_user_class' );

switch($type)
{
    case 'date':

        if($pai_user_obj->check_role($user_id) == 'model')
        {
            $sql_str = "SELECT DISTINCTROW(from_date_id) AS id FROM `event_db`.`event_date_tbl` WHERE to_date_id = $user_id AND date_status = 'confirm'";
        }else{
            $sql_str = "SELECT DISTINCTROW(to_date_id) AS id FROM `event_db`.`event_date_tbl` WHERE from_date_id = $user_id AND date_status = 'confirm'";
        }
        $result = db_simple_getdata($sql_str, FALSE);    
    break;

    case 'follow':
        $sql_str = "SELECT DISTINCTROW(be_follow_user_id) AS id FROM pai_db.pai_user_follow_tbl WHERE follow_user_id = $user_id";
        $result = db_simple_getdata($sql_str, FALSE, 101);       
    break;
}

$data_array = array();
foreach($result AS $key=>$val)
{
    $data_array[$key]['id']			= $val['id'];
    $data_array[$key]['iconv']		= get_user_icon($val['id'], $size = 86); 
    //$data_array[$key]['nickname']	= iconv('gbk', 'utf8', get_user_nickname_by_user_id($val['id']));
    $data_array[$key]['nickname']	= get_user_nickname_by_user_id($val['id']);

        if($pai_user_obj->check_role($val['id']) == 'model')
        {
            $role_str = '模特';
        }else{
            $role_str = '摄影师';
        }
        $data_array[$key]['role']	= $role_str;


}     

//print_r($data_array);
$options['data'] = $data_array;

$cp->output($options);
?>