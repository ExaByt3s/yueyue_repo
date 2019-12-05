<?php 
/**
* @file sync_wx_subcribe_list.php
* @synopsis 微信关注列表同步触发器
* @author wuhy@yueus.com
* @version null
* @date 2015-07-22
 */

ignore_user_abort(true);
set_time_limit(3600);//超时时间, 触发频率 应调整为合适的值, 避免服务器线程急剧加大
ini_set('memory_limit', '512M');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$bind_list = $weixin_helper_obj->get_bind_list_by_search('', false, '0,99999', 'bind_id DESC', 'bind_id');
$bind_id_arr = array_map('array_shift', $bind_list);

foreach( $bind_id_arr as $bind_id )
{
    $rst = $weixin_helper_obj->sync_subscribe_list($bind_id);
    echo $bind_id.' 微信关注列表同步结果： '. var_export($rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
}
