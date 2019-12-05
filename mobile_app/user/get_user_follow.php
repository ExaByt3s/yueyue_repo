<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/3/5
 * Time: 10:54
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$v_id           = $client_data['data']['param']['v_id'];
$limit          = $client_data['data']['param']['limit'];

$follow_obj = POCO::singleton ( 'pai_user_follow_class' );

$version_config = include('/disk/data/htdocs232/poco/pai/config/appstore_version_config.php');
$appstore_version = $version_config['version'];


if($v_id)
{
    $result = $follow_obj->get_user_follow_by_user_id($v_id, false, $limit);

        foreach($result AS $key=>$val)
        {
            $return_rs[$key]['follow_user_id'] = $val['be_follow_user_id'];
            $return_rs[$key]['nickname']        = $val['nickname'];
            $return_rs[$key]['user_icon']       = $val['user_icon'];
            $return_rs[$key]['role']            = $val['role'];
        }

}

if($appstore_version)
{
	if($client_data['data']['version'] == $appstore_version)
	{
		$return_rs = array();
	}
}

$options['data'] = $return_rs;
$cp->output($options);

?>

