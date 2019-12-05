<?php
/**
 * 作品获取评价操作，异步
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


$goods_id = (int)$_INPUT["goods_id"];
$ajax_status = 1;

if(empty($goods_id))
{
    $ajax_status = 0;
    $msg = iconv('gbk//IGNORE','utf-8',"参数有误");

}


if($ajax_status==1)
{

    //初始化分页
    $page_obj =POCO::singleton('show_page');
    $show_count = 5;//测试，先设置三个


    $total_count = POCO::singleton('pai_mall_comment_class')->get_seller_comment_list_by_user_id_or_goods_id("",$goods_id,true, $order_by = 'comment_id DESC', $limit_str, $fields = '*');

    $page_obj->file = '';
    $page_obj->set($show_count, $total_count);
    $limit_str = $page_obj->limit();


    $comment_list = POCO::singleton('pai_mall_comment_class')->get_seller_comment_list_by_user_id_or_goods_id("",$goods_id,false, $order_by = 'comment_id DESC', $limit_str, $fields = '*');

    $star_len = 5;
    foreach($comment_list as $k => $v)
    {
        $star_tmp_len = $v["overall_score"];
        for($i=0;$i<$star_len;$i++)
        {
            if($i<$star_tmp_len)
            {
                $v["star_list"][$i]["html_class"] = "yellow";
            }
            else
            {
                $v["star_list"][$i]["html_class"] = "";
            }

        }
        $comment_list[$k] = $v;
        $comment_list[$k]["comment"] = iconv('gbk//IGNORE','utf-8',$v["comment"]);
        if($v["is_anonymous"]==1)//匿名评价
        {
            $comment_list[$k]["from_user_id"] = 0;
        }

        $comment_list[$k]["buyer_nickname"] = iconv('gbk//IGNORE','utf-8',$v["buyer_nickname"]);
        $comment_list[$k]["add_time"] = date("Y.m.d-H:i",$v["add_time"]);
    }


    $page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
    //异步处理分页
    $page_reg = '/href=(?:\"|)(.*)(\?|&)p=(\d+)(?:\"| )/isU';
    preg_match_all($page_reg,$page_select,$data_arr);
    $page_arr =	$data_arr[3];
    foreach( $page_arr as $key=>$val ){
        $page_select = str_replace($data_arr[0][$key],"href='javascript:void(0)' data-class=\"J_list_page_ajax\"  data-page=\"{$val}\"  ",$page_select);
    }
    $page_select = str_replace('color:red','color:#737373',$page_select);
    $page_select = str_replace('>上一页<', '>&lt;<', $page_select);
    $page_select = str_replace('>下一页<', '>&gt;<', $page_select);

}
if($yue_login_id==100004)
{
    //print_r($comment_list);
}

$arr["ajax_status"] = $ajax_status;
$arr["comment_list"] = $comment_list;
$arr["page_select"] = iconv("GBK","UTF-8",$page_select);
echo json_encode($arr);
exit;

?>