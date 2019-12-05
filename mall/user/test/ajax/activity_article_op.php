<?php
/**
 * 发作品操作，异步
 *
 * @author 星星
 * @version $Id$
 * @copyright , 2015-11-20
 * @package default
 */

/**
 * 初始化配置文件
 */
include_once 'config.php';

$goods_id = (int)$_INPUT["goods_id"];
$url = trim($_INPUT["url"]);
$user_id = $yue_login_id;
$ajax_status = 1;

if (empty($goods_id) || empty($user_id) || empty($url))
{
    $ajax_status = 0;
    $msg = iconv('gbk//IGNORE','utf-8', "参数有误");
}


if($ajax_status == 1)
{
    $data['user_id']=$user_id;
    $data['goods_id']=$goods_id;
    $data['url']=$url;
    $data['source']='poco';
    $ret=POCO::singleton('pai_mall_relate_opus_class')->add_opus($data);

    if($ret["result"]==1)
    {
        $ajax_status = 1;
        $msg = "success";
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