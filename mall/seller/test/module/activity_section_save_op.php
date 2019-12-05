<?php

/**
 * 异步保存活动场次
 *
 * 2015-11-3
 *
 * author  星星
 *
 */

include_once '../common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');

$user_id = $yue_login_id;

$ajax_status = 1;
if(empty($user_id))
{
    $ajax_status = 0;
}
//相应操作
if($ajax_status==1)
{


    //input获取不了两层，使用POST
    $data = poco_iconv_arr($_POST,'UTF-8','GBK');
    //做过滤处理
    foreach($data["prices_diy"] as $k => $v)
    {
        $data["prices_diy"][$k]["name"] = mall_remove_xss($v["name"]);
        $data["prices_diy"][$k]["time_s"] = mall_remove_xss($v["time_s"]);
        $data["prices_diy"][$k]["time_e"] = mall_remove_xss($v["time_e"]);
        $data["prices_diy"][$k]["stock_num"] = mall_remove_xss($v["stock_num"]);
        foreach($v["detail"] as $key => $value)
        {
            foreach($value as $k_v => $v_v)
            {
                $v["detail"][$key][$k_v] = mall_remove_xss($v_v);
            }
        }
        $data["prices_diy"][$k]["detail"] = $v["detail"];

    }

    foreach($data as $k => $v)
    {


        if(preg_match("/section_goods_/",$k))
        {
            $goods_id = mall_remove_xss($v);
        }

        if(preg_match("/section_action_/",$k))
        {
            $section_action = mall_remove_xss($v);
        }


    }

    /*print_r($data);
    echo $goods_id;
    echo $section_action;
    exit;*/
    //print_r($data);
    //echo $user_id;
    if($section_action=="edit")
    {
        $ret = $pai_mall_goods_obj->user_update_goods_prices_detail_for_42($goods_id,$data,$user_id);
    }
    else if($section_action=="add")
    {
        //去除添加内容多余的东西
        foreach($data as $k => $v)
        {

            if(preg_match("/section_goods_/",$k) || preg_match("/section_action_/",$k))
            {
                unset($data[$k]);
            }

        }

        //print_r($data);
        //echo $goods_id;
        //echo $user_id;

        $ret = $pai_mall_goods_obj->user_add_goods_prices_detail_for_42($goods_id,$data,$user_id);

        //print_r($ret);
        //exit();

    }

    //print_r($ret);
    if($ret["result"] > 0)
    {
        $ajax_status = 1;
        $msg = iconv('gbk//IGNORE','utf-8', $ret["message"]);

    }
    else
    {
        $ajax_status = 0;
        $msg = iconv('gbk//IGNORE','utf-8', $ret["message"]);
    }

}




$arr["ajax_status"] = $ajax_status;
$arr["msg"] = $msg;
echo json_encode($arr);
exit;

?>