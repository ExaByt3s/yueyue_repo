<?php
/**
 * 作品删除操作，异步
 *
 * @author 星星
 * @version $Id$
 * @copyright , 2015-11-23
 * @package default
 */

/**
 * 初始化配置文件
 */
include_once 'config.php';

$opus_id = (int)$_INPUT["opus_id"];
$user_id = $yue_login_id;
$ajax_status = 1;

if (empty($opus_id) || empty($user_id))
{
    $ajax_status = 0;
    $msg = iconv('gbk//IGNORE','utf-8', "参数有误");
}

if($ajax_status == 1)
{
    $ret = POCO::singleton('pai_mall_relate_opus_class')->delete_opus($opus_id,$user_id);
    if($ret["result"]==1)
    {
        $ajax_status = 1;
    }
    else
    {
        $ajax_status = 0;
        $msg = iconv('gbk//IGNORE','utf-8', $ret["message"]);
    }

}

//$ret是数组，参数为result跟msg

$arr["ajax_status"] = $ajax_status;
$arr["msg"] = $msg;
echo json_encode($arr);
exit;


?>