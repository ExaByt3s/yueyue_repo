<?php
/**
 * 获取作品列表，异步
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

$p = (int)$_INPUT["p"];
$goods_id = (int)$_INPUT["goods_id"];
$user_id = $yue_login_id;
$ajax_status = 1;

if($p=="" || empty($goods_id))
{
    $ajax_status = 0;
    $msg = iconv('gbk//IGNORE','utf-8',"参数有误");

}

if($ajax_status == 1)
{
    if(empty($p))
    {
        $p = 1;
    }
    $limit_count = 3;//测试，先设置三个
    $limit_start = ($p-1)*$limit_count;

    $limit_str = $limit_start.",".$limit_count;



    $article_arr_count = POCO::singleton('pai_mall_relate_opus_class')->get_opus_full_list(true,$goods_id,$limit_str,$order_by='add_time desc');
    $article_arr = POCO::singleton('pai_mall_relate_opus_class')->get_opus_full_list(false,$goods_id,$limit_str,$order_by='add_time desc');
    //根据当前页数跟数据总数判断是否有下一页
    $now_search_count = $p*$limit_count;
    if($now_search_count>=$article_arr_count)
    {
        $next_p = 0;
    }
    else
    {
        $next_p = $p+1;
    }




    foreach($article_arr as $key => $value)
    {
        $article_arr[$key]["title"] = iconv('gbk//IGNORE','utf-8', $value["title"]);
        $article_arr[$key]["nickname"] = iconv('gbk//IGNORE','utf-8', $value["nickname"]);
        if(!empty($user_id))
        {
            //判断是否有删除的权限
            if($value["user_id"]==$user_id)
            {
                $article_arr[$key]["del_op"] = 1;//删除功能操作显示
            }
            else
            {
                $article_arr[$key]["del_op"] = 0;//删除功能操作显示
            }
        }
        else
        {
            $article_arr[$key]["del_op"] = 0;//删除功能操作显示
        }

    }



}

//$ret是数组，参数为result跟msg

$arr["ajax_status"] = $ajax_status;
$arr["article_arr"] = $article_arr;
$arr["next_p"] = $next_p;//判断是否有下一页按钮
$arr["msg"] = $msg;
echo json_encode($arr);
exit;


?>